<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\FieldJob;
use App\Models\Kecamatan;

// Check last job
$j = FieldJob::orderBy('id','desc')->first();
echo "Last job: #{$j->id} kec_id={$j->kecamatan_id} status={$j->status} ticket={$j->ticket_code}\n";

// Check pivot
$pivots = DB::table('kecamatan_teknisi')->get();
echo "\nPivot entries: " . count($pivots) . "\n";
foreach ($pivots as $r) {
    $kec = Kecamatan::find($r->kecamatan_id);
    $user = App\Models\User::find($r->user_id);
    echo "  {$user->name} (id={$r->user_id}) -> {$kec->nama} (kec_id={$r->kecamatan_id})\n";
}

// Check all kecamatans
echo "\nAll kecamatans:\n";
$kecs = Kecamatan::orderBy('id')->get();
foreach ($kecs as $k) {
    echo "  id={$k->id}: {$k->nama}\n";
}

// Test broadcast manually
echo "\n--- Testing broadcast for last job... ---\n";
$telegram = new App\Services\TelegramService();
$telegram->broadcastJob($j);
echo "Done!\n";
