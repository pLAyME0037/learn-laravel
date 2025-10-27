<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_schedule_id',
        'enrollment_date',
        'status',
    ];

    /**
     * Cast the enrollment_date attribute.
     */
    protected function enrollmentDate(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => new Carbon($value),
            set: fn (Carbon $value) => $value,
        );
    }

    /**
     * Get the student that owns the Enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the class schedule that the Enrollment belongs to.
     */
    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    /**
     * Get the course through the class schedule.
     */
    public function course(): HasOneThrough
    {
        return $this->hasOneThrough(Course::class, ClassSchedule::class,
            'id', // Foreign key on ClassSchedule table
            'id', // Foreign key on Course table
            'class_schedule_id', // Local key on Enrollment table
            'course_id' // Local key on ClassSchedule table
        );
    }

    /**
     * Get a human-readable enrollment status.
     */
    public function getEnrollmentStatusAttribute(): string
    {
        return match (strtolower($this->status)) {
            'enrolled' => 'Enrolled',
            'dropped' => 'Dropped',
            'completed' => 'Completed',
            default => 'Unknown',
        };
    }

    /**
     * Get a formatted enrollment date.
     */
    public function getFormattedEnrollmentDateAttribute(): string
    {
        return $this->enrollment_date->format('M d, Y');
    }

    /**
     * Scope a query to only include enrollments by a specific student.
     */
    public function scopeByStudent(Builder $query, int $studentId): void
    {
        $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include enrollments by a specific class schedule.
     */
    public function scopeByClassSchedule(Builder $query, int $classScheduleId): void
    {
        $query->where('class_schedule_id', $classScheduleId);
    }

    /**
     * Scope a query to filter for active enrollments.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'enrolled');
    }

    /**
     * Scope a query to filter for completed enrollments.
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', 'completed');
    }

    /**
     * Checks if the enrollment is active.
     */
    public function isEnrolled(): bool
    {
        return strtolower($this->status) === 'enrolled';
    }

    /**
     * Checks if the enrollment is completed.
     */
    public function isCompleted(): bool
    {
        return strtolower($this->status) === 'completed';
    }
}
