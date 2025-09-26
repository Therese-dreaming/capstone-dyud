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
        // Call the UserSeeder to create the three users
        $this->call([
            UserSeeder::class,
            SemesterSeeder::class,
        ]);

        \App\Models\Category::insert([
            ['name' => 'Furnitures', 'code' => 'FUR'],
            ['name' => 'Electronics & IT Equipment', 'code' => 'ELE'],
            ['name' => 'Teaching & Presentation Tools', 'code' => 'TPT'],
            ['name' => 'Fixtures', 'code' => 'FIX'],
            ['name' => 'Religious or Institutional Items', 'code' => 'REL'],
        ]);
        
    }
}
