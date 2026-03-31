<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;

    public function __construct()
    {
        // Get token from settings database
        $token = \App\Models\Setting::where('key', 'fonnte_token')->value('value');
        $this->token = $token ?? ''; // fallback to empty string if not set
    }

    /**
     * Send a WhatsApp message via Fonnte
     *
     * @param string $target Phone number (multiple numbers separated by comma)
     * @param string $message The WA message to send
     * @return array
     */
    public function sendMessage(string $target, string $message): array
    {
        Log::info("Fonnte: Preparing to send message to $target");
        // Fonnte expects numbers without '+', usually 08 or 62 format
        // Clean up the numbers
        $targets = explode(',', $target);
        $cleanTargets = [];
        foreach ($targets as $t) {
            $t = preg_replace('/[^0-9]/', '', $t);
            if (substr($t, 0, 1) === '0') {
                $t = '62' . substr($t, 1);
            }
            if ($t) {
                $cleanTargets[] = $t;
            }
        }
        $targetStr = implode(',', $cleanTargets);

        if (empty($targetStr)) {
            Log::warning('FonnteService: No valid target phone numbers provided.');
            return ['status' => false, 'message' => 'No valid targets'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token
            ])->post('https://api.fonnte.com/send', [
                'target' => $targetStr,
                'message' => $message,
                'countryCode' => '62'
            ]);

            $result = $response->json();
            Log::info("Fonnte API Response: " . json_encode($result));
            
            if (!$response->successful() || (isset($result['status']) && $result['status'] === false)) {
                Log::error('Fonnte API Error: ' . json_encode($result));
            }

            return ['status' => $response->successful(), 'response' => $result];
        } catch (\Exception $e) {
            Log::error('Fonnte HTTP Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Broadcast a new job to all technicians in the relevant kecamatan via WhatsApp
     */
    public function broadcastJob(\App\Models\FieldJob $job): void
    {
        Log::info("Fonnte: Starting broadcast for job " . $job->id);
        // Require eager loading
        $job->loadMissing('kecamatan');
        $kecName = $job->kecamatan ? $job->kecamatan->nama : 'Lainnya';

        // Find eligible technicians: role 'petugas', linked to this kecamatan, have a valid phone number
        $teknisi = \App\Models\User::where('role', 'petugas')
            ->whereNotNull('phone')
            ->whereHas('kecamatans', function ($q) use ($job) {
                // if job has no kecamatan, it might be tricky, but assuming it usually does
                if ($job->kecamatan_id) {
                    $q->where('kecamatan_id', $job->kecamatan_id);
                }
            })
            ->get();

        if ($teknisi->count() === 0) {
            Log::info("Fonnte broadcast skipped: No valid phone numbers for technicians in Kec. {$kecName}");
            return;
        }

        $tanggal = now()->format('d/m/Y H.i') . ' WITA';

        foreach ($teknisi as $petugas) {
            // Generate single-use expiring signed URL for this specific technician
            $claimUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'worker.claim_wa',
                now()->addHours(2),
                ['job' => $job->id, 'worker' => $petugas->id]
            );

            $message = "🔔 *PENUGASAN BARU*\n\n"
                . "📋 No. Tiket: {$job->ticket_code}\n"
                . "📅 Tanggal: {$tanggal}\n"
                . "────────────────\n";

            if ($job->reporter_name) {
                $message .= "👤 Pelapor: {$job->reporter_name}\n"
                         . ($job->customer_id ? "🔢 No. Pel: {$job->customer_id}\n" : '')
                         . ($job->reporter_phone ? "📞 No. HP: {$job->reporter_phone}\n" : '')
                         . "────────────────\n";
            }

            $message .= "🏘️ Kecamatan: Kec. {$kecName}\n"
                . "🔧 Jenis: {$job->title}\n"
                . "────────────────\n"
                . "Tekan link di bawah ini untuk mengambil tugas ini:\n👉 {$claimUrl}\n\n"
                . "_Link keamanan ini hanya berlaku selama 2 jam._";

            // Send WA message independently per technician
            $this->sendMessage($petugas->phone, $message);
        }
    }
}
