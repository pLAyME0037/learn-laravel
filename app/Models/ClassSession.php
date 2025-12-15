<?php

declare (strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClassSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'semester_id',
        'instructor_id',
        'classroom_id', // Optional: link to Room model if you have one
        'section_name', // 'A', 'B'
        'capacity',
        'day_of_week', // 'Mon', 'Tue'
        'start_time',
        'end_time',
        'status', // 'open', 'closed', 'cancelled'
    ];

    // --- Relationships ---

    public function course() {
        return $this->belongsTo(Course::class);
    }
    public function semester() {
        return $this->belongsTo(Semester::class);
    }
    public function instructor() {
        return $this->belongsTo(User::class, 'instructor_id'); 
    }
    public function classroom() {
        return $this->belongsTo(Classroom::class);
    }
    public function enrollments() {
        return $this->hasMany(Enrollment::class);
    }

    // --- Accessors ---

    public function getScheduleLabelAttribute()
    {
        return "{$this->day_of_week} " . 
               substr($this->start_time, 0, 5) . '-' . substr($this->end_time, 0, 5);
    }

    public function getEnrolledCountAttribute()
    {
        return $this->enrollments()->where('status', 'enrolled')->count();
    }

    public function getIsFullAttribute()
    {
        return $this->enrolled_count >= $this->capacity;
    }

    /**
     * Logic to prevent Double Booking for ClassSession.
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
     * Get the dropdown label for the class session.
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
}
