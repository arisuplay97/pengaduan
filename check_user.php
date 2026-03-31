<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('username', 'petugas1')->first();

if ($user) {
    echo "User Found:\n";
    echo "ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Username: " . $user->username . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Password Hash: " . $user->password . "\n";
    
    // Check if password matches 'petugas1' or 'password'
    if (Hash::check('petugas1', $user->password)) {
        echo "Password matches 'petugas1'\n";
    } elseif (Hash::check('password', $user->password)) {
        echo "Password matches 'password'\n";
    } else {
        echo "Password does NOT match 'petugas1' or 'password'\n";
    }
} else {
    echo "User 'petugas1' NOT FOUND.\n";
    
    // List all users to see what's there
    echo "Listing all users:\n";
    $users = User::all();
    foreach ($users as $u) {
        echo "- " . $u->username . " (" . $u->email . ")\n";
    }
}
