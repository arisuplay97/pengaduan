<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$botToken = config('services.telegram.bot_token');
$webhookSecret = config('services.telegram.webhook_secret');
$domain = 'https://pdam-tsa.loca.lt';

echo "Registering webhook via Laravel HTTP Client...\n";

$response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
    'url'          => "{$domain}/api/telegram/webhook",
    'secret_token' => $webhookSecret,
]);

echo "Response Body:\n";
echo $response->body() . "\n";
echo "Done!\n";
