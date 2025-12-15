<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Program;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup Context
        $activeSemester = Semester::where('is_active', true)->first();
        if (!$activeSemester) {
            $this->command->error('No Active Semester found. Please run AcademicStructureSeeder first.');
            return;
        }

        // Get Instructor (Staff)
        $instructor = User::where('email', 'staff@university.com')->first();
        if (!$instructor) {
            $instructor = User::first(); // Fallback
        }

        // 2. Define The Curriculum Map
        // [Program Name, Course Code, Course Name, Credits, Year, Term]
        $curriculum = [
            // --- Bachelor of Software Engineering (Year 1, Term 1) ---
            ['Bachelor of Software Engineering', 'CS101', 'Intro to Computer Science', 3, 1, 1],
            ['Bachelor of Software Engineering', 'MATH101', 'Calculus I', 3, 1, 1],
            ['Bachelor of Software Engineering', 'ENG101', 'Academic English', 3, 1, 1],
            
            // --- Bachelor of Software Engineering (Year 1, Term 2) ---
            ['Bachelor of Software Engineering', 'CS102', 'Programming Fundamentals', 4, 1, 2],
            ['Bachelor of Software Engineering', 'MATH102', 'Linear Algebra', 3, 1, 2],

            // --- Bachelor of Computer Science (Year 1, Term 1) ---
            ['Bachelor of Computer Science', 'CS101', 'Intro to Computer Science', 3, 1, 1],
            ['Bachelor of Computer Science', 'IT101', 'IT Fundamentals', 3, 1, 1],
        ];

        foreach ($curriculum as [$progName, $code, $name, $credits, $year, $term]) {
            
            // A. Find Program
            $program = Program::with('major.department')->where('name', $progName)->first();
            
            if (!$program) {
                $this->command->warn("Program not found: $progName (Skipping)");
                continue;
            }

            // B. Create Course (Catalog)
            $course = Course::firstOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'department_id' => $program->major->department_id,
                    'credits' => $credits,
                    'description' => "Standard curriculum for $code",
                ]
            );

            // C. Create Roadmap (Program Structure) -> THIS FIXES YOUR BUG
            DB::table('program_structures')->updateOrInsert(
                [
                    'program_id' => $program->id,
                    'course_id' => $course->id
                ],
                [
                    'recommended_year' => $year,
                    'recommended_term' => $term,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            // D. Schedule Class (Class Session)
            // Only schedule if it's for the current term (optional logic, but good for demo)
            // For simplicity, we schedule ALL of them in the active semester so you can test easily.
            ClassSession::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'semester_id' => $activeSemester->id,
                    'section_name' => 'A'
                ],
                [
                    'instructor_id' => $instructor->id,
                    'capacity' => 50,
                    'day_of_week' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'][rand(0, 4)],
                    'start_time' => '09:00:00',
                    'end_time' => '10:30:00',
                    'status' => 'open'
                ]
            );
        }

        $this->command->info('Courses, Roadmap, and Schedule seeded successfully.');
    }
}