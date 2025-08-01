<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin User',
            'id_number' => 'admin123',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
        \App\Models\Category::insert([
            ['name' => 'Furnitures', 'code' => 'FUR'],
            ['name' => 'Electronics & IT Equipment', 'code' => 'ELE'],
            ['name' => 'Teaching & Presentation Tools', 'code' => 'TPT'],
            ['name' => 'Fixtures', 'code' => 'FIX'],
            ['name' => 'Religious or Institutional Items', 'code' => 'REL'],
        ]);
        
        // Seed borrowing data
        $this->call(BorrowingSeeder::class);
    }
}
