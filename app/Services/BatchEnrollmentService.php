<?php
namespace App\Services;

use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BatchEnrollmentService
{
    /**
     * Analyze what WOULD happen if we enrolled this cohort.
     */
    public function previewChanges(Collection $students, Collection $classSessions): array
    {
        $analysis = [
            // 'total_students' => $students->count(),
            // 'total_classes'  => $classSessions->count(),
            'to_create'      => 0,
            'duplicates'     => 0,
            'conflicts'      => 0,
        ];

        $existingEnrollments = Enrollment::query()
            ->whereIn('student_id', $students->pluck('id'))
            ->whereIn('class_session_id', $classSessions->pluck('id'))
            ->get()
            ->mapWithKeys(fn($e) => ["{$e->student_id}-{$e->class_session_id}" => true]);

        $studentSchedules = Enrollment::query()
            ->whereIn('student_id', $students->pluck('id'))
            ->with('classSession')
            ->get()
            ->groupBy('student_id')
            ->map(fn($e) => $e->pluck('classSession'));

        foreach ($students as $student) {
            $currentSchedule = $studentSchedules->get($student->id, collect());

            foreach ($classSessions as $session) {
                $key = "{$student->id}-{$session->id}";

                if ($existingEnrollments->has($key)) {
                    $analysis['duplicates']++;
                    continue;
                }

                $hasConflict = $currentSchedule->contains(fn($existSes) =>
                    $existSes
                    && $existSes->day_of_week === $session->day_of_week
                    && $existSes->start_time < $session->end_time
                    && $existSes->end_time > $session->start_time
                );
                if ($hasConflict) {
                    $analysis['conflicts']++;
                } else {
                    $analysis['to_create']++;
                }
            }
        }

        return $analysis;
    }

    /**
     * DANGER: Remove all enrollments for a cohort in a specific semester.
     */
    public function rollbackCohort(
        int $programId,
        int $currentTerm,
        int $semesterId
    ): int {
        return DB::transaction(function () use (
            $programId,
            $currentTerm,
            $semesterId,
        ) {
            // 1. Find Students
            $studentIds = Student::where('program_id', $programId)
                ->where('current_term', $currentTerm)
                ->pluck('id');

            if ($studentIds->isEmpty()) {
                return 0;
            }

            // 2. Delete Enrollments for this Semester
            $deleted = Enrollment::whereIn('student_id', $studentIds)
                ->whereHas('classSession', fn($q) =>
                    $q->where('semester_id', $semesterId))
                ->delete();

            Log::warning(""
            . "Batch Rollback: Deleted {$deleted} "
            . "enrollments for Program {$programId}, Term {$currentTerm}"
            );

            return $deleted;
        });
    }

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
