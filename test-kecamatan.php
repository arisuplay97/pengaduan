<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jobs = \App\Models\FieldJob::with('kecamatan')->whereDate('created_at', \Carbon\Carbon::today())->get();

foreach ($jobs as $job) {
    echo "Job ID: {$job->id} | Kec ID: {$job->kecamatan_id} | Name: " . ($job->kecamatan ? $job->kecamatan->name : 'NULL') . "\n";
}
