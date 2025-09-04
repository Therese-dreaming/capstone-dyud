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
        // Create Super Admin user
        User::create([
            'name' => 'Super Admin',
            'id_number' => 'SUPER001',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        // Create GSU user
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