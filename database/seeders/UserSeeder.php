<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Create GSU user (Super Admin)
        User::create([
            'name' => 'GSU',
            'id_number' => 'GSU001',
            'email' => 'gsu@example.com',
            'password' => Hash::make('password'),
            'role' => 'gsu',
        ]);

        // Create Admin user
        User::create([
            'name' => 'Admin',
            'id_number' => 'ADMIN001',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create User
        User::create([
            'name' => 'User',
            'id_number' => 'USER001',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
} 