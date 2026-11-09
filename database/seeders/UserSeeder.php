<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Jangan buat admin bawaan jika admin sudah dibuat melalui wizard instalasi
        if (User::role(UserRole::Admin->value)->exists()) {
            return;
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@satusuara.id'],
            [
                'name' => 'Administrator Panitia',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([UserRole::Admin->value]);
    }
}

