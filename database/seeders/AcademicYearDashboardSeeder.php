<?php
namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Instructor;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicYearDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1. PRE-CHECKS
        $semester = Semester::where('name', 'Fall 2025')->first();
        if (! $semester) {
            $this->command->error('Semester "Fall 2025" not found. Run AcademicStructureSeeder first.');
            return;
        }

        // 2. CREATE CLASSROOMS
        $classrooms = [
            ['room_number' => 'A101', 'building_name' => 'Science Block', 'capacity' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['room_number' => 'A102', 'building_name' => 'Science Block', 'capacity' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['room_number' => 'B201', 'building_name' => 'Engineering Block', 'capacity' => 60, 'created_at' => $now, 'updated_at' => $now],
            ['room_number' => 'C301', 'building_name' => 'Main Hall', 'capacity' => 100, 'created_at' => $now, 'updated_at' => $now],
        ];
        Classroom::insertOrIgnore($classrooms);
        // Map classrooms by ID to easily get their capacities
        $classroomCapacities = Classroom::pluck('capacity', 'id');
        $classroomIds        = Classroom::pluck('id')->toArray();

        // 3. CREATE INSTRUCTORS
        $potentialInstructors = User::whereDoesntHave('student')->take(10)->get();
        foreach ($potentialInstructors as $user) {
            if (! $user->instructor) {
                Instructor::create([
                    'user_id'       => $user->id,
                    'department_id' => $user->department_id ?? 1,
                    'payscale'      => 'Associate Professor',
                    'hire_date'     => $now->subYears(rand(1, 10)),
                ]);
                $user->assignRole('professor');
            }
        }
        // Get a map of instructor_id (from Instructors table) to user_id (professor_id in class_schedules)
        $instructorUserMap = Instructor::with('user')->get()->keyBy('id')->map(fn($instructor) => $instructor->user_id);
        $instructorIds     = Instructor::pluck('id')->toArray();

        if (empty($instructorIds)) {
            $this->command->error('No Instructors found. Make sure you have Users who are not Students.');
            return;
        }

        // 4. CREATE CLASS SCHEDULES
        // FIX: Try to find courses for this semester. If none, grab ANY courses.
        $courses = Course::where('semester_id', $semester->id)->take(20)->get();

        if ($courses->isEmpty()) {
            $this->command->warn("No courses found specifically for Fall 2025. Grabbing random courses...");
            $courses = Course::take(20)->get();
        }

        if ($courses->isEmpty()) {
            $this->command->error('No courses found in database at all. Run AcademicStructureSeeder.');
            return;
        }

        $schedulesToInsert = [];
        $days              = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $times             = ['09:00:00', '11:00:00', '13:00:00', '15:00:00'];

        $classroomCapacityMap = Classroom::pluck('capacity', 'id')->toArray();
        foreach ($courses as $index => $course) {
            $instructorId = $instructorIds[$index % count($instructorIds)];
            $classroomId  = $classroomIds[$index % count($classroomIds)];
            $capacity     = $classroomCapacityMap[$classroomId] ?? 30;
            $day          = $days[$index % count($days)];
            $startTime    = $times[$index % count($times)];
            $endTime      = date('H:i:s', strtotime($startTime) + 5400);

            $schedulesToInsert[] = [
                'capacity'      => $capacity,
                'course_id'     => $course->id,
                'instructor_id' => $instructorUserMap[$instructorId], // Correctly use instructor_id and map to user_id
                'classroom_id'  => $classroomId,
                'semester_id'   => $semester->id,                      // Force assignment to this semester
                'capacity'      => $classroomCapacities[$classroomId], // Add capacity from classroom
                'day_of_week'   => $day,
                'start_time'    => $startTime,
                'end_time'      => $endTime,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // Use INSERT instead of InsertOrIgnore to see SQL errors if any
        ClassSchedule::insert($schedulesToInsert);

        // Fetch back the created schedules
        $schedules = ClassSchedule::where('semester_id', $semester->id)->get();

        if ($schedules->isEmpty()) {
            $this->command->error('Failed to create schedules (Insert failed). Check database logs.');
            return;
        }

        // 5. CREATE ENROLLMENTS
        $students = Student::where('academic_status', 'active')->take(50)->get();

        if ($students->isEmpty()) {
            $this->command->warn('No Active Students found. Run StudentSeeder.');
            return;
        }

        $enrollmentsToInsert = [];

        foreach ($students as $student) {
            // FIX: Use min() to prevent requesting more items than available
            $count            = min($schedules->count(), rand(3, 5));
            $studentSchedules = $schedules->random($count);

            foreach ($studentSchedules as $schedule) {
                $enrollmentsToInsert[] = [
                    'student_id'        => $student->id,
                    'class_schedule_id' => $schedule->id,
                    'semester_id'       => $semester->id,
                    'status'            => 'enrolled',
                    'enrollment_date'   => $now->subDays(rand(1, 20)),
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        // Use chunking for enrollments
        foreach (array_chunk($enrollmentsToInsert, 100) as $chunk) {
            Enrollment::insertOrIgnore($chunk);
        }

        $this->command->info('Dashboard Data Seeded: Classrooms, Instructors, Schedules, Enrollments.');
    }
}
