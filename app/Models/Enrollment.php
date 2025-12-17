<?php

declare (strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_session_id',
        'final_grade',
        'grade_points',
        'grade_letter',
        'status',
        'enrollment_date',
    ];

    protected $cast = [
        'final_grade' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'enrollment_data' => 'datetime',
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
    public function classSession(): BelongsTo {
        return $this->belongsTo(ClassSession::class);
    }

    /**
     * Get the course through the class session.
     */
    public function getCourseAttribute() {
        return $this->classSession->course;
    }
}
