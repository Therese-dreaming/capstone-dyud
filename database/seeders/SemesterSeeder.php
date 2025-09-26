<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Semester;
use Carbon\Carbon;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create semesters for multiple academic years
        $this->createSemestersForYear('2023-2024', 2023);
        $this->createSemestersForYear('2024-2025', 2024);
        $this->createSemestersForYear('2025-2026', 2025);
        $this->createSemestersForYear('2026-2027', 2026);
        
        // Set current semester based on today's date
        $this->setCurrentSemester();
    }
    
    /**
     * Create semesters for a specific academic year
     */
    private function createSemestersForYear($academicYear, $startYear)
    {
        $semesters = [
            [
                'name' => '1st Semester',
                'start_date' => Carbon::create($startYear, 8, 1),
                'end_date' => Carbon::create($startYear, 12, 31),
                'description' => "First semester of academic year {$academicYear}"
            ],
            [
                'name' => '2nd Semester', 
                'start_date' => Carbon::create($startYear + 1, 1, 1),
                'end_date' => Carbon::create($startYear + 1, 5, 31),
                'description' => "Second semester of academic year {$academicYear}"
            ],
            [
                'name' => 'Summer',
                'start_date' => Carbon::create($startYear + 1, 6, 1),
                'end_date' => Carbon::create($startYear + 1, 7, 31),
                'description' => "Summer semester of academic year {$academicYear}"
            ]
        ];
        
        foreach ($semesters as $semesterData) {
            Semester::create([
                'name' => $semesterData['name'],
                'academic_year' => $academicYear,
                'start_date' => $semesterData['start_date'],
                'end_date' => $semesterData['end_date'],
                'is_current' => false,
                'is_active' => true,
                'description' => $semesterData['description']
            ]);
        }
    }
    
    /**
     * Set the current semester based on today's date
     */
    private function setCurrentSemester()
    {
        $today = now();
        
        // Find semester that contains today's date
        $currentSemester = Semester::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('is_active', true)
            ->first();
        
        if ($currentSemester) {
            $currentSemester->setCurrent();
            $this->command->info("Set current semester: {$currentSemester->academic_year} - {$currentSemester->name}");
        } else {
            // If no semester contains today's date, set the most recent one as current
            $latestSemester = Semester::where('is_active', true)
                ->orderBy('start_date', 'desc')
                ->first();
                
            if ($latestSemester) {
                $latestSemester->setCurrent();
                $this->command->info("Set latest semester as current: {$latestSemester->academic_year} - {$latestSemester->name}");
            }
        }
    }
}
