<?php
namespace App\Services;

use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class GradingService
{
    /**
     * Convert raw score (0-100) to Grade Point (0-4.0) and Letter.
     */
    public function calculateGrade($score)
    {
        if ($score >= 85) {
            return ['letter' => 'A', 'point' => 4.00, 'status' => 'completed'];
        }

        if ($score >= 80) {
            return ['letter' => 'B+', 'point' => 3.50, 'status' => 'completed'];
        }

        if ($score >= 70) {
            return ['letter' => 'B', 'point' => 3.00, 'status' => 'completed'];
        }

        if ($score >= 65) {
            return ['letter' => 'C+', 'point' => 2.50, 'status' => 'completed'];
        }

        if ($score >= 50) {
            return ['letter' => 'C', 'point' => 2.00, 'status' => 'completed'];
        }

        if ($score >= 45) {
            return ['letter' => 'D', 'point' => 1.50, 'status' => 'completed'];
        }
        // Probationary Pass
        if ($score >= 40) {
            return ['letter' => 'E', 'point' => 1.00, 'status' => 'completed'];
        }

        return ['letter' => 'F', 'point' => 0.00, 'status' => 'failed'];
    }

    /**
     * Submit a grade for an enrollment and recalculate student CGPA.
     */
    public function submitGrade(Enrollment $enrollment, $score)
    {
        $result = $this->calculateGrade($score);

        DB::transaction(function () use ($enrollment, $score, $result) {

            // 1. Update the Enrollment Record
            $enrollment->update([
                'final_grade'  => $score,
                'grade_letter' => $result['letter'],
                'grade_points' => $result['point'],
                'status'       => $result['status'],
            ]);

            // 2. Recalculate CGPA for the student
            $this->updateStudentCGPA($enrollment->student);
        });
    }

    /**
     * Recalculate Cumulative GPA based on all completed courses.
     * Formula: Sum(GradePoints * CourseCredits) / Sum(CourseCredits)
     */
    public function updateStudentCGPA(Student $student)
    {
        $enrollments = $student->enrollments()
            ->whereIn('status', ['completed', 'failed']) // Include Fails in GPA? Usually yes.
            ->whereNotNull('grade_points')
            ->with('classSession.course')
            ->get();

        if ($enrollments->isEmpty()) {
            return;
        }

        $totalPoints  = 0;
        $totalCredits = 0;

        foreach ($enrollments as $record) {
            $credits = $record->classSession->course->credits;

            // Basic GPA Math
            $totalPoints += ($record->grade_points * $credits);
            $totalCredits += $credits;
        }

        $cgpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.00;

        $student->update(['cgpa' => $cgpa]);
    }
}
