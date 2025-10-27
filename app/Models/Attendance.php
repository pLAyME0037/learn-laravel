<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'course_id',
        'date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_number', 'code');
    }

    /**
     * Get a human-readable attendance status.
     */
    public function getAttendanceStatusAttribute(): string
    {
        return match (strtolower($this->status)) {
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            default => 'Unknown',
        };
    }

    /**
     * Scope a query to only include attendance records by a specific student.
     */
    public function scopeByStudent(Builder $query, int $studentId): void
    {
        $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include attendance records by a specific course.
     */
    public function scopeByCourse(Builder $query, int $courseId): void
    {
        $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to only include attendance records by a specific date.
     */
    public function scopeByDate(Builder $query, string $date): void
    {
        $query->whereDate('date', $date);
    }

    /**
     * Scope a query to only include present students.
     */
    public function scopePresent(Builder $query): void
    {
        $query->where('status', 'present');
    }

    /**
     * Scope a query to only include absent students.
     */
    public function scopeAbsent(Builder $query): void
    {
        $query->where('status', 'absent');
    }

    /**
     * Checks if the attendance status is 'present'.
     */
    public function isPresent(): bool
    {
        return strtolower($this->status) === 'present';
    }

    /**
     * Checks if the attendance status is 'absent'.
     */
    public function isAbsent(): bool
    {
        return strtolower($this->status) === 'absent';
    }
}
