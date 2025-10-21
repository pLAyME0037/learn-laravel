<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'credits',
        'department_id',
        'program_id',
        'instructor_id',
        'max_students',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Get the department that owns the Course.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the program that the Course belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the user (instructor) for the Course.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * The prerequisites that belong to the Course.
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_id');
    }

    /**
     * The courses that this course is a prerequisite for.
     */
    public function prerequisiteFor(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'prerequisite_id', 'course_id');
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the students enrolled in the course.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'course_id', 'id');
    }

    /**
     * Get the class schedules for the course.
     */
    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    /**
     * Add a prerequisite course.
     */
    public function addPrerequisite(Course $prerequisite): void
    {
        $this->prerequisites()->attach($prerequisite->id);
    }

    /**
     * Remove a prerequisite course.
     */
    public function removePrerequisite(Course $prerequisite): void
    {
        $this->prerequisites()->detach($prerequisite->id);
    }

    /**
     * Check if this course is a prerequisite for another course.
     */
    public function isPrerequisiteFor(Course $course): bool
    {
        return $this->prerequisiteFor()->where('course_id', $course->id)->exists();
    }

    /**
     * Check if this course has a specific prerequisite.
     */
    public function hasPrerequisite(Course $prerequisite): bool
    {
        return $this->prerequisites()->where('prerequisite_id', $prerequisite->id)->exists();
    }

    /**
     * Calculate available seats in the course.
     */
    public function getAvailableSeats(): int
    {
        if ($this->max_students === null) {
            return -1; // Indicates unlimited seats
        }
        return $this->max_students - $this->enrollments()->count();
    }

    /**
     * Check if the course is full.
     */
    public function isFull(): bool
    {
        if ($this->max_students === null) {
            return false; // Unlimited seats
        }
        return $this->enrollments()->count() >= $this->max_students;
    }

    /**
     * Check if the course is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
