<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "-> Mengambil URL Ngrok publik terbaru...\n";

// Fetch the tunnels from local ngrok API
$ch = curl_init('http://127.0.0.1:4040/api/tunnels');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Very short timeout since it's local
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    die("❌ Gagal menghubungi Ngrok. Pastikan Ngrok sudah berjalan.\n");
}

$data = json_decode($response, true);
$publicUrl = null;

if (isset($data['tunnels'])) {
    foreach ($data['tunnels'] as $tunnel) {
        if ($tunnel['proto'] === 'https') {
            $publicUrl = $tunnel['public_url'];
            break;
        }
    }
}

if (!$publicUrl) {
    die("❌ Tidak dapat menemukan HTTPS URL dari Ngrok.\n");
}

echo "✅ Berhasil mendeteksi URL: {$publicUrl}\n";

// Update APP_URL in .env so signed routes generate correct domain
$envFile = __DIR__ . '/.env';
$envContent = file_get_contents($envFile);
$envContent = preg_replace('/^APP_URL=.*$/m', 'APP_URL=' . $publicUrl, $envContent);
file_put_contents($envFile, $envContent);
echo "✅ Sistem web (APP_URL) telah diperbarui ke URL baru.\n";

// Register Webhook to Telegram
echo "-> Menghubungkan bot Telegram ke URL baru...\n";
$botToken = config('services.telegram.bot_token');
$webhookSecret = config('services.telegram.webhook_secret');

$res = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
    'url'          => "{$publicUrl}/api/telegram/webhook",
    'secret_token' => $webhookSecret,
]);

$tgResult = $res->json();
if (isset($tgResult['ok']) && $tgResult['ok'] === true) {
    echo "✅ Telegram Webhook sukses disambungkan!\n";
} else {
    echo "❌ Gagal menyambungkan Telegram Webhook:\n";
    echo $res->body() . "\n";
}

echo "\nSelamat bekerja! Laporan dari masyarakat siap diterima.\n";
