<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class ClassSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'professor_id',
        'classroom_id',
        'capacity',
        'schedule_date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the classroom associated with the class schedule.
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id'); // Updated to use classroom_id
    }

    /**
     * Get a boolean indicating if the class schedule has reached its capacity.
     */
    public function getIsFullAttribute(): bool
    {
        return $this->currentEnrollmentCount() >= $this->capacity;
    }

    /**
     * Scope a query to only include class schedules by a specific course.
     */
    public function scopeByCourse(Builder $query, int $courseId): void
    {
        $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to only include class schedules by a specific professor.
     */
    public function scopeByProfessor(Builder $query, int $professorId): void
    {
        $query->where('professor_id', $professorId);
    }


    /**
     * Scope a query to filter for schedules that still have available capacity.
     */
    public function scopeAvailableCapacity(Builder $query): void
    {
        $query->whereColumn('capacity', '>', function ($subQuery) {
            $subQuery->selectRaw('count(*)')
                     ->from('enrollments')
                     ->whereColumn('class_schedule_id', 'class_schedules.id');
        });
    }

    /**
     * Checks if there is still capacity in the class.
     */
    public function hasAvailableCapacity(): bool
    {
        return $this->currentEnrollmentCount() < $this->capacity;
    }

    /**
     * Returns the number of students currently enrolled.
     */
    public function currentEnrollmentCount(): int
    {
        return $this->enrollments()->count();
    }
}
