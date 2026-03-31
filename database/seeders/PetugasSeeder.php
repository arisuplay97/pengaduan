<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class PetugasSeeder extends Seeder
{
    public function run(): void
    {
        $petugasList = [
            ['name' => 'Budi Petugas', 'username' => 'petugas1', 'email' => 'petugas1@pdam.test'],
            ['name' => 'Andi Teknisi', 'username' => 'petugas2', 'email' => 'petugas2@pdam.test'],
            ['name' => 'Siti Montir', 'username' => 'petugas3', 'email' => 'petugas3@pdam.test'],
        ];

        foreach ($petugasList as $data) {
            User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt('password'),
                    'role' => 'petugas',
                    'magic_token' => 'token-' . Str::random(20),
                ]
            );
        }

        // Print tokens for convenience
        $petugas = User::where('role', 'petugas')->get();
        foreach ($petugas as $p) {
            echo "{$p->name}: /akses-petugas/{$p->magic_token}\n";
        }
    }
}
