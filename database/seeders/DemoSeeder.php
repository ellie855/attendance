<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // デモ用: 一般ユーザー
        User::updateOrCreate(
            ['email' => 'demo-user@example.com'],
            [
                'name' => 'デモ一般',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // デモ用: 管理者
        User::updateOrCreate(
            ['email' => 'demo-admin@example.com'],
            [
                'name' => 'デモ管理者',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
