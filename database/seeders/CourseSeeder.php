<?php
namespace Database\Seeders;

use App\Models\Course;
use App\Models\Faculty;
use App\Models\Program;  // Import Faculty model
use App\Models\Semester; // Import Semester model
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing program IDs and faculty IDs to assign courses to them
        $programIds  = Program::pluck('id')->toArray();
        $facultyIds  = Faculty::pluck('id')->toArray();
        $semesterIds = Semester::pluck('id')->toArray(); // Assuming Semester model and seeder exist

        // Ensure necessary related data exists
        if (empty($programIds) || empty($facultyIds) || empty($semesterIds)) {
            $this->command->warn(''
                . 'Some prerequisite data (Programs, Faculties, or Semesters) not found. Please seed them first.'
        );
        }

        // Define sample courses
        $coursesData = [
            // Computer Science Courses
            [
                'program_code'  => 'BSCS',
                'faculty_code'  => 'Faculty of Science',
                'semester_name' => 'Fall 2025', // Use semester name
                'name'          => 'Introduction to Computer Science',
                'code'          => 'CS101',
                'credits'       => 3,
                'max_students'  => 50,
                'start_date'    => Carbon::now()->addDays(10),
                'end_date'      => Carbon::now()->addMonths(4),
                'status'        => 'Active',
            ],
            [
                'program_code'  => 'BSCS',
                'faculty_code'  => 'Faculty of Science',
                'semester_name' => 'Fall 2025',
                'name'          => 'Data Structures and Algorithms',
                'code'          => 'CS201',
                'credits'       => 4,
                'max_students'  => 45,
                'start_date'    => Carbon::now()->addDays(10),
                'end_date'      => Carbon::now()->addMonths(4),
                'status'        => 'Active',
            ],
            [
                'program_code'  => 'MSCS',
                'faculty_code'  => 'Faculty of Science',
                'semester_name' => 'Fall 2025',
                'name'          => 'Advanced Algorithms',
                'code'          => 'CS501',
                'credits'       => 3,
                'max_students'  => 30,
                'start_date'    => Carbon::now()->addDays(15),
                'end_date'      => Carbon::now()->addMonths(4),
                'status'        => 'Active',
            ],
            // Mathematics Courses
            [
                'program_code'  => 'BSMATH',
                'faculty_code'  => 'Faculty of Science',
                'semester_name' => 'Fall 2025',
                'name'          => 'Calculus I',
                'code'          => 'MATH101',
                'credits'       => 4,
                'max_students'  => 60,
                'start_date'    => Carbon::now()->addDays(10),
                'end_date'      => Carbon::now()->addMonths(4),
                'status'        => 'Active',
            ],
            [
                'program_code'  => 'BSMATH',
                'faculty_code'  => 'Faculty of Science',
                'semester_name' => 'Fall 2025',
                'name'          => 'Linear Algebra',
                'code'          => 'MATH201',
                'credits'       => 3,
                'max_students'  => 55,
                'start_date'    => Carbon::now()->addDays(15),
                'end_date'      => Carbon::now()->addMonths(4),
                'status'        => 'Active',
            ],
            // Electrical Engineering Courses
            [
                'program_code'  => 'BSEE',
                'faculty_code'  => 'Faculty of Engineering',
                'semester_name' => 'Fall 2025',
                'name'          => 'Circuit Theory',
                'code'          => 'EE201',
                'credits'       => 4,
                'max_students'  => 40,
                'start_date'    => Carbon::now()->addDays(10),
                'end_date'      => Carbon::now()->addMonths(4),
                'status'        => 'Active',
            ],
            [
                'program_code'  => 'BSEE',
                'faculty_code'  => 'Faculty of Engineering',
                'semester_name' => 'Fall 2025',
                'name'          => 'Electromagnetics',
                'code'          => 'EE301',
                'credits'       => 3,
                'max_students'  => 35,
                'start_date'    => Carbon::now()->addDays(15),
                'end_date'      => Carbon::now()->addMonths(4),
                'status'        => 'Active',
            ],
            // Business Administration Courses
            [
                'program_code'  => 'BBA',
                'faculty_code'  => 'Faculty of Business',
                'semester_name' => 'Fall 2025',
                'name'          => 'Principles of Management',
                'code'          => 'MGMT101',
                'credits'       => 3,
                'max_students'  => 50,
                'start_date'    => Carbon::now()->addDays(10),
                'end_date'      => Carbon::now()->addMonths(4),
                'status'        => 'Active',
            ],
        ];

        foreach ($coursesData as $courseData) {
            // Find the program, faculty, and semester IDs
            $program = Program::where(
                'code',
                $courseData['program_code']
            )->firstOrFail();
            $faculty = Faculty::where(
                'name',
                $courseData['faculty_code']
            )->firstOrFail();
            $semester = Semester::where(
                'name',
                $courseData['semester_name']
            )->firstOrFail();

            $createCourseData = [
                'name'          => $courseData['name'],
                'code'          => $courseData['code'],                // Ensure code is also in create data
                'description'   => $courseData['description'] ?? null, // Provide a default null if description is missing
                'credits'       => $courseData['credits'],
                'department_id' => $program->department_id,
                'faculty_id'    => $faculty->id,
                'program_id'    => $program->id,
                'semester_id'   => $semester->id,
                'max_students'  => $courseData['max_students'],
                'start_date'    => $courseData['start_date'],
                'end_date'      => $courseData['end_date'],
                'status'        => $courseData['status'],
            ];

            Course::updateOrCreate(
                ['code' => $courseData['code']], // Unique identifier for the course
                $createCourseData
            );
        }
    }
}
