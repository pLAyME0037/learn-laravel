<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Department;
use App\Models\Course;
use App\Models\ClassSchedule;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Classroom; // Import Classroom model
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as SpatieRole;

class AcademicYearDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch roles
        $professorRole = SpatieRole::where('name', 'professor')->first();
        $studentRole = SpatieRole::where('name', 'student')->first();

        // Ensure roles exist
        if (!$professorRole) {
            $professorRole = SpatieRole::create(['name' => 'professor', 'description' => 'Teaching faculty', 'is_system_role' => true]);
        }
        if (!$studentRole) {
            $studentRole = SpatieRole::create(['name' => 'student', 'description' => 'Student access', 'is_system_role' => true]);
        }

        // Create an Academic Year
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2025-2026 Academic Year'],
            [
                'start_date' => '2025-09-01',
                'end_date' => '2026-08-31',
                'is_current' => true,
            ]
        );
        $academicYear->save();

        $fallSemester = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Fall 2025',
            'start_date' => '2025-09-01',
            'end_date' => '2025-12-15',
            'is_current' => true,
        ]);

        $springSemester = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Spring 2026',
            'start_date' => '2026-01-15',
            'end_date' => '2026-05-15',
            'is_current' => false,
        ]);

        // Fetch existing Departments (assuming DepartmentSeeder has run)
        $csDepartment = Department::firstOrCreate(['code' => 'CS'], ['name' => 'Computer Science']);
        $mathDepartment = Department::firstOrCreate(['code' => 'MA'], ['name' => 'Mathematics']);

        // Create Instructors
        $instructor1 = User::firstOrCreate(
            ['email' => 'alice.smith@example.com'],
            [
                'name' => 'Dr. Alice Smith',
                'username' => 'alicesmith',
                'password' => Hash::make('password'),
                'department_id' => $csDepartment->id,
            ]
        );
        $instructor1->assignRole($professorRole);

        $instructor2 = User::firstOrCreate(
            ['email' => 'bob.johnson@example.com'],
            [
                'name' => 'Dr. Bob Johnson',
                'username' => 'bobjohnson',
                'password' => Hash::make('password'),
                'department_id' => $mathDepartment->id,
            ]
        );
        $instructor2->assignRole($professorRole);

        // Create Students
        $student1 = User::firstOrCreate(
            ['email' => 'student1@example.com'],
            [
                'name' => 'Student One',
                'username' => 'studentone',
                'password' => Hash::make('password'),
            ]
        );
        $student1->assignRole($studentRole);

        $student2 = User::firstOrCreate(
            ['email' => 'student2@example.com'],
            [
                'name' => 'Student Two',
                'username' => 'studenttwo',
                'password' => Hash::make('password'),
            ]
        );
        $student2->assignRole($studentRole);

        $student3 = User::firstOrCreate(
            ['email' => 'student3@example.com'],
            [
                'name' => 'Student Three',
                'username' => 'studentthree',
                'password' => Hash::make('password'),
            ]
        );
        $student3->assignRole($studentRole);

        $student4 = User::firstOrCreate(
            ['email' => 'student4@example.com'],
            [
                'name' => 'Student Four',
                'username' => 'studentfour',
                'password' => Hash::make('password'),
            ]
        );
        $student4->assignRole($studentRole);

        // Create Courses for Fall 2025
        $course1 = Course::create([
            'semester_id' => $fallSemester->id,
            'department_id' => $csDepartment->id,
            'instructor_id' => $instructor1->id,
            'name' => 'Introduction to Programming',
            'code' => 'CS101',
            'description' => 'Basic programming concepts.',
            'credits' => 3,
            'max_students' => 30,
            'start_date' => '2025-09-01',
            'end_date' => '2025-12-15',
            'status' => 'active',
        ]);

        $course2 = Course::create([
            'semester_id' => $fallSemester->id,
            'department_id' => $csDepartment->id,
            'instructor_id' => $instructor1->id,
            'name' => 'Data Structures',
            'code' => 'CS201',
            'description' => 'Advanced data structures.',
            'credits' => 4,
            'max_students' => 25,
            'start_date' => '2025-09-01',
            'end_date' => '2025-12-15',
            'status' => 'active',
        ]);

        $course3 = Course::create([
            'semester_id' => $fallSemester->id,
            'department_id' => $mathDepartment->id,
            'instructor_id' => $instructor2->id,
            'name' => 'Calculus I',
            'code' => 'MA101',
            'description' => 'Introduction to differential calculus.',
            'credits' => 3,
            'max_students' => 40,
            'start_date' => '2025-09-01',
            'end_date' => '2025-12-15',
            'status' => 'active',
        ]);

        // Create Classrooms
        $classroomA101 = Classroom::firstOrCreate(['room_number' => 'A101'], ['capacity' => 30]);
        $classroomA102 = Classroom::firstOrCreate(['room_number' => 'A102'], ['capacity' => 30]);
        $classroomB201 = Classroom::firstOrCreate(['room_number' => 'B201'], ['capacity' => 25]);
        $classroomC301 = Classroom::firstOrCreate(['room_number' => 'C301'], ['capacity' => 40]);

        // Create Class Schedules for Courses
        $classSchedule1 = ClassSchedule::create([
            'course_id' => $course1->id,
            'professor_id' => $instructor1->id,
            'classroom_id' => $classroomA101->id,
            'capacity' => 30,
            'day_of_week' => 'Monday',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
        ]);
        $classSchedule2 = ClassSchedule::create([
            'course_id' => $course1->id,
            'professor_id' => $instructor1->id,
            'classroom_id' => $classroomA102->id,
            'capacity' => 30,
            'day_of_week' => 'Wednesday',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);
        $classSchedule3 = ClassSchedule::create([
            'course_id' => $course2->id,
            'professor_id' => $instructor1->id,
            'classroom_id' => $classroomB201->id,
            'capacity' => 25,
            'day_of_week' => 'Tuesday',
            'start_time' => '11:00:00',
            'end_time' => '12:30:00',
        ]);
        $classSchedule4 = ClassSchedule::create([
            'course_id' => $course3->id,
            'professor_id' => $instructor2->id,
            'classroom_id' => $classroomC301->id,
            'capacity' => 40,
            'day_of_week' => 'Thursday',
            'start_time' => '13:00:00',
            'end_time' => '14:30:00',
        ]);

        // Create Enrollments
        Enrollment::create(['student_id' => $student1->id, 'class_schedule_id' => $classSchedule1->id, 'enrollment_date' => '2025-08-20']);
        Enrollment::create(['student_id' => $student2->id, 'class_schedule_id' => $classSchedule1->id, 'enrollment_date' => '2025-08-21']);
        Enrollment::create(['student_id' => $student3->id, 'class_schedule_id' => $classSchedule1->id, 'enrollment_date' => '2025-08-22']);
        Enrollment::create(['student_id' => $student4->id, 'class_schedule_id' => $classSchedule1->id, 'enrollment_date' => '2025-08-23']);

        Enrollment::create(['student_id' => $student1->id, 'class_schedule_id' => $classSchedule3->id, 'enrollment_date' => '2025-08-20']);
        Enrollment::create(['student_id' => $student2->id, 'class_schedule_id' => $classSchedule3->id, 'enrollment_date' => '2025-08-21']);

        Enrollment::create(['student_id' => $student3->id, 'class_schedule_id' => $classSchedule4->id, 'enrollment_date' => '2025-08-22']);
    }
}
