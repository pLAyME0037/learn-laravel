<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Course extends Model
{
    use HasFactory, SoftDeletes;

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
        'max_students',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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
     * The prerequisites that belong to the Course.
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_id');
    }

    /**
     * The courses that this course is a prerequisite for.
     */
    public function prerequisiteForCourses(): BelongsToMany
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
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments', 'course_id', 'student_id');
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class, 'course_instructor_pivot', 'course_id', 'instructor_id');
    }

    /**
     * Get the class schedules for the course.
     */
    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    /**
     * Get the semester that the Course belongs to.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Course $course) {
            // Detach prerequisites (from pivot table)
            $course->prerequisites()->detach();
            $course->prerequisiteForCourses()->detach(); // Also detach where this course is a prerequisite

            // Detach instructors (from pivot table)
            $course->instructors()->detach();
        });
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
     * Get a human-readable status for the course.
     */
    public function getCourseStatusAttribute(): string
    {
        $now = Carbon::now();
        if ($this->start_date->isFuture()) {
            return 'Upcoming';
        } elseif ($this->end_date->isPast()) {
            return 'Completed';
        } elseif ($this->start_date->isPast() && $this->end_date->isFuture()) {
            return 'Active';
        }
        return 'Inactive';
    }

    /**
     * Get a formatted string for start and end dates.
     */
    public function getFormattedDatesAttribute(): string
    {
        return "{$this->start_date->format('M d, Y')} - {$this->end_date->format('M d, Y')}";
    }

    /**
     * Scope a query to only include active courses.
     */
    public function scopeActive(Builder $query): void
    {
        $now = Carbon::now();
        $query->where('start_date', '<=', $now)
              ->where('end_date', '>=', $now);
    }

    /**
     * Scope a query to only include courses that haven't started yet.
     */
    public function scopeUpcoming(Builder $query): void
    {
        $query->where('start_date', '>', Carbon::now());
    }

    /**
     * Scope a query to only include courses by a specific department.
     */
    public function scopeByDepartment(Builder $query, int $departmentId): void
    {
        $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to only include courses by a specific program.
     */
    public function scopeByProgram(Builder $query, int $programId): void
    {
        $query->where('program_id', $programId);
    }

    /**
     * Scope a query to filter for courses with available seats.
     */
    public function scopeHasCapacity(Builder $query): void
    {
        $query->whereColumn('max_students', '>', function ($subQuery) {
            $subQuery->selectRaw('count(*)')
                     ->from('enrollments')
                     ->whereColumn('course_id', 'courses.id');
        })->orWhereNull('max_students');
    }

    /**
     * Returns the count of enrolled students.
     */
    public function getEnrolledStudentsCount(): int
    {
        return $this->enrollments()->count();
    }

    /**
     * Checks if a student can enroll (e.g., not full, active, meets prerequisites).
     * This method would typically take a Student model as an argument for prerequisite checks.
     * For simplicity, this version only checks if the course is active and not full.
     */
    public function canEnroll(): bool
    {
        return $this->getCourseStatusAttribute() === 'Active' && !$this->isFull();
    }
}
