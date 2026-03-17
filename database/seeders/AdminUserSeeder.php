<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'shadow902@gmail.com'],
            [
                'name' => 'Platform Admin',
                'username' => 'admin',
                'password' => Hash::make('Welc0me!1225'),
                'role' => 'admin',
                'is_creator' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
