<?php
namespace App\Services;

use App\Models\Enrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BatchEnrollmentService
{
    public function enrollCohort(Collection $students, Collection $classSessions)
    {
        return DB::transaction(function () use ($students, $classSessions) {
            $count = 0;
            $now   = now();

            foreach ($students as $student) {
                // Skip if not active
                if ($student->academic_status !== 'active') {
                    continue;
                }

                foreach ($classSessions as $session) {
                    // Check if already enrolled to avoid crashing
                    $exists = Enrollment::where('student_id', $student->id)
                        ->where('class_session_id', $session->id)
                        ->exists();

                    if (! $exists) {
                        Enrollment::create([
                            'student_id'       => $student->id,
                            'class_session_id' => $session->id,
                            'status'           => 'enrolled',
                            'enrollment_date'  => $now,
                        ]);
                        $count++;
                    }
                }
            }
            Log::info(""
            . "Batch Enrollment: Enrolled {$students->count()} students into "
            . "{$classSessions->count()} classes. Total records: $count"
            );
            return $count;
        });
    }
}
