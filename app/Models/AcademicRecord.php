<?php

declare (strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AcademicRecord extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'semester_id',
        'grade',
        'credits_earned',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the human-readable status for the grade.
     */
    public function getGradeStatusAttribute(): string
    {
        return match (strtoupper($this->grade)) {
            'A', 'B' => 'Excellent',
            'C'     => 'Pass',
            'D', 'F' => 'Fail',
            default => 'Unknown',
        };
    }

    /**
     * Scope a query to only include academic records by a specific student.
     */
    public function scopeByStudent(Builder $query, int $studentId): void
    {
        $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include academic records by a specific course.
     */
    public function scopeByCourse(Builder $query, int $courseId): void
    {
        $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to only include academic records by a specific semester.
     */
    public function scopeBySemester(Builder $query, int $semesterId): void
    {
        $query->where('semester_id', $semesterId);
    }

    /**
     * Scope a query to only include academic records where the grade indicates a pass.
     */
    public function scopePassed(Builder $query): void
    {
        $query->whereIn('grade', ['A', 'B', 'C']);
    }

    /**
     * Checks if the student passed the course based on the grade.
     */
    public function isPassed(): bool
    {
        return in_array(strtoupper($this->grade), ['A', 'B', 'C']);
    }
}
