<?php
namespace Database\Seeders;

use App\Models\Course;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursePrerequisiteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clean existing prerequisites
        DB::table('course_prerequisites')->truncate();

        // 2. Fetch all Programs to process their courses
        $programs = Program::select('id')->get();

        if ($programs->isEmpty()) {
            $this->command->error('No programs found. Run AcademicStructureSeeder first.');
            return;
        }

        $prerequisitesToInsert = [];
        $now                   = now();

        foreach ($programs as $program) {
            // Get all courses for this specific program
            // We assume codes contain level info (e.g., CRS-1-100, CRS-1-200)
            // Or we can sort by ID assuming they were created sequentially
            $courses = Course::where('program_id', $program->id)
                ->orderBy('code') // Assuming code structure helps order
                ->get();

            // Map courses by "Level" (approximate logic based on seeder)
            // Seeder created 4 courses per program.
            // Course 0 (Level 100) -> No Prereq
            // Course 1 (Level 200) -> Requires Course 0
            // Course 2 (Level 300) -> Requires Course 1

            if ($courses->count() < 2) {
                continue;
            }

            for ($i = 1; $i < $courses->count(); $i++) {
                $currentCourse = $courses[$i];
                $prevCourse    = $courses[$i - 1];

                // 50% chance to add a prerequisite to make it realistic (not every course has one)
                if (rand(1, 100) <= 80) {
                    $prerequisitesToInsert[] = [
                        'course_id'              => $currentCourse->id,
                        'prerequisite_id' => $prevCourse->id,
                        'created_at'             => $now,
                        'updated_at'             => $now,
                    ];
                }
            }
        }

        // 3. Bulk Insert
        if (! empty($prerequisitesToInsert)) {
            foreach (array_chunk($prerequisitesToInsert, 200) as $chunk) {
                DB::table('course_prerequisites')->insert($chunk);
            }
            $this->command->info('Seeded ' . count($prerequisitesToInsert) . ' prerequisites based on program levels.');
        } else {
            $this->command->warn('No prerequisites seeded (not enough courses per program).');
        }
    }
}
