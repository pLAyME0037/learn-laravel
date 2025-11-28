<?php
namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an Academic Year if it doesn't exist
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2025-2026 Academic Year'],
            [
                'start_date' => Carbon::parse('2025-09-01'),
                'end_date'   => Carbon::parse('2026-08-31'),
                'is_current' => true,
            ]
        );

        // Create Semesters for the Academic Year
        Semester::firstOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'name'             => 'Fall 2025',
            ],
            [
                'start_date' => '2025-09-01',
                'end_date'   => '2025-12-15',
                'is_active' => true,
            ]
        );

        Semester::firstOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'name'             => 'Spring 2026',
            ],
            [
                'start_date' => '2026-01-15',
                'end_date'   => '2026-05-15',
                'is_active' => false,
            ]
        );

        // Add more semesters as needed
    }
}
