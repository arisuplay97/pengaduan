<?php

namespace App\Http\Controllers;

use App\Models\FieldJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkerController extends Controller
{
    /**
     * Magic link login for petugas
     */
    public function magicLogin($token)
    {
        $user = User::where('magic_token', $token)->where('role', 'petugas')->first();

        if (!$user) {
            return response()->view('errors.magic-link', [], 403);
        }

        Auth::login($user, true); // Force remember me
        
        // Rotate the magic token to prevent session hijacking (link becomes single-use)
        $user->forceFill([
            'magic_token' => \Illuminate\Support\Str::random(60)
        ])->save();

        return redirect()->route('worker.dashboard');
    }

    /**
     * Worker dashboard — mobile-first
     */
    public function dashboard()
    {
        $user = auth()->user();
        $jobs = FieldJob::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'on_progress'])
            ->orderByRaw("FIELD(status, 'on_progress', 'pending')")
            ->orderBy('created_at', 'desc')
            ->get();

        $doneToday = FieldJob::where('user_id', $user->id)
            ->where('status', 'selesai')
            ->whereDate('finished_at', today())
            ->count();

        return view('pages.worker', compact('jobs', 'doneToday'));
    }

    /**
     * Self-dispatch: petugas reports a new issue from the field
     */
    public function selfReport(Request $request)
    {
        $request->validate([
            'problem_type' => 'required|string',
            'photo_before' => 'required|image|max:5120',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Store photo
        $photoPath = null;
        if ($request->hasFile('photo_before')) {
            $file = $request->file('photo_before');
            $filename = 'before_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/jobs'), $filename);
            $photoPath = 'uploads/jobs/' . $filename;
        }

        // Create job with status working immediately
        FieldJob::create([
            'user_id' => auth()->id(),
            'title' => $request->problem_type,
            'address' => 'Lokasi GPS: ' . round($request->latitude, 5) . ', ' . round($request->longitude, 5),
            'description' => 'Laporan temuan mandiri oleh petugas.',
            'photo_before' => $photoPath,
            'problem_type' => $request->problem_type,
            'status' => 'on_progress',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'started_at' => now(),
        ]);

        return redirect()->route('worker.dashboard')->with('success', 'Laporan berhasil dikirim!');
    }

    /**
     * Start a job — receives GPS coords
     */
    public function startJob(Request $request, $id)
    {
        $job = FieldJob::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $job->update([
            'status' => 'on_progress',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'started_at' => now(),
        ]);

        return response()->json(['success' => true, 'job' => $job]);
    }

    /**
     * Finish a job — requires photo evidence
     */
    public function finishJob(Request $request, $id)
    {
        $job = FieldJob::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'on_progress')
            ->firstOrFail();

        $request->validate([
            'photo_after' => 'required|image|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo_after')) {
            $file = $request->file('photo_after');
            $filename = 'after_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/jobs'), $filename);
            $photoPath = 'uploads/jobs/' . $filename;
        }

        $job->update([
            'status' => 'selesai',
            'photo_after' => $photoPath,
            'finished_at' => now(),
        ]);

        return response()->json(['success' => true, 'job' => $job]);
    }

    /**
     * Claim a job via WhatsApp signed link and login automatically
     */
    public function claimViaWa(Request $request, $jobId, $workerId)
    {
        $worker = User::where('id', $workerId)->where('role', 'petugas')->firstOrFail();

        try {
            $result = \Illuminate\Support\Facades\DB::transaction(function () use ($jobId, $worker) {
                // Lock the row for update to prevent race conditions
                $job = FieldJob::where('id', $jobId)->lockForUpdate()->first();

                if (!$job) {
                    return ['success' => false, 'message' => 'Tugas tidak ditemukan.'];
                }

                // If job is already taken
                if ($job->user_id !== null || $job->status !== 'pending') {
                    if ($job->user_id === $worker->id) {
                         return ['success' => true, 'message' => 'Anda sudah mengambil tugas ini sebelumnya.', 'job' => $job];
                    }
                    return ['success' => false, 'message' => 'Maaf, tugas ini sudah selesai atau telah diambil oleh teknisi lain.'];
                }

                // Claim the job (Stage 0: Assigned to technician)
                $job->update([
                    'user_id'    => $worker->id,
                    'status'     => 'assigned',
                ]);

                return ['success' => true, 'message' => 'Tugas berhasil Anda ambil!', 'job' => $job];
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WA claim callback error: ' . $e->getMessage());
            return redirect()->route('public.landing')->with('error', 'Terjadi kesalahan sistem saat mengambil tugas.');
        }

        if ($result['success']) {
            $job = $result['job'];
            $uploadUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('upload.form', now()->addHours(12), ['ticketCode' => $job->ticket_code]);
            return redirect($uploadUrl)->with('success', $result['message']);
        } else {
            return redirect()->route('public.landing')->with('error', $result['message']);
        }
    }
}
