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
            ->whereIn('status', ['completed', 'failed'])
            ->whereNotNull('grade_points')
            ->with('classSession.course')
            ->get();

        if ($enrollments->isEmpty()) {
            return;
        }

        $totalPoints = 0;
        $totalCreditsAttempted = 0; // For GPA calculation
        $totalCreditsEarned = 0;    // For Graduation requirements

        foreach ($enrollments as $record) {
            $credits = $record->classSession->course->credits;
            
            // GPA Math
            $totalPoints += ($record->grade_points * $credits);
            $totalCreditsAttempted += $credits;

            // Earned Credits Logic: Only count if they passed (Grade Point > 0 or specific logic)
            // Assuming 'F' = 0.00 points
            if ($record->grade_points > 0) {
                $totalCreditsEarned += $credits;
            }
        }

        $cgpa = $totalCreditsAttempted > 0 ? round($totalPoints / $totalCreditsAttempted, 2) : 0.00;

        // Update Student
        $student->update([
            'cgpa' => $cgpa,
            'total_credits_earned' => $totalCreditsEarned // <--- Saving it here
        ]);
    }
}
