<?php
namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Student;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

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
        $studentIds      = Student::pluck('id')->toArray();
        $classSessionIds = ClassSession::pluck('id')->toArray();
        $now             = now();

        if (empty($studentIds)) {
            $this->command->warn('No students found. Please seed students first.');
            return;
        }

        if (empty($classSessionIds)) {
            $this->command->warn('No class sessions found. Please seed class sessions first.');
            return;
        }

        $enrollmentsToCreate      = [];
        $maxEnrollmentsPerStudent = 3; // Each student can be enrolled in up to 3 classes

        foreach ($studentIds as $studentId) {
            $numEnrollments   = $faker->numberBetween(1, $maxEnrollmentsPerStudent);
            $selectedSessions = $faker->randomElements($classSessionIds, $numEnrollments, false);

            foreach ($selectedSessions as $sessionId) {
                // Check if this student is already enrolled in this session
                // (simple check to prevent duplicate enrollments within this seeder run)
                if (in_array(['student_id' => $studentId, 'class_session_id' => $sessionId], $enrollmentsToCreate)) {
                    continue;
                }

                $enrollmentsToCreate[] = [
                    'student_id'       => $studentId,
                    'class_session_id' => $sessionId,
                    'enrollment_date'  => $faker->dateTimeBetween('-1 year', 'now'),
                    'status'           => $faker->randomElement(['enrolled', 'completed', 'withdrawn']),
                    'course_id'        => ClassSession::find($sessionId)->course_id,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
        }

        foreach (array_chunk($enrollmentsToCreate, 500) as $chunk) {
            Enrollment::insert($chunk);
        }

        $this->command->info(count($enrollmentsToCreate) . ' enrollments have been created.');
    }
}
