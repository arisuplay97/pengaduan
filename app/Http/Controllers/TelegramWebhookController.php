<?php

namespace App\Http\Controllers;

use App\Models\FieldJob;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    protected TelegramService $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Handle incoming Telegram webhook
     */
    public function handle(Request $request)
    {
        $secretToken = config('services.telegram.webhook_secret');
        
        // If a secret is configured, ensure the incoming request bears it
        if (!empty($secretToken) && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $secretToken) {
            Log::warning('Unauthorized Telegram Webhook Attempt: Missing or invalid secret token.', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->all();

        Log::info('Telegram webhook received', $data);

        // Handle callback query (inline button clicks)
        if (isset($data['callback_query'])) {
            return $this->handleCallbackQuery($data['callback_query']);
        }

        // Handle regular messages (e.g. /start)
        if (isset($data['message']['text'])) {
            return $this->handleMessage($data['message']);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Handle callback query — "Ambil Tugas" button click
     */
    protected function handleCallbackQuery(array $callback): \Illuminate\Http\JsonResponse
    {
        $callbackId = $callback['id'];
        $chatId     = $callback['from']['id'] ?? null;
        $messageId  = $callback['message']['message_id'] ?? null;
        $cbData     = $callback['data'] ?? '';

        // Parse: claim_job_{id} or start_job_{id}
    if (str_starts_with($cbData, 'claim_job_')) {
        $jobId = (int) str_replace('claim_job_', '', $cbData);
        $isClaim = true;
    } elseif (str_starts_with($cbData, 'start_job_')) {
        $jobId = (int) str_replace('start_job_', '', $cbData);
        $isClaim = false;
    } else {
        $this->telegram->answerCallbackQuery($callbackId, '❌ Perintah tidak dikenali.');
        return response()->json(['ok' => true]);
    }

    // Find technician by telegram_chat_id
    $user = User::where('telegram_chat_id', (string) $chatId)
        ->where('role', 'petugas')
        ->first();

    if (!$user) {
        $this->telegram->answerCallbackQuery($callbackId, '❌ Akun Telegram Anda tidak terdaftar sebagai petugas.', true);
        return response()->json(['ok' => true]);
    }

    // Race condition handling with pessimistic lock
    try {
        $result = DB::transaction(function () use ($jobId, $user, $isClaim) {
            // Lock the row for update
            $job = FieldJob::where('id', $jobId)
                ->lockForUpdate()
                ->first();

            if (!$job) {
                return ['success' => false, 'reason' => 'not_found'];
            }

            if ($isClaim) {
                // Check if still claimable (user_id is null and status is pending)
                if ($job->user_id !== null || $job->status !== 'pending') {
                    return ['success' => false, 'reason' => 'already_claimed', 'job' => $job];
                }
                // Claim the job!
                $job->update([
                    'user_id'    => $user->id,
                    'status'     => 'on_progress',
                    'started_at' => now(),
                ]);
            } else {
                if ($job->user_id !== $user->id) {
                    return ['success' => false, 'reason' => 'not_owner'];
                }
                if ($job->status !== 'pending') {
                    return ['success' => false, 'reason' => 'already_started'];
                }
                $job->update([
                    'status'     => 'on_progress',
                    'started_at' => now(),
                ]);
            }

            return ['success' => true, 'job' => $job->fresh(), 'isClaim' => $isClaim];
        });
    } catch (\Exception $e) {
        Log::error('Telegram callback error: ' . $e->getMessage());
        $this->telegram->answerCallbackQuery($callbackId, '❌ Terjadi kesalahan server.', true);
        return response()->json(['ok' => true]);
    }

    if ($result['success']) {
        $job = $result['job'];
        
        if ($result['isClaim']) {
            $this->telegram->answerCallbackQuery($callbackId, '✅ Tugas berhasil diambil!');
            
            // Edit the original broadcast message
            if ($chatId && $messageId) {
                $this->telegram->editMessageText(
                    (string) $chatId,
                    $messageId,
                    "✅ <b>Tugas berhasil diambil!</b>\n\n"
                    . "🎫 Tiket: <code>{$job->ticket_code}</code>\n"
                    . "🔧 {$job->title}\n"
                    . "📮 {$job->address}\n\n"
                    . "Segera ke lokasi dan upload foto."
                );
            }
            // Send upload URL
            $uploadUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('upload.form', now()->addHours(12), ['ticketCode' => $job->ticket_code]);
            $this->telegram->sendMessage(
                (string) $chatId,
                "📸 <b>Upload Foto Before-After:</b>\n"
                . "Gunakan link berikut untuk upload bukti foto:\n\n"
                . "👉 {$uploadUrl}\n\n"
                . "<i>Link ini khusus untuk tiket {$job->ticket_code}. Tidak perlu login.</i>"
            );

            // Send location
            if ($job->latitude && $job->longitude) {
                $this->telegram->sendLocation((string) $chatId, (float)$job->latitude, (float)$job->longitude);
            }
        } else {
            $this->telegram->answerCallbackQuery($callbackId, '▶️ Tugas mulai dikerjakan!');
            if ($chatId && $messageId) {
                // Delete the reply markup button from the message to prevent multiple clicks
                $this->telegram->editMessageReplyMarkup(
                    (string) $chatId,
                    $messageId,
                    json_encode(['inline_keyboard' => []])
                );
            }
            // Send a confirming message
            $this->telegram->sendMessage(
                (string) $chatId,
                "▶️ <b>Proses Dimulai!</b>\n"
                . "Tiket: <code>{$job->ticket_code}</code>\n"
                . "Status: <b>On Progress</b>\n\n"
                . "Selamat bertugas! Jangan lupa upload foto jika sudah selesai."
            );
        }
    } else {
        $reason = $result['reason'] ?? 'error';
        if ($reason === 'already_claimed') {
            $this->telegram->answerCallbackQuery($callbackId, '❌ Maaf, tugas ini sudah diambil petugas lain.', true);
            if ($chatId && $messageId) {
                $this->telegram->editMessageText(
                    (string) $chatId,
                    $messageId,
                    "❌ <b>Tugas sudah diambil</b>\n\nTugas ini sudah diklaim oleh petugas lain."
                );
            }
        } elseif ($reason === 'not_owner') {
            $this->telegram->answerCallbackQuery($callbackId, '❌ Anda tidak memiliki akses ke tugas ini.', true);
        } elseif ($reason === 'already_started') {
            $this->telegram->answerCallbackQuery($callbackId, '⚠️ Laporan ini sudah tidak berstatus pending.', true);
            if ($chatId && $messageId) {
                // Remove the button
                $this->telegram->editMessageReplyMarkup(
                    (string) $chatId,
                    $messageId,
                    json_encode(['inline_keyboard' => []])
                );
            }
        } else {
            $this->telegram->answerCallbackQuery($callbackId, '❌ Tugas tidak ditemukan.', true);
        }
    }

        return response()->json(['ok' => true]);
    }

    /**
     * Handle regular text messages (e.g. /start, /info)
     */
    protected function handleMessage(array $message): \Illuminate\Http\JsonResponse
    {
        $chatId = $message['from']['id'] ?? null;
        $text   = trim($message['text'] ?? '');

        if (!$chatId) {
            return response()->json(['ok' => true]);
        }

        if ($text === '/start') {
            $user = User::where('telegram_chat_id', (string) $chatId)->first();

            if ($user) {
                $this->telegram->sendMessage((string) $chatId,
                    "👋 Halo <b>{$user->name}</b>!\n\n"
                    . "🤖 Saya adalah <b>Tiara Smart Assistant</b>.\n"
                    . "Anda akan menerima notifikasi tugas gangguan melalui bot ini.\n\n"
                    . "Status: ✅ Terdaftar sebagai <b>" . ucfirst($user->role) . "</b>"
                );
            } else {
                $this->telegram->sendMessage((string) $chatId,
                    "👋 Halo!\n\n"
                    . "🤖 Saya adalah <b>Tiara Smart Assistant</b>.\n\n"
                    . "⚠️ Telegram ID Anda (<code>{$chatId}</code>) belum terdaftar.\n"
                    . "Hubungi Admin untuk mendaftarkan akun Anda."
                );
            }
        } elseif ($text === '/id') {
            $this->telegram->sendMessage((string) $chatId,
                "🆔 Telegram Chat ID Anda:\n<code>{$chatId}</code>"
            );
        } elseif ($text === '/bantuan') {
            $this->telegram->sendMessage((string) $chatId,
                "ℹ️ <b>Pusat Bantuan Tiara Smart Assistant</b>\n\n"
                . "Perintah yang tersedia:\n"
                . "▪️ /start - Memulai bot & cek status login\n"
                . "▪️ /id - Menampilkan Telegram Chat ID Anda\n"
                . "▪️ /status - Lihat tugas aktifmu\n"
                . "▪️ /bantuan - Menampilkan pesan ini\n\n"
                . "<b>Cara Menggunakan Bot:</b>\n"
                . "1️⃣ Anda akan menerima pesan setiap ada <b>PENUGASAN BARU</b>.\n"
                . "2️⃣ Klik tombol <b>Ambil Tugas</b> untuk mengklaim tiket tersebut.\n"
                . "3️⃣ Setelah mendapat detail lokasi, segera kerjakan.\n"
                . "4️⃣ Klik link <b>Upload Foto Before-After</b> yang diberikan bot untuk menyelesaikan tiket.\n\n"
                . "Jika ada kendala teknis, silakan hubungi Administrator sistem."
            );
        } else {
            // Fallback for random messages
            $this->telegram->sendMessage((string) $chatId,
                "Maaf, saya tidak mengerti pesan Anda.\n\n"
                . "Perintah yang tersedia:\n"
                . "/bantuan  → lihat semua perintah\n"
                . "/id       → cek ID Telegram kamu\n"
                . "/status   → lihat tugas aktifmu\n\n"
                . "Atau tunggu notifikasi tugas baru\ndari Admin. 🔔"
            );
        }

        return response()->json(['ok' => true]);
    }
}
