<?php
namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as ModelsRole;
use Spatie\Permission\Models\SpatieRole as Role;

class AcademicYearDashboardSeeder extends Seeder
{
/**
 * Run the database seeds.
 * This seeder creates a complete academic scenario for a dashboard.
 * It is optimized for performance using bulk operations and in-memory mapping.
 */
    public function run(): void
    {
        // 1. Fetch or create foundational data that everything else depends on.
        $roles       = $this->fetchOrCreateRoles(['professor', 'student']);
        $departments = $this->fetchOrCreateDepartments(['CS', 'MATH']);
        $semesters   = $this->fetchOrCreateSemesters(['Fall 2025', 'Spring 2026']);

        AcademicYear::firstOrCreate(['name' => '2025-2026 Academic Year']);

        // 2. Centralized data configuration for the entire scenario.
        $scenarioData = $this->getScenarioData();

        // 3. Seed users and map them by email for easy lookup.
        $userMap = $this->seedUsers($scenarioData['users'], $roles, $departments);

        // 4. Seed classrooms and map them by room number.
        $classroomMap = $this->seedClassrooms($scenarioData['classrooms']);

        // 5. Seed courses, linking them to departments, semesters, and instructors.
        $courseMap = $this->seedCourses($scenarioData['courses'], $departments, $semesters, $userMap);

        // 6. Seed class schedules, linking them to courses, classrooms, and professors.
        $scheduleMap = $this->seedClassSchedules($scenarioData['schedules'], $courseMap, $classroomMap, $userMap);

        // 7. Seed enrollments, linking students to their class schedules.
        $this->seedEnrollments($scenarioData['enrollments'], $userMap, $scheduleMap);

        $this->command->info('Academic Year Dashboard scenario has been seeded successfully.');
    }

    /**
     * Fetches or creates a set of Spatie Roles.
     * @return Collection A collection of Role models keyed by their name.
     */
    private function fetchOrCreateRoles(array $roleNames): Collection
    {
        $roles = collect();
        foreach ($roleNames as $name) {
            $roles[$name] = ModelsRole::firstOrCreate(
                ['name' => $name],
                ['description' => ucfirst($name) . ' access', 'is_system_role' => true]
            );
        }
        return $roles;
    }

    /**
     * Fetches or creates a set of Departments.
     * @return Collection A collection of Department models keyed by their code.
     */
    private function fetchOrCreateDepartments(array $departmentCodes): Collection
    {
        $departments = collect();
        foreach ($departmentCodes as $code) {
            $departments[$code] = Department::firstOrCreate(
                ['code' => $code],
                ['name' => "Department of {$code}"]
            );
        }
        return $departments;
    }

    /**
     * Fetches or creates a set of Semesters.
     * @return Collection A collection of Semester models keyed by their name.
     */
    private function fetchOrCreateSemesters(array $semesterNames): Collection
    {
        $semesters = collect();
        foreach ($semesterNames as $name) {
            $semesters[$name] = Semester::firstOrCreate(['name' => $name]);
        }
        return $semesters;
    }

    /**
     * Seeds users from the config and assigns roles.
     * @return Collection A collection of User models keyed by their email.
     */
    private function seedUsers(array $usersData, Collection $roles, Collection $departments): Collection
    {
        $userMap = collect();
        foreach ($usersData as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'          => $userData['name'],
                    'username'      => $userData['username'],
                    'password'      => Hash::make('password'),
                    'department_id' => $userData['department_code'] 
                    ? $departments[$userData['department_code']]->id 
                    : null,
                ]
            );
            $user->assignRole($roles[$userData['role']]);
            $userMap[$user->email] = $user;
        }
        return $userMap;
    }

    /**
     * Seeds classrooms using a bulk `upsert`.
     * @return Collection A collection of Classroom models keyed by their room number.
     */
    private function seedClassrooms(array $classroomsData): Collection
    {
        Classroom::upsert($classroomsData, ['room_number'], ['capacity']);
        return Classroom::whereIn('room_number', array_column(
            $classroomsData, 
        'room_number'
        ))->get()->keyBy('room_number');
    }

    /**
     * Seeds courses using a bulk `upsert`.
     * @return Collection A collection of Course models keyed by their code.
     */
    private function seedCourses(array $coursesData, Collection $departments, Collection $semesters, Collection $userMap): Collection
    {
        $coursesToUpsert = collect($coursesData)->map(function ($course) use ($departments, $semesters, $userMap) {
            $course['department_id'] = $departments[$course['department_code']]->id;
            $course['semester_id']   = $semesters[$course['semester_name']]->id;
            $course['instructor_id'] = $userMap[$course['instructor_email']]->id;
            unset($course['department_code'], $course['semester_name'], $course['instructor_email']);
            return $course;
        })->all();

        Course::upsert($coursesToUpsert, ['code', 'semester_id'], array_keys($coursesToUpsert[0]));
        return Course::whereIn('code', array_column($coursesToUpsert, 'code'))->get()->keyBy('code');
    }

    /**
     * Seeds class schedules using a bulk `insert`.
     * @return Collection A collection of ClassSchedule models keyed by a composite key.
     */
    private function seedClassSchedules(array $schedulesData, Collection $courseMap, Collection $classroomMap, Collection $userMap): Collection
    {
        $now               = now();
        $schedulesToInsert = collect($schedulesData)->map(function ($schedule) use ($courseMap, $classroomMap, $userMap, $now) {
            $schedule['course_id']    = $courseMap[$schedule['course_code']]->id;
            $schedule['classroom_id'] = $classroomMap[$schedule['room_number']]->id;
            $schedule['professor_id'] = $userMap[$schedule['professor_email']]->id;
            $schedule['created_at']   = $now;
            $schedule['updated_at']   = $now;
            unset($schedule['course_code'], $schedule['room_number'], $schedule['professor_email']);
            return $schedule;
        })->all();

        ClassSchedule::insert($schedulesToInsert);
        // We need to fetch the schedules back to get their IDs for enrollments
        return ClassSchedule::with('course')->get()->keyBy(function ($item) {
            return $item->course->code . '_' . $item->day_of_week;
        });
    }

    /**
     * Seeds enrollments using a bulk `insert`.
     */
    private function seedEnrollments(array $enrollmentsData, Collection $userMap, Collection $scheduleMap): void
    {
        $now                 = now();
        $enrollmentsToInsert = collect($enrollmentsData)->map(function ($enrollment) use ($userMap, $scheduleMap, $now) {
            $enrollment['student_id']        = $userMap[$enrollment['student_email']]->id;
            $enrollment['class_schedule_id'] = $scheduleMap[$enrollment['schedule_key']]->id;
            $enrollment['created_at']        = $now;
            $enrollment['updated_at']        = $now;
            unset($enrollment['student_email'], $enrollment['schedule_key']);
            return $enrollment;
        })->all();

        Enrollment::insert($enrollmentsToInsert);
    }

    /**
     * Centralized configuration for the entire academic scenario.
     * @return array
     */
    private function getScenarioData(): array
    {
        return [
            'users'       => [
                ['name' => 'Dr. Alice Smith', 'email' => 'alice.smith@example.com', 'username' => 'alicesmith', 'role' => 'professor', 'department_code' => 'CS'],
                ['name' => 'Dr. Bob Johnson', 'email' => 'bob.johnson@example.com', 'username' => 'bobjohnson', 'role' => 'professor', 'department_code' => 'MATH'],
                ['name' => 'Student One', 'email' => 'student1@example.com', 'username' => 'studentone', 'role' => 'student', 'department_code' => null],
                ['name' => 'Student Two', 'email' => 'student2@example.com', 'username' => 'studenttwo', 'role' => 'student', 'department_code' => null],
                ['name' => 'Student Three', 'email' => 'student3@example.com', 'username' => 'studentthree', 'role' => 'student', 'department_code' => null],
                ['name' => 'Student Four', 'email' => 'student4@example.com', 'username' => 'studentfour', 'role' => 'student', 'department_code' => null],
            ],
            'classrooms'  => [
                ['room_number' => 'A101', 'capacity' => 30],
                ['room_number' => 'A102', 'capacity' => 30],
                ['room_number' => 'B201', 'capacity' => 25],
                ['room_number' => 'C301', 'capacity' => 40],
            ],
            'courses'     => [
                ['semester_name' => 'Fall 2025', 'department_code' => 'CS', 'instructor_email' => 'alice.smith@example.com', 'name' => 'Introduction to Programming', 'code' => 'CS101', 'description' => 'Basic programming concepts.', 'credits' => 3, 'max_students' => 30, 'start_date' => '2025-09-01', 'end_date' => '2025-12-15', 'status' => 'active'],
                ['semester_name' => 'Fall 2025', 'department_code' => 'CS', 'instructor_email' => 'alice.smith@example.com', 'name' => 'Data Structures', 'code' => 'CS201', 'description' => 'Advanced data structures.', 'credits' => 4, 'max_students' => 25, 'start_date' => '2025-09-01', 'end_date' => '2025-12-15', 'status' => 'active'],
                ['semester_name' => 'Fall 2025', 'department_code' => 'MATH', 'instructor_email' => 'bob.johnson@example.com', 'name' => 'Calculus I', 'code' => 'MA101', 'description' => 'Introduction to differential calculus.', 'credits' => 3, 'max_students' => 40, 'start_date' => '2025-09-01', 'end_date' => '2025-12-15', 'status' => 'active'],
            ],
            'schedules'   => [
                ['course_code' => 'CS101', 'professor_email' => 'alice.smith@example.com', 'room_number' => 'A101', 'capacity' => 30, 'day_of_week' => 'Monday', 'start_time' => '09:00:00', 'end_time' => '10:00:00'],
                ['course_code' => 'CS101', 'professor_email' => 'alice.smith@example.com', 'room_number' => 'A102', 'capacity' => 30, 'day_of_week' => 'Wednesday', 'start_time' => '10:00:00', 'end_time' => '11:00:00'],
                ['course_code' => 'CS201', 'professor_email' => 'alice.smith@example.com', 'room_number' => 'B201', 'capacity' => 25, 'day_of_week' => 'Tuesday', 'start_time' => '11:00:00', 'end_time' => '12:30:00'],
                ['course_code' => 'MA101', 'professor_email' => 'bob.johnson@example.com', 'room_number' => 'C301', 'capacity' => 40, 'day_of_week' => 'Thursday', 'start_time' => '13:00:00', 'end_time' => '14:30:00'],
            ],
            'enrollments' => [
                ['student_email' => 'student1@example.com', 'schedule_key' => 'CS101_Monday', 'enrollment_date' => '2025-08-20'],
                ['student_email' => 'student2@example.com', 'schedule_key' => 'CS101_Monday', 'enrollment_date' => '2025-08-21'],
                ['student_email' => 'student3@example.com', 'schedule_key' => 'CS101_Monday', 'enrollment_date' => '2025-08-22'],
                ['student_email' => 'student4@example.com', 'schedule_key' => 'CS101_Monday', 'enrollment_date' => '2025-08-23'],
                ['student_email' => 'student1@example.com', 'schedule_key' => 'CS201_Tuesday', 'enrollment_date' => '2025-08-20'],
                ['student_email' => 'student2@example.com', 'schedule_key' => 'CS201_Tuesday', 'enrollment_date' => '2025-08-21'],
                ['student_email' => 'student3@example.com', 'schedule_key' => 'MA101_Thursday', 'enrollment_date' => '2025-08-22'],
            ],
        ];
    }
}
