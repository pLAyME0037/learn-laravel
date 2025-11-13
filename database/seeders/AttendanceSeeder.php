<?php
namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Student; // Import ClassSchedule model
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courseIds        = Course::pluck('id')->toArray();
        $studentIds       = Student::pluck('id')->toArray();
        $classScheduleIds = ClassSchedule::pluck('id')->toArray();

        if (
            empty($courseIds)
            || empty($studentIds)
            || empty($classScheduleIds)
        ) {
            $this->command->warn(''
                . 'Some prerequisite data (Courses, Students, or Class Schedules) '
                . 'not found. Please seed them first.'
            );
            return;
        }

        $sampleScheduleIds = array_slice($classScheduleIds, 0, 5);

        // 1. Pre-fetch all necessary data in bulk to avoid N+1 queries.
        $classSchedules = ClassSchedule::whereIn('id', $sampleScheduleIds)->get();
        $statuses = ['present', 'absent', 'late']; // Updated to match migration enum
        $attendancesToCreate = [];
        $now = now();

        // 2. Handle missing schedules (Guard Clause / Early Return Principle).
        $foundScheduleIds   = $classSchedules->pluck('id')->all();
        $missingScheduleIds = array_diff($sampleScheduleIds, $foundScheduleIds);

        foreach ($missingScheduleIds as $missingId) {
            $this->command->warn(
                "Class schedule with ID {$missingId} not found. Skipping."
            );
        }

        // 3. Prepare all attendance records in memory without database interaction.
        // The nested loops remain as they represent the business logic of creating a
        // record for each student per schedule, but the body is a simple array push.
        foreach ($classSchedules as $classSchedule) {
            foreach ($studentIds as $studentId) {
                $attendancesToCreate[] = [
                    'student_id'        => $studentId,
                    'class_schedule_id' => $classSchedule->id,
                    'date'              => $classSchedule->schedule_date ?? '2025-01-01',
                    'status'            => $statuses[array_rand($statuses)],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        // 4. Perform a single, efficient batch insert into the database.
        if (! empty($attendancesToCreate)) {
            Attendance::insert($attendancesToCreate);
        }
    }
}
