<?php

namespace App\Http\Controllers;

use App\Models\FieldJob;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class UploadController extends Controller
{
    /**
     * Show upload form (no auth — via ticket_code URL)
     */
    public function showForm(string $ticketCode)
    {
        $job = FieldJob::where('ticket_code', $ticketCode)
            ->with(['user', 'kecamatan'])
            ->firstOrFail();

        // Enforce state transition: must be 'on_progress' (already claimed/assigned)
        if (in_array($job->status, ['pending', 'assigned'])) {
            return response("Tugas ini belum diambil. Silakan klik tombol 'Ambil Tugas' di Telegram terlebih dahulu untuk mulai mengerjakan.", 403);
        }

        return view('pages.public.upload', compact('job', 'ticketCode'));
    }

    /**
     * Process upload: validate both photos, watermark, save, notify admin
     */
    public function store(Request $request, string $ticketCode)
    {
        $job = FieldJob::where('ticket_code', $ticketCode)
            ->with('user')
            ->firstOrFail();
            
        // Enforce state transition on POST as well
        if (in_array($job->status, ['pending', 'assigned'])) {
            return back()->with('error', 'Silakan klik tombol "Ambil Tugas" di Telegram terlebih dahulu sebelum mengupload foto perbaikan.');
        }

        $request->validate([
            'photo_before'   => 'required|image|max:10240',
            'photo_after'    => 'required|image|max:10240',
            'estimated_time' => 'required|string|in:Kurang dari 30 Menit,30 Menit - 1 Jam,1 - 2 Jam,2 - 3 Jam,> 3 Jam',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
        ], [
            'photo_before.required'   => 'Foto SEBELUM wajib diisi.',
            'photo_after.required'    => 'Foto SESUDAH wajib diisi.',
            'photo_before.image'      => 'Foto SEBELUM harus berupa gambar.',
            'photo_after.image'       => 'Foto SESUDAH harus berupa gambar.',
            'estimated_time.required' => 'Estimasi waktu pengerjaan wajib dipilih.',
            'estimated_time.in'       => 'Pilihan estimasi waktu tidak valid.',
        ]);

        $teknisiName = $job->user ? $job->user->name : 'Petugas';
        $lat = $request->latitude;
        $lng = $request->longitude;
        $estimatedTime = $request->estimated_time;

        // ── Step 1: Save ORIGINAL photos ───────────────
        $dir = public_path('uploads/jobs');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $beforeOrigFile = 'original_before_' . $job->id . '_' . time() . '.' . $request->file('photo_before')->getClientOriginalExtension();
        $afterOrigFile  = 'original_after_' . $job->id . '_' . time() . '.' . $request->file('photo_after')->getClientOriginalExtension();

        $request->file('photo_before')->move($dir, $beforeOrigFile);
        $request->file('photo_after')->move($dir, $afterOrigFile);

        // ── Step 2: Create WATERMARKED copies (Fail-safe) ──────────
        $beforeWmFile = $beforeOrigFile; // Fallback to original
        $afterWmFile = $afterOrigFile;

        try {
            $beforeWmFile = $this->addWatermark(
                $dir . '/' . $beforeOrigFile,
                $job, $teknisiName, 'BEFORE', $lat, $lng
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Watermark BEFORE failed: ' . $e->getMessage());
        }

        try {
            $afterWmFile = $this->addWatermark(
                $dir . '/' . $afterOrigFile,
                $job, $teknisiName, 'AFTER', $lat, $lng
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Watermark AFTER failed: ' . $e->getMessage());
        }

        // ── Step 3: Update DB ──────────────────────────
        $updateData = [
            'photo_before'   => 'uploads/jobs/' . $beforeWmFile,
            'photo_after'    => 'uploads/jobs/' . $afterWmFile,
            'status'         => 'selesai',
            'finished_at'    => now(),
            'estimated_time' => $estimatedTime,
        ];

        if ($lat && $lng && !$job->latitude) {
            $updateData['latitude']  = $lat;
            $updateData['longitude'] = $lng;
        }

        $job->update($updateData);
        $job->refresh();

        // â”€â”€ Step 4: Notify admins via Telegram â”€â”€â”€â”€â”€â”€â”€â”€â”€
        try {
            $telegram = new TelegramService();
            $telegram->notifyJobCompleted($job);

            // Notify the technician that their upload was successful
            if ($job->user && $job->user->telegram_chat_id) {
                $telegram->sendMessage(
                    $job->user->telegram_chat_id,
                    "🔔 <b>Laporan berhasil terkirim, Terimakasih!</b>\n\nNomor Tiket: <code>{$job->ticket_code}</code>"
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Telegram notification error: ' . $e->getMessage());
        }

        // ── Step 5: Notify technician via WhatsApp ──────────
        try {
            if ($job->user && $job->user->phone) {
                $fonnte = new \App\Services\FonnteService();
                $waMsg = "✅ *LAPORAN BERHASIL DITERIMA*\n\n"
                       . "Terima kasih, {$teknisiName}! Laporan perbaikan tiket *{$job->ticket_code}* beserta bukti foto telah berhasil tersimpan di sistem.\n"
                       . "Pekerjaan Anda ini telah dicatat dan akan dievaluasi oleh Direksi.\n\n"
                       . "_Tetap utamakan keselamatan kerja_ ⚒️";

                $fonnte->sendMessage($job->user->phone, $waMsg);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Fonnte completion notification error: ' . $e->getMessage());
        }

        return back()->with('success', 'Foto berhasil diupload! Tiket telah ditandai selesai. ✅');
    }

    /**
     * Add watermark to a photo file
     * 3 lines: Tiket|Nama, Tanggal WITA, GPS
     * Returns the watermarked filename
     */
    protected function addWatermark(string $filePath, FieldJob $job, string $teknisiName, string $type, ?string $lat, ?string $lng): string
    {
        $image = Image::read($filePath);
        
        // Prevent OutOfMemory by scaling down huge photos before processing
        if ($image->width() > 1400) {
            $image->scaleDown(width: 1400);
        }

        $width  = $image->width();
        $height = $image->height();

        // Watermark text (3 lines)
        $timestamp = now()->format('d/m/Y H:i:s') . ' WITA';
        $gpsText = ($lat && $lng)
            ? "{$lat}, {$lng}"
            : ($job->latitude && $job->longitude ? "{$job->latitude}, {$job->longitude}" : '-');

        $kecName = $job->kecamatan ? $job->kecamatan->nama : '-';

        $lines = [
            "{$job->ticket_code} | {$teknisiName}",
            "Kec. {$kecName} | {$timestamp}",
            "GPS: {$gpsText}",
        ];

        // Calculate sizes (responsive to image width)
        $fontSize  = max(12, intval($width / 35));
        $padding   = intval($fontSize * 0.8);
        $lineHeight = intval($fontSize * 1.5);
        $boxHeight  = ($lineHeight * count($lines)) + ($padding * 2);

        // Draw semi-transparent black overlay at bottom
        $image->drawRectangle(
            0,
            $height - $boxHeight,
            function ($rectangle) use ($width, $height, $boxHeight) {
                $rectangle->size($width, $boxHeight); // Width, Height in v3
                $rectangle->background('rgba(0, 0, 0, 0.6)');
            }
        );

        // Draw text lines
        $y = $height - $boxHeight + $padding + intval($fontSize / 2); // v3 text alignment is often from middle/baseline
        foreach ($lines as $line) {
            $image->text($line, $padding, $y, function ($font) use ($fontSize) {
                $font->file(public_path('fonts/PlusJakartaSans.ttf'));
                $font->size($fontSize);
                $font->color('rgba(255, 255, 255, 0.95)');
                $font->align('left');
                $font->valign('top');
            });
            $y += $lineHeight;
        }

        // Save watermarked copy
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $wmFilename = 'watermarked_' . strtolower($type) . '_' . $job->id . '_' . time() . '.' . $ext;
        $image->save(dirname($filePath) . '/' . $wmFilename, quality: 85);

        return $wmFilename;
    }
}
