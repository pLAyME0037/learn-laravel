<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
