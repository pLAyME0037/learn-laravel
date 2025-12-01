<?php

namespace Database\Seeders;

use App\Models\ClassSchedule;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Faker $faker
     * @return void
     */
    public function run(Faker $faker): void
    {
        $studentIds = Student::pluck('id')->toArray();
        $classScheduleIds = ClassSchedule::pluck('id')->toArray();
        $now = now();

        if (empty($studentIds)) {
            $this->command->warn('No students found. Please seed students first.');
            return;
        }

        if (empty($classScheduleIds)) {
            $this->command->warn('No class schedules found. Please seed class schedules first.');
            return;
        }

        $enrollmentsToCreate = [];
        $maxEnrollmentsPerStudent = 3; // Each student can be enrolled in up to 3 classes

        foreach ($studentIds as $studentId) {
            $numEnrollments = $faker->numberBetween(1, $maxEnrollmentsPerStudent);
            $selectedSchedules = $faker->randomElements($classScheduleIds, $numEnrollments, false); // unique schedules

            foreach ($selectedSchedules as $scheduleId) {
                // Check if this student is already enrolled in this schedule
                // (simple check to prevent duplicate enrollments within this seeder run)
                if (in_array(['student_id' => $studentId, 'class_schedule_id' => $scheduleId], $enrollmentsToCreate)) {
                    continue;
                }

                $enrollmentsToCreate[] = [
                    'student_id'        => $studentId,
                    'class_schedule_id' => $scheduleId,
                    'enrollment_date'   => $faker->dateTimeBetween('-1 year', 'now'),
                    'status'            => $faker->randomElement(['enrolled', 'completed', 'withdrawn']),
                    // 'grade'             => $faker->randomElement(['A', 'B', 'C', 'D', 'F', null]), // Removed as 'grade' column does not exist
                    'course_id'         => ClassSchedule::find($scheduleId)->course_id, // Fetch course_id from class schedule
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        foreach (array_chunk($enrollmentsToCreate, 500) as $chunk) {
            Enrollment::insert($chunk);
        }

        $this->command->info(count($enrollmentsToCreate) . ' enrollments have been created.');
    }
}
