<?php
namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing attendance records to prevent duplicates on re-seeding
        DB::table('attendances')->truncate();

        $enrollments = Enrollment::select('student_id', 'class_schdule_id')->get();

        // Ensure there are users and class schedules to link to
        if ($enrollments->isEmpty()) {
            $this->command->warn('No enrollments found. Run AcademicYearDashboardSeeder first.');
            return;
        }

        $data     = [];
        $now      = now();
        $statuses = ['present', 'present', 'present', 'absent', 'late'];

        foreach ($enrollments as $enrollment) {
            if (! $enrollment->class_schedule_id || ! $enrollment->student_id) {
                continue;
            }
            for ($i = 0; $i < 5; $i++) {
                $data[] = [
                    'student_id'        => $enrollment->student_id,
                    'class_schedule_id' => $enrollment->class_schedule_id,
                    'status'            => $statuses[array_rand($statuses)],
                    'date'              => $now->copy()->subDays($i * 7), // Once a week
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        foreach (array_chunk($data, 500) as $chunk) {
            Attendance::insert($chunk);
        }
    }
}
