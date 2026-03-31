<?php

namespace App\Http\Controllers;

use App\Models\FieldJob;
use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\TelegramService;

class JobController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    /**
     * Worker dashboard
     */
    public function workerDashboard()
    {
        $user = auth()->user();
        
        // Redirect non-petugas
        if ($user->role !== 'petugas') {
            return redirect()->route('command-center');
        }

        // Active jobs: own jobs + unassigned public reports (visible to all petugas)
        $jobs = FieldJob::where(function($q) use ($user) {
                $q->where('user_id', $user->id)  // own assigned jobs
                  ->orWhereNull('user_id');        // + unassigned public reports
            })
            ->where(function($q) {
                $q->whereDate('created_at', today())
                  ->orWhere('status', 'on_progress')
                  ->orWhere('status', 'pending');
            })
            ->orderByRaw("FIELD(status, 'pending', 'assigned', 'on_progress', 'selesai', 'ditutup')")
            ->orderBy('created_at', 'desc')
            ->with('kecamatan')
            ->get();

        // History: only DONE jobs from past days (last 30 days)
        $history = FieldJob::where('user_id', $user->id)
            ->where('status', 'selesai')
            ->whereDate('created_at', '<', today())
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->with('kecamatan')
            ->get();

        $workingCount = $jobs->where('status', 'on_progress')->count();
        $doneCount = $jobs->where('status', 'selesai')->count();

        $kecamatans = Kecamatan::orderBy('nama')->get();

        return view('pages.worker', compact('jobs', 'history', 'workingCount', 'doneCount', 'kecamatans'));
    }

    /**
     * startJob() — Worker starts a pending job (with photo before)
     */
    public function startJob(Request $request, $id)
    {
        $job = FieldJob::where(function($q) {
            $q->where('user_id', auth()->id())->orWhereNull('user_id');
        })->findOrFail($id);

        if ($job->status !== 'pending') {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Job sudah tidak pending.']);
            return back()->with('error', 'Job ini sudah tidak pending.');
        }

        $photoPath = $job->photo_before;
        if ($request->hasFile('photo_before')) {
            $file = $request->file('photo_before');
            $filename = 'before_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/jobs'), $filename);
            $photoPath = 'uploads/jobs/' . $filename;
        }

        $job->update([
            'user_id' => auth()->id(),  // auto-assign to whoever starts it
            'status' => 'on_progress',
            'started_at' => now(),
            'photo_before' => $photoPath,
        ]);

        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Mulai mengerjakan laporan #' . $id);
    }

    /**
     * store() — Petugas creates a self-dispatch job
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'photo_before' => 'required|image|max:5120',
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo_before')) {
            $file = $request->file('photo_before');
            $filename = 'before_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/jobs'), $filename);
            $photoPath = 'uploads/jobs/' . $filename;
        }

        FieldJob::create([
            'user_id'      => auth()->id(),
            'title'        => $request->title,
            'address'      => 'GPS: ' . round($request->latitude, 5) . ', ' . round($request->longitude, 5),
            'kecamatan_id' => $request->kecamatan_id,
            'description'  => $request->description,
            'photo_before' => $photoPath,
            'problem_type' => $request->title,
            'status'       => 'on_progress',
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'started_at'   => now(),
        ]);

        return redirect()->route('worker.dashboard')->with('success', 'Laporan berhasil dikirim! 🎉');
    }

    /**
     * update() — Finish a job with photo evidence
     */
    public function update(Request $request, $id)
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
            'status'      => 'selesai',
            'photo_after'  => $photoPath,
            'finished_at'  => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * updateProfile() - Update field agent profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $user->name = $request->name;

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            
            // Store new (using 'public' disk)
            $path = $request->file('photo')->store('photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        return response()->json([
            'success' => true, 
            'message' => 'Profil berhasil diperbarui!',
            'photo_url' => $user->photo ? asset('storage/' . $user->photo) : null
        ]);
    }

    /**
     * destroy() — Cancel/delete a working or done job (only own)
     */
    public function destroy($id)
    {
        $job = FieldJob::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['on_progress', 'selesai'])
            ->firstOrFail();

        // Delete photo files if exist
        if ($job->photo_before && file_exists(public_path($job->photo_before))) {
            unlink(public_path($job->photo_before));
        }
        if ($job->photo_after && file_exists(public_path($job->photo_after))) {
            unlink(public_path($job->photo_after));
        }

        $job->delete();

        return response()->json(['success' => true]);
    }

    /**
     * updateReport() - Edit title/photo of an existing report
     */
    public function updateReport(Request $request, $id)
    {
        $job = FieldJob::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'selesai')
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'photo_before' => 'nullable|image|max:2048',
        ]);

        $job->title = $request->title;
        // Map problem_type if needed based on title
        $job->problem_type = \Illuminate\Support\Str::contains($request->title, 'Pipa') ? 'Pipa Bocor' : 
                            (\Illuminate\Support\Str::contains($request->title, 'Meteran') ? 'Meteran Bermasalah' : 'Lainnya');

        if ($request->hasFile('photo_before')) {
            // Delete old photo
            if ($job->photo_before && file_exists(public_path($job->photo_before))) {
                unlink(public_path($job->photo_before));
            }
            
            // Store new photo
            $file = $request->file('photo_before');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/jobs'), $filename);
            
            $job->photo_before = 'uploads/jobs/' . $filename;
        }

        $job->save();

        return redirect()->route('worker.dashboard')->with('success', 'Laporan berhasil diperbarui!');
    }

    /**
     * Command Center — Director/Admin monitoring view
     */
    public function commandCenter()
    {
        return view('pages.command-center');
    }

    /**
     * getMapData() — JSON API for map with date filter
     */
    /**
     * getMapData() — JSON API for map
     * Logic:
     * 1. Ambil SEMUA job status 'on_progress' (tanpa filter tanggal)
     * 2. Ambil job status 'selesai' SESUAI filter tanggal (default hari ini)
     */
    public function getMapData(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));

        // Query 1: All working jobs
        $working = FieldJob::with('user:id,name')
            ->where('status', 'on_progress')
            ->get();

        // Query 2: Done jobs filtered by chosen date
        $done = FieldJob::with('user:id,name')
            ->where('status', 'selesai')
            ->whereDate('finished_at', $date)
            ->get();

        // Merge and sort by newest
        $jobs = $working->merge($done)->sortByDesc('created_at')->values();

        $mapped = $jobs->map(function ($job) {
            $end = $job->finished_at ?? now();
            $durasi = $job->started_at ? $this->durasiIndo($job->started_at, $end) : '-';

            return [
                'id'           => $job->id,
                'title'        => $job->title,
                'address'      => $job->address,
                'description'  => $job->description,
                'status'       => $job->status,
                'latitude'     => $job->latitude,
                'longitude'    => $job->longitude,
                'photo_before' => $job->photo_before ? asset($job->photo_before) : null,
                'photo_after'  => $job->photo_after ? asset($job->photo_after) : null,
                'problem_type' => $job->problem_type,
                'user_name'    => $job->user ? $job->user->name : '-',
                'started_at'   => $job->started_at ? $job->started_at->format('H:i') : null,
                'finished_at'  => $job->finished_at ? $job->finished_at->format('H:i') : null,
                'duration'     => $durasi,
                'created_at'   => $job->created_at->format('H:i'),
            ];
        });

        return response()->json($mapped);
    }

    /**
     * Format duration in Indonesian: "15 menit", "1 jam 30 menit", "2 hari"
     */
    private function durasiIndo($start, $end)
    {
        $diff = $start->diff($end);
        $parts = [];
        if ($diff->d > 0) $parts[] = $diff->d . ' hari';
        if ($diff->h > 0) $parts[] = $diff->h . ' jam';
        if ($diff->i > 0) $parts[] = $diff->i . ' menit';
        return count($parts) > 0 ? implode(' ', $parts) : 'baru saja';
    }

    // ═════════════════════════════════════════════════════════
    //  ASSIGNMENT MANAGEMENT (Admin)
    // ═════════════════════════════════════════════════════════

    /**
     * Display assignment management page with all jobs
     */
    public function assignmentIndex(Request $request)
    {
        $query = FieldJob::with(['user', 'kecamatan']);

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['pending', 'on_progress', 'selesai'])) {
            $query->where('status', $request->status);
        }

        // Filter by kecamatan
        if ($request->filled('kecamatan_id')) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('address', 'like', "%{$s}%")
                  ->orWhere('id', $s);
            });
        }

        $jobs = $query->orderByRaw("FIELD(status, 'pending', 'assigned', 'on_progress', 'selesai', 'ditutup')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $counts = [
            'pending' => FieldJob::where('status', 'pending')->count(),
            'on_progress' => FieldJob::where('status', 'on_progress')->count(),
            'selesai'    => FieldJob::where('status', 'selesai')->count(),
            'total'   => FieldJob::count(),
        ];

        $kecamatans = Kecamatan::orderBy('nama')->get();
        $workers = User::where('role', 'petugas')->orderBy('name')->get();

        return view('pages.assignment', compact('jobs', 'counts', 'kecamatans', 'workers'));
    }

    /**
     * Update job status: pending → working → done
     */
    public function updateAssignmentStatus(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:start,finish',
        ]);

        $job = FieldJob::findOrFail($id);

        if ($request->action === 'start' && $job->status === 'pending') {
            $job->update([
                'status' => 'on_progress',
                'started_at' => now(),
            ]);
            return back()->with('success', "Laporan #{$id} mulai dikerjakan!");
        }

        if ($request->action === 'finish' && $job->status === 'on_progress') {
            $job->update([
                'status' => 'selesai',
                'finished_at' => now(),
            ]);
            return back()->with('success', "Laporan #{$id} telah diselesaikan!");
        }

        return back()->with('error', 'Aksi tidak valid untuk status saat ini.');
    }

    /**
     * destroyAssignment() - Superadmin deletes a report
     */
    public function destroyAssignment($id)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Akses ditolak. Fitur ini khusus superadmin.');
        }
        
        $job = FieldJob::findOrFail($id);

        if ($job->photo_before && file_exists(public_path($job->photo_before))) {
            unlink(public_path($job->photo_before));
        }
        if ($job->photo_after && file_exists(public_path($job->photo_after))) {
            unlink(public_path($job->photo_after));
        }

        $job->delete();

        return back()->with('success', 'Laporan berhasil dihapus secara permanen.');
    }

    /**
     * exportPdf() - Download single report as PDF
     */
    public function exportPdf($id)
    {
        $job = FieldJob::with(['user', 'kecamatan'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.pdf.report', compact('job'));
        return $pdf->download('Laporan_' . $job->ticket_code . '.pdf');
    }

    /**
     * exportExcel() - Download single report as Excel
     */
    public function exportExcel($id)
    {
        $job = FieldJob::with(['user', 'kecamatan'])->findOrFail($id);

        return response()->streamDownload(function () use ($job) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="UTF-8"></head><body>';
            echo '<table border="1">';
            echo '<tr><th colspan="2" style="background-color: #0284c7; color: white;">Detail Laporan Gangguan - ' . $job->ticket_code . '</th></tr>';
            echo '<tr><td><b>Nomor Tiket</b></td><td>' . $job->ticket_code . '</td></tr>';
            echo '<tr><td><b>Tanggal Lapor</b></td><td>' . $job->created_at->format('d/m/Y H:i') . '</td></tr>';
            echo '<tr><td><b>Tanggal Selesai</b></td><td>' . ($job->finished_at ? $job->finished_at->format('d/m/Y H:i') : '-') . '</td></tr>';
            echo '<tr><td><b>Status</b></td><td>' . strtoupper($job->status) . '</td></tr>';
            echo '<tr><td><b>Kecamatan</b></td><td>' . ($job->kecamatan ? $job->kecamatan->nama : '-') . '</td></tr>';
            echo '<tr><td><b>Alamat Lengkap</b></td><td>' . htmlspecialchars($job->address) . '</td></tr>';
            echo '<tr><td><b>Nama Pelapor</b></td><td>' . htmlspecialchars($job->reporter_name ?? '-') . '</td></tr>';
            echo '<tr><td><b>No Telepon Pelapor</b></td><td>' . htmlspecialchars($job->reporter_phone ?? '-') . '</td></tr>';
            echo '<tr><td><b>Keterangan</b></td><td>' . htmlspecialchars($job->description ?? '-') . '</td></tr>';
            echo '<tr><td><b>Petugas Lapangan</b></td><td>' . ($job->user ? $job->user->name : '-') . '</td></tr>';
            echo '</table></body></html>';
        }, 'Laporan_' . $job->ticket_code . '.xls', [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    /**
     * Assign a worker to a job
     */
    public function assignWorker(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $job = FieldJob::findOrFail($id);
        $worker = User::findOrFail($request->user_id);

        $job->update(['user_id' => $request->user_id]);

        return back()->with('success', "Laporan #{$id} ditugaskan ke {$worker->name}!");
    }

    // ═════════════════════════════════════════════════════════
    //  DISPATCH (Admin Lapang — Field Supervisor)
    // ═════════════════════════════════════════════════════════

    /**
     * Dispatch index — shows jobs created/dispatched by admin lapang
     */
    public function dispatchIndex(Request $request)
    {
        $user = auth()->user();

        // Only allow adminlapang
        if ($user->username !== 'adminlapang') {
            return redirect()->route('worker.dashboard');
        }

        $query = FieldJob::with(['user', 'kecamatan']);

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['pending', 'on_progress', 'selesai'])) {
            $query->where('status', $request->status);
        }

        // Filter by petugas
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $jobs = $query->orderByRaw("FIELD(status, 'pending', 'assigned', 'on_progress', 'selesai', 'ditutup')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $counts = [
            'pending' => FieldJob::where('status', 'pending')->count(),
            'on_progress' => FieldJob::where('status', 'on_progress')->count(),
            'selesai'    => FieldJob::where('status', 'selesai')->count(),
            'total'   => FieldJob::count(),
        ];

        // Get all petugas except adminlapang for assignment dropdown
        $petugasList = User::where('role', 'petugas')
            ->where('username', '!=', 'adminlapang')
            ->orderBy('name')
            ->get();

        $kecamatans = Kecamatan::orderBy('nama')->get();

        return view('pages.dispatch', compact('jobs', 'counts', 'petugasList', 'kecamatans'));
    }

    /**
     * Dispatch store — admin lapang creates a job assigned to a petugas (status: pending)
     */
    public function dispatchStore(Request $request)
    {
        $user = auth()->user();
        // Allow admin, superadmin, or adminlapang to use this feature
        if (!in_array($user->role, ['admin', 'superadmin']) && $user->username !== 'adminlapang') {
            return redirect()->route('worker.dashboard');
        }

        $request->validate([
            'title'          => 'required|string|max:255',
            'address'        => 'required|string|max:500',
            'user_id'        => 'required', // can be 'BROADCAST' or numeric ID
            'kecamatan_id'   => 'nullable|exists:kecamatans,id',
            'description'    => 'nullable|string',
            'reporter_name'  => 'nullable|string|max:255',
            'reporter_phone' => 'nullable|string|max:20',
            'customer_id'    => 'nullable|string|max:50',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
        ]);

        $jobData = [
            'user_id'        => $request->user_id === 'BROADCAST' ? null : $request->user_id,
            'title'          => $request->title,
            'address'        => $request->address,
            'kecamatan_id'   => $request->kecamatan_id,
            'description'    => $request->description,
            'problem_type'   => $request->title,
            'status'         => 'pending',
            'latitude'       => $request->latitude ?? 0,
            'longitude'      => $request->longitude ?? 0,
            'reporter_name'  => $request->reporter_name,
            'reporter_phone' => $request->reporter_phone,
            'customer_id'    => $request->customer_id,
        ];

        $job = FieldJob::create($jobData);

        // Telegram Notification Logic
        if ($request->user_id === 'BROADCAST') {
            $this->telegramService->broadcastJob($job);
            return back()->with('success', "Laporan berhasil dibuat dan di-broadcast ke semua petugas di kecamatan tersebut!");
        } else {
            $petugas = User::find($request->user_id);
            if ($petugas && $petugas->telegram_chat_id) {
                // Determine format
                $kecName = $job->kecamatan ? $job->kecamatan->nama : 'Tidak diketahui';
                $tanggal = now()->format('d/m/Y H.i') . ' WITA';
                $uploadUrl = url("/upload/{$job->ticket_code}");

                $msg = "🔧 <b>PENUGASAN BARU (DIRECT)</b>\n\n"
                    . "📋 No. Tiket  : <code>{$job->ticket_code}</code>\n"
                    . "📅 Tanggal    : {$tanggal}\n"
                    . "─────────────────────────────────\n";
                    
                if ($job->reporter_name) {
                    $msg .= "👤 Pelapor    : {$job->reporter_name}\n";
                    if ($job->customer_id) $msg .= "🔢 No. Pel.   : {$job->customer_id}\n";
                    if ($job->reporter_phone) $msg .= "📞 No. HP     : {$job->reporter_phone}\n";
                    $msg .= "─────────────────────────────────\n";
                }

                $msg .= "🏘️ Kecamatan  : Kec. {$kecName}\n"
                . "🔧 Jenis      : {$job->title}\n"
                . "📍 Alamat     : {$job->address}\n"
                . "─────────────────────────────────\n"
                . "Silakan klik tombol di bawah untuk mulai mengerjakan.\n"
                . "Atau upload foto disini:\n"
                . "👉 {$uploadUrl}";

            $replyMarkup = [
                'inline_keyboard' => [[
                    [
                        'text'          => '▶️ Mulai Kerjakan',
                        'callback_data' => 'start_job_' . $job->id,
                    ],
                ]],
            ];

            $this->telegramService->sendMessage($petugas->telegram_chat_id, $msg, $replyMarkup);
        }
            return back()->with('success', "Perintah kerja berhasil dibuat dan ditugaskan ke {$petugas->name}!");
        }
    }

    /**
     * Dispatch update status — admin lapang can also start/finish jobs
     */
    public function dispatchUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:start,finish',
        ]);

        $job = FieldJob::findOrFail($id);

        if ($request->action === 'start' && $job->status === 'pending') {
            $job->update(['status' => 'on_progress', 'started_at' => now()]);
            return back()->with('success', "Laporan #{$id} mulai dikerjakan!");
        }

        if ($request->action === 'finish' && $job->status === 'on_progress') {
            $job->update(['status' => 'selesai', 'finished_at' => now()]);
            return back()->with('success', "Laporan #{$id} telah diselesaikan!");
        }

        return back()->with('error', 'Aksi tidak valid.');
    }
}
