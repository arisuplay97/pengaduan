<?php

namespace App\Services;

use App\Models\FieldJob;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $token;
    protected string $baseUrl;

    public function __construct()
    {
        $dbToken = \App\Models\Setting::where('key', 'telegram_bot_token')->value('value');
        $this->token = $dbToken ?: config('services.telegram.bot_token');
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    // ═══════════════════════════════════════
    // CORE API METHODS
    // ═══════════════════════════════════════

    public function sendMessage(string $chatId, string $text, ?array $replyMarkup = null): array
    {
        $payload = [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ];
        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }
        return $this->post('sendMessage', $payload);
    }

    public function sendLocation(string $chatId, float $lat, float $lng): array
    {
        return $this->post('sendLocation', [
            'chat_id'   => $chatId,
            'latitude'  => $lat,
            'longitude' => $lng,
        ]);
    }

    public function answerCallbackQuery(string $callbackId, string $text, bool $showAlert = false): array
    {
        return $this->post('answerCallbackQuery', [
            'callback_query_id' => $callbackId,
            'text'              => $text,
            'show_alert'        => $showAlert,
        ]);
    }

    public function editMessageText(string $chatId, int $messageId, string $text): array
    {
        return $this->post('editMessageText', [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public function setWebhook(string $url): array
    {
        return $this->post('setWebhook', ['url' => $url]);
    }

    public function getWebhookInfo(): array
    {
        return $this->post('getWebhookInfo', []);
    }

    // ═══════════════════════════════════════
    // BROADCAST & ASSIGN LOGIC
    // ═══════════════════════════════════════

    /**
     * Broadcast a new job to technicians (Gojek/Grab model)
     *   - 0 teknisi  → notify admins
     *   - 1 teknisi  → auto-assign
     *   - >1 teknisi → broadcast with claim button
     */
    public function broadcastJob(FieldJob $job): void
    {
        $job->load('kecamatan');

        $teknisi = User::where('role', 'petugas')
            ->whereNotNull('telegram_chat_id')
            ->whereHas('kecamatans', function ($q) use ($job) {
                $q->where('kecamatans.id', $job->kecamatan_id);
            })
            ->get();

        $kecName = $job->kecamatan ? $job->kecamatan->nama : 'Tidak diketahui';

        if ($teknisi->count() === 0) {
            $this->notifyAdmins(
                "⚠️ <b>TIDAK ADA PETUGAS</b>\n"
                . "━━━━━━━━━━━━━━━━━━\n"
                . "📋 Tiket: <code>{$job->ticket_code}</code>\n"
                . "🏘️ Kecamatan: {$kecName}\n\n"
                . "Tidak ada petugas terdaftar di kecamatan ini."
            );
            return;
        }

        // === BROADCAST (Send to all eligible technicians) ===
        $message = $this->formatBroadcastMessage($job, $kecName);
        $replyMarkup = [
            'inline_keyboard' => [[
                [
                    'text'          => '✋ Ambil Tugas',
                    'callback_data' => 'claim_job_' . $job->id,
                ],
            ]],
        ];

        foreach ($teknisi as $petugas) {
            $this->sendMessage($petugas->telegram_chat_id, $message, $replyMarkup);
        }
    }

    // ═══════════════════════════════════════
    // MESSAGE FORMATS
    // ═══════════════════════════════════════

    /**
     * Format: Penugasan otomatis (1 teknisi)
     */
    protected function formatAssignmentMessage(FieldJob $job, string $kecName): string
    {
        $uploadUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('upload.form', now()->addHours(12), ['ticketCode' => $job->ticket_code]);
        $tanggal = now()->format('d/m/Y H.i') . ' WITA';

        return "🔧 <b>PENUGASAN BARU</b>\n\n"
            . "📋 No. Tiket  : <code>{$job->ticket_code}</code>\n"
            . "📅 Tanggal    : {$tanggal}\n"
            . "─────────────────────────────────\n"
            . ($job->reporter_name
                ? "👤 Pelapor    : {$job->reporter_name}\n"
                . ($job->customer_id ? "🔢 No. Pel.   : {$job->customer_id}\n" : '')
                . ($job->reporter_phone ? "📞 No. HP     : {$job->reporter_phone}\n" : '')
                . "─────────────────────────────────\n"
                : '')
            . "🏘️ Kecamatan  : Kec. {$kecName}\n"
            . "🔧 Jenis      : {$job->title}\n"
            . "─────────────────────────────────\n"
            . "📸 Upload foto disini:\n"
            . "👉 {$uploadUrl}";
    }

    /**
     * Format: Broadcast (>1 teknisi, siapa cepat dia dapat)
     */
    protected function formatBroadcastMessage(FieldJob $job, string $kecName): string
    {
        $tanggal = now()->format('d/m/Y H.i') . ' WITA';

        return "🔔 <b>PENUGASAN BARU</b>\n\n"
            . "📋 No. Tiket  : <code>{$job->ticket_code}</code>\n"
            . "📅 Tanggal    : {$tanggal}\n"
            . "─────────────────────────────────\n"
            . ($job->reporter_name
                ? "👤 Pelapor    : {$job->reporter_name}\n"
                . ($job->customer_id ? "🔢 No. Pel.   : {$job->customer_id}\n" : '')
                . ($job->reporter_phone ? "📞 No. HP     : {$job->reporter_phone}\n" : '')
                . "─────────────────────────────────\n"
                : '')
            . "🏘️ Kecamatan  : Kec. {$kecName}\n"
            . "🔧 Jenis      : {$job->title}\n"
            . "─────────────────────────────────\n"
            . "Tekan tombol di bawah untuk mengambil tugas ini.";
    }

    /**
     * Notify admins when a job is completed
     */
    public function notifyJobCompleted(FieldJob $job): void
    {
        $job->load(['user', 'kecamatan']);

        $teknisiName = $job->user ? $job->user->name : '-';
        $selesaiAt = $job->finished_at ? $job->finished_at->format('d/m/Y H.i') . ' WITA' : now()->format('d/m/Y H.i') . ' WITA';

        // Calculate duration
        $durasi = '-';
        if ($job->started_at && $job->finished_at) {
            $diff = $job->started_at->diff($job->finished_at);
            $parts = [];
            if ($diff->h > 0) $parts[] = "{$diff->h} jam";
            if ($diff->i > 0) $parts[] = "{$diff->i} menit";
            if (empty($parts)) $parts[] = "< 1 menit";
            $durasi = implode(' ', $parts);
        }

        $kecName = $job->kecamatan ? $job->kecamatan->nama : '-';

        $message = "✅ <b>LAPORAN SELESAI DIKERJAKAN</b>\n\n"
            . "📋 No. Tiket : <code>{$job->ticket_code}</code>\n"
            . "📍 Lokasi    : {$job->address}\n"
            . "🏘️ Kecamatan : Kec. {$kecName}\n"
            . "🔧 Jenis     : {$job->title}\n"
            . "👷 Teknisi   : {$teknisiName}\n"
            . "🕐 Selesai   : {$selesaiAt}\n"
            . "⏱️ Durasi    : {$durasi}\n\n"
            . "Silakan verifikasi dan tutup tiket.";

        $this->notifyAdmins($message);
    }

    /**
     * Send message to all admins with telegram_chat_id
     */
    public function notifyAdmins(string $message): void
    {
        $admins = User::whereIn('role', ['admin', 'superadmin'])
            ->whereNotNull('telegram_chat_id')
            ->where('telegram_chat_id', '!=', '')
            ->get();

        foreach ($admins as $admin) {
            $this->sendMessage($admin->telegram_chat_id, $message);
        }
    }

    // ═══════════════════════════════════════
    // HTTP
    // ═══════════════════════════════════════

    protected function post(string $method, array $data): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/{$method}", $data);
            $result = $response->json();

            if (!($result['ok'] ?? false)) {
                Log::warning("Telegram API error [{$method}]", $result ?? []);
            }

            return $result ?? ['ok' => false];
        } catch (\Exception $e) {
            Log::error("Telegram API exception [{$method}]: " . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
