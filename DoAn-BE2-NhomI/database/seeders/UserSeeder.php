<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $adminId = DB::table('users')->insertGetId([
            'email' => 'admin@gmail.com',
            'password_hash' => Hash::make('12345678'),
            'full_name' => 'Administrator',
            'phone' => '0900000001',
            'avatar_url' => null,
            'role' => 'admin',
            'provider' => null,
            'provider_id' => null,
            'is_active' => 1,
            'is_verified' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Staff
        DB::table('users')->insert([
            'email' => 'staff@gmail.com',
            'password_hash' => Hash::make('12345678'),
            'full_name' => 'Staff User',
            'phone' => '0900000002',
            'avatar_url' => null,
            'role' => 'staff',
            'provider' => null,
            'provider_id' => null,
            'is_active' => 1,
            'is_verified' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User mẫu
        DB::table('users')->insert([
            'email' => 'user@gmail.com',
            'password_hash' => Hash::make('12345678'),
            'full_name' => 'Normal User',
            'phone' => '0900000003',
            'avatar_url' => null,
            'role' => 'user',
            'provider' => null,
            'provider_id' => null,
            'is_active' => 1,
            'is_verified' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        
        // 20 user thường
        for ($i = 1; $i <= 20; $i++) {
            DB::table('users')->insert([
                'email' => "user{$i}@gmail.com",
                'password_hash' => Hash::make('12345678'),
                'full_name' => "User {$i}",
                'phone' => '09' . str_pad((string)($i + 3), 8, '0', STR_PAD_LEFT),
                'avatar_url' => null,
                'role' => 'user',
                'provider' => null,
                'provider_id' => null,
                'is_active' => 1,
                'is_verified' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2FA cho admin
        DB::table('user_2fa')->insert([
            'user_id' => $adminId,
            'secret_key' => Str::random(32),
            'is_enabled' => 1,
            'created_at' => now(),
        ]);
    }
}