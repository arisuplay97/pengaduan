<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@pdam.go.id',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'Direktur Utama',
                'username' => 'dirut',
                'email' => 'dirut@pdam.go.id',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'Direktur Umum',
                'username' => 'dirum',
                'email' => 'dirum@pdam.go.id',
                'password' => Hash::make('123456'),
            ],
            [
                'name' => 'Direktur Operasional',
                'username' => 'dirop',
                'email' => 'dirop@pdam.go.id',
                'password' => Hash::make('123456'),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }
    }
}
