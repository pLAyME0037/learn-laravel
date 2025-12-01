<?php

declare (strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClassSchedule extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (ClassSchedule $classSchedule) {
            // Soft delete related Enrollments
            $classSchedule->enrollments()->each(fn (Enrollment $enrollment) => $enrollment->delete());
            // Soft delete related Attendances
            $classSchedule->attendances()->each(fn (Attendance $attendance) => $attendance->delete());
        });
    }

    protected $fillable = [
        'course_id',
        'instructor_id',
        'classroom_id',
        'semester_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor() // Renamed from professor to instructor
    {
        return $this->belongsTo(Instructor::class); // Changed to Instructor::class
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
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
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    /**
     * Logic to prevent Double Booking for ClassSchedule.
     * 1. Room Conflict: Same Room + Same Time
     * 2. Instructor Conflict: Same Teacher + Same Time (Different Room)
     */
    public static function checkForConflicts(
        $roomId,
        $instructorId,
        $day,
        $start,
        $end,
        $semesterId,
        $ignoreId = null
    ): void {
        $query = static::where('semester_id', $semesterId)
            ->where('day_of_week', $day)
            ->where(function ($q) use ($roomId, $instructorId) {
                // Check if Room is busy OR Instructor is busy
                $q->where('classroom_id', $roomId)
                    ->orWhere('instructor_id', $instructorId);
            })
        // Time Intersection Formula: (StartA < EndB) and (EndA > StartB)
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                    ->where('end_time', '>', $start);
            });

        // If updating, exclude the current record from the check
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'start_time' => 'Time conflict detected! The Room or Instructor is already booked for this time slot.',
            ]);
        }
    }

    /**
     * Reusable Validation Rules for ClassSchedule.
     */
    public static function validateSchedule(Request $request): array
    {
        return $request->validate([
            'course_id'     => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:instructors,id',
            'classroom_id'  => 'required|exists:classrooms,id',
            'semester_id'   => 'required|exists:semesters,id',
            'day_of_week'   => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
        ]);
    }

    /**
     * Get the dropdown label for the class schedule.
     * Use in `enrollment/edit`
     */
    public function getDropdownLabelAttribute(): string
    {
        // 1. Truncate/Limit text so it doesn't break alignment
        // 2. Pad with standard spaces
        $course = str_pad(Str::limit($this->course_name, 20), 22, '_');
        $day    = str_pad(Str::limit($this->day_of_week, 10), 12, '_');
        $time   = $this->start_time;

        $string = $course . $day . $time;

        // 3. Replace temporary '_' with Non-Breaking Space entity
        // We use '_' first to visually see padding, then swap it.
        // Or simply pad with spaces and swap spaces to &nbsp;

        $courseRaw = str_pad(Str::limit($this->course_name, 40), 45);
        $dayRaw    = str_pad(Str::limit($this->day_of_week, 10), 15);

        $fullString = $courseRaw . $dayRaw . $time;

        return str_replace(' ', '&nbsp;', $fullString);
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
        $query->where('instructure_id', $professorId);
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
