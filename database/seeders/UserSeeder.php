<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Only creates users if they don't exist yet.
     * Never overwrites passwords of existing users.
     */
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Superadmin',
                'username' => 'superadmin',
                'email'    => 'superadmin@pdam.go.id',
                'role'     => 'superadmin',
            ],
            [
                'name'     => 'Administrator',
                'username' => 'admin',
                'email'    => 'admin@pdam.go.id',
                'role'     => 'admin',
            ],
            [
                'name'     => 'Direktur Utama',
                'username' => 'dirut',
                'email'    => 'dirut@pdam.go.id',
            ],
            [
                'name'     => 'Direktur Umum',
                'username' => 'dirum',
                'email'    => 'dirum@pdam.go.id',
            ],
            [
                'name'     => 'Direktur Operasional',
                'username' => 'dirop',
                'email'    => 'dirop@pdam.go.id',
            ],
        ];

        foreach ($users as $userData) {
            // Only create if user doesn't exist — never overwrite password
            if (!User::where('username', $userData['username'])->exists()) {
                $defaultPassword = env('DEFAULT_SEED_PASSWORD', 'ChangeMeImmediately!2026');
                User::create(array_merge($userData, [
                    'password' => Hash::make($defaultPassword),
                ]));
            }
        }
    }
}
