<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['username' => 'atasan1', 'name' => 'Atasan Satu', 'email' => 'atasan1@example.com', 'role' => 'Atasan', 'approval_level' => 1],
            ['username' => 'atasan2', 'name' => 'Atasan Dua',  'email' => 'atasan2@example.com', 'role' => 'Atasan', 'approval_level' => 2],
            ['username' => 'pic1',    'name' => 'PIC Satu',    'email' => 'pic1@example.com',    'role' => 'PIC',    'approval_level' => null],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['username' => $account['username']],
                $account + ['password' => Hash::make('password123'), 'is_active' => true]
            );
        }
    }
}