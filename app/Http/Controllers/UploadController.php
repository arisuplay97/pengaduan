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

        // Enforce state transition: must be claimed first
        if ($job->status === 'pending') {
            return response("Tugas ini belum diambil. Silakan klik tombol 'Ambil Tugas' di Telegram/WhatsApp terlebih dahulu untuk mulai mengerjakan.", 403);
        }

        return view('pages.public.upload', compact('job', 'ticketCode'));
    }

    /**
     * Process upload: validate both photos, watermark, save, notify admin
     */
    public function store(Request $request, string $ticketCode)
    {
        $job = FieldJob::where('ticket_code', $ticketCode)
            ->with(['user', 'kecamatan'])
            ->firstOrFail();
            
        if ($job->status === 'pending') {
            return back()->with('error', 'Silakan klik tombol "Ambil Tugas" di Telegram/WhatsApp terlebih dahulu.');
        }

        if ($job->status === 'selesai' || $job->status === 'ditutup') {
            return back()->with('error', 'Tugas ini sudah selesai.');
        }

        $teknisiName = $job->user ? $job->user->name : 'Petugas';
        $lat = $request->latitude;
        $lng = $request->longitude;
        $dir = public_path('uploads/jobs');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // ── CASE A: STEP 1 (Mulai Pengerjaan: Foto Sebelum + Estimasi) ──────────
        if (!$job->photo_before) {
            $request->validate([
                'photo_before'   => 'required|image|max:10240',
                'estimated_time' => 'required|string|in:Kurang dari 30 Menit,30 Menit - 1 Jam,1 - 2 Jam,2 - 3 Jam,> 3 Jam',
                'latitude'       => 'nullable|numeric',
                'longitude'      => 'nullable|numeric',
            ], [
                'photo_before.required'   => 'Foto SEBELUM wajib diunggah saat mulai pengerjaan.',
                'estimated_time.required' => 'Estimasi waktu wajib dipilih.',
            ]);

            $beforeFileName = 'original_before_' . $job->id . '_' . time() . '.' . $request->file('photo_before')->getClientOriginalExtension();
            $request->file('photo_before')->move($dir, $beforeFileName);

            // Watermark (Fail-safe)
            $wmFile = $beforeFileName;
            try {
                $wmFile = $this->addWatermark($dir . '/' . $beforeFileName, $job, $teknisiName, 'BEFORE', $lat, $lng);
            } catch (\Exception $e) { \Illuminate\Support\Facades\Log::error('Watermark B-Failed: ' . $e->getMessage()); }

            $job->update([
                'photo_before'   => 'uploads/jobs/' . $wmFile,
                'status'         => 'on_progress',
                'started_at'     => now(),
                'estimated_time' => $request->estimated_time,
                'latitude'       => $lat ?? $job->latitude,
                'longitude'      => $lng ?? $job->longitude,
            ]);

            return back()->with('success', 'Foto kedatangan berhasil diunggah! Status sekarang: SEDANG DIKERJAKAN. Silakan lanjutkan pengerjaan.');
        }

        // ── CASE B: STEP 2 (Selesaikan Pekerjaan: Foto Sesudah) ──────────
        $request->validate([
            'photo_after' => 'required|image|max:10240',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
        ], [
            'photo_after.required' => 'Foto SESUDAH wajib diunggah untuk menyelesaikan tugas.',
        ]);

        $afterFileName = 'original_after_' . $job->id . '_' . time() . '.' . $request->file('photo_after')->getClientOriginalExtension();
        $request->file('photo_after')->move($dir, $afterFileName);

        // Watermark (Fail-safe)
        $wmFile = $afterFileName;
        try {
            $wmFile = $this->addWatermark($dir . '/' . $afterFileName, $job, $teknisiName, 'AFTER', $lat, $lng);
        } catch (\Exception $e) { \Illuminate\Support\Facades\Log::error('Watermark A-Failed: ' . $e->getMessage()); }

        $job->update([
            'photo_after' => 'uploads/jobs/' . $wmFile,
            'status'      => 'selesai',
            'finished_at' => now(),
        ]);

        // Notify admins via Telegram
        try {
            $telegram = new TelegramService();
            $telegram->notifyJobCompleted($job);
            if ($job->user && $job->user->telegram_chat_id) {
                $telegram->sendMessage($job->user->telegram_chat_id, "🔔 <b>Laporan perbaikan selesai, Terimakasih!</b>\n\nTiket: <code>{$job->ticket_code}</code>");
            }
        } catch (\Exception $e) { \Illuminate\Support\Facades\Log::error('Telegram notification error: ' . $e->getMessage()); }

        // Notify technician via WhatsApp
        try {
            if ($job->user && $job->user->phone) {
                $fonnte = new \App\Services\FonnteService();
                $waMsg = "✅ *LAPORAN SELESAI DITERIMA*\n\nTerima kasih, {$teknisiName}! Laporan perbaikan tiket *{$job->ticket_code}* telah tersimpan.\nPekerjaan Anda telah dicatat oleh sistem. ⚒️";
                $fonnte->sendMessage($job->user->phone, $waMsg);
            }
        } catch (\Exception $e) { \Illuminate\Support\Facades\Log::error('Fonnte completion notification error: ' . $e->getMessage()); }

        return back()->with('success', 'Tiket telah berhasil diselesaikan! Terima kasih atas kerjasamanya. ✅');
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
