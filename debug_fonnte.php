<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fetching latest job...\n";
$job = \App\Models\FieldJob::latest()->first();

if (!$job) {
    die("No jobs found.\n");
}

echo "Job ID: {$job->id}, Kecamatan: {$job->kecamatan_id}\n";

$fonnte = new \App\Services\FonnteService();
echo "Triggering broadcast...\n";
$fonnte->broadcastJob($job);

echo "Checking the recent log entries...\n";
$logData = file_get_contents(storage_path('logs/laravel.log'));
$lines = explode("\n", $logData);
foreach (array_slice($lines, -10) as $line) {
    echo $line . "\n";
}
echo "Done.\n";
