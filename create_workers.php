<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$password = Hash::make('123456');

for ($i = 2; $i <= 6; $i++) {
    try {
        User::create([
            'name' => 'Petugas ' . $i,
            'username' => 'petugas' . $i,
            'email' => 'petugas' . $i . '@pdam.com',
            'password' => $password,
            'role' => 'petugas',
        ]);
        echo "Created petugas$i\n";
    } catch (\Exception $e) {
        echo "Failed petugas$i: " . $e->getMessage() . "\n";
    }
}
