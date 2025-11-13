<?php
namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Instructor;
use Faker\Generator as Faker; // Import Carbon
use Illuminate\Database\Seeder;

// Import Faker

class ClassScheduleSeeder extends Seeder
{
/**
 * Run the database seeds.
 *
 * This seeder is optimized to prevent N+1 query issues and uses a single
 * batch insert for performance.
 */
    public function run(Faker $faker): void
    {
        // 1. EFFICIENT DATA FETCHING & MAPPING
        // Fetch all necessary data from the database in a few queries,
        // and map them by their codes for fast lookups in memory.
        $courseCodeToIdMap = Course::pluck('id', 'code');
        // Instructors do not have a 'code' column. We will pluck by 'id' and handle instructor assignment differently.
        $instructorIdToUserIdMap = Instructor::pluck('user_id', 'id');
        $allClassroomIds         = Classroom::pluck('id')->all();
        $allInstructorUserIds    = $instructorIdToUserIdMap->values()->all(); // Get all available instructor user_ids

        // 2. GUARD CLAUSES (EARLY RETURN)
        // Ensure that the foundational data exists before proceeding.
        if ($courseCodeToIdMap->isEmpty()) {
            $this->command->warn('No courses found. Please seed them first.');
            return;
        }
        if (empty($allInstructorUserIds)) {
            $this->command->warn('No instructors found. Please seed them first.');
            return;
        }
        if (empty($allClassroomIds)) {
            $this->command->warn('No classrooms found. Please seed them first.');
            return;
        }

        // Define sample class schedules data
        $schedulesData     = $this->getSchedulesData();
        $schedulesToCreate = [];
        $now               = now();

        // 3. DATA PREPARATION (IN MEMORY)
        // Loop through the static data and prepare an array for a batch insert.
        // All database interactions are replaced with fast array lookups.
        foreach ($schedulesData as $scheduleData) {
            // Validate that the course code from our array exists in the database map.
            if (! $courseCodeToIdMap->has($scheduleData['course_code'])) {
                $this->command->warn(''
                    . "Course with code '{$scheduleData['course_code']}' not found. Skipping schedule."
                );
                continue; // Skip this iteration
            }

            $courseId = $courseCodeToIdMap[$scheduleData['course_code']];

            // Determine instructor ID. Since instructors don't have a 'code', we'll assign a random one
            // if 'instructor_code' is provided in the static data, or if it's null.
            $instructorUserId = $allInstructorUserIds[array_rand($allInstructorUserIds)];

            // Pick a random classroom ID from the pre-fetched array.
            $classroomId = $allClassroomIds[array_rand($allClassroomIds)];

            // If a specified instructor code was not found, we can assign a random one or skip.
            // Here, we'll assign a random one to ensure data is seeded.
            if (! $instructorUserId) {
                $this->command->warn(''
                    . "Instructor '{$scheduleData['instructor_code']}' not found. Assigning a random one."
                );
                $instructorUserId = $allInstructorUserIds[array_rand($allInstructorUserIds)];
            }

            $schedulesToCreate[] = [
                'course_id'     => $courseId,
                'professor_id'  => $instructorUserId,
                'classroom_id'  => $classroomId,
                'capacity'      => $scheduleData['capacity'],
                'day_of_week'   => $scheduleData['day_of_week'],
                'schedule_date' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                'start_time'    => $scheduleData['start_time'],
                'end_time'      => $scheduleData['end_time'],
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // 4. BATCH INSERT
        // Perform a single, highly efficient database insert for all prepared records.
        // We chunk the inserts to avoid hitting query size limits with very large datasets.
        foreach (array_chunk($schedulesToCreate, 500) as $chunk) {
            ClassSchedule::insert($chunk);
        }

        $this->command->info(count(
            $schedulesToCreate
        ) . ' class schedules have been created.'
        );
    }

    /**
     * Provides the static data for class schedules.
     *
     * @return array
     */
    private function getSchedulesData(): array
    {
        return [
            // Your schedule data array remains the same...
            [
                'course_code'     => 'CS101',
                'instructor_code' => null,
                'day_of_week'     => 'Monday',
                'start_time'      => '09:00:00',
                'end_time'        => '10:30:00',
                'capacity'        => 50,
            ],
            [
                'course_code'     => 'CS101',
                'instructor_code' => null,
                'day_of_week'     => 'Wednesday',
                'start_time'      => '11:00:00',
                'end_time'        => '12:30:00',
                'capacity'        => 50,
            ],
            [
                'course_code'     => 'CS201',
                'instructor_code' => null,
                'day_of_week'     => 'Tuesday',
                'start_time'      => '13:00:00',
                'end_time'        => '14:30:00',
                'capacity'        => 45,
            ],
            [
                'course_code'     => 'CS501',
                'instructor_code' => null,
                'day_of_week'     => 'Friday',
                'start_time'      => '10:00:00',
                'end_time'        => '11:30:00',
                'capacity'        => 30,
            ],
            [
                'course_code'     => 'MATH101',
                'instructor_code' => null,
                'day_of_week'     => 'Monday',
                'start_time'      => '09:00:00',
                'end_time'        => '10:30:00',
                'capacity'        => 60,
            ],
            [
                'course_code'     => 'MATH201',
                'instructor_code' => null,
                'day_of_week'     => 'Thursday',
                'start_time'      => '11:00:00',
                'end_time'        => '12:30:00',
                'capacity'        => 55,
            ],
            [
                'course_code'     => 'EE201',
                'instructor_code' => null,
                'day_of_week'     => 'Tuesday',
                'start_time'      => '13:00:00',
                'end_time'        => '14:30:00',
                'capacity'        => 40,
            ],
            [
                'course_code'     => 'EE301',
                'instructor_code' => null,
                'day_of_week'     => 'Thursday',
                'start_time'      => '14:00:00',
                'end_time'        => '15:30:00',
                'capacity'        => 35,
            ],
            [
                'course_code'     => 'MGMT101',
                'instructor_code' => null,
                'day_of_week'     => 'Wednesday',
                'start_time'      => '09:00:00',
                'end_time'        => '10:30:00',
                'capacity'        => 50,
            ],
        ];
    }
}
