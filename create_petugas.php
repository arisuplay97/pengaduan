<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$username = 'petugas1';
$password = 'password';

$user = User::where('username', $username)->first();

if ($user) {
    echo "User '$username' already exists.\n";
} else {
    $user = User::create([
        'name' => 'Petugas Lapangan 1',
        'username' => $username,
        'email' => 'petugas1@pdam.go.id',
        'password' => Hash::make($password),
        'role' => 'petugas', // Assuming 'role' column exists based on previous migrations
        'email_verified_at' => now(),
    ]);
    
    echo "User created successfully!\n";
    echo "Username: $username\n";
    echo "Password: $password\n";
    echo "Role: " . $user->role . "\n";
}
