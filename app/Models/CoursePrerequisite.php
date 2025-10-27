<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CoursePrerequisite extends Model
{
    protected $fillable = [
        'course_id',
        'prerequisite_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function prerequisite()
    {
        return $this->belongsTo(Course::class, 'prerequisite_id');
    }

    /**
     * Scope a query to only include prerequisites for a specific course.
     */
    public function scopeForCourse(Builder $query, int $courseId): void
    {
        $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to only include where a specific course is a prerequisite.
     */
    public function scopeIsPrerequisite(Builder $query, int $prerequisiteId): void
    {
        $query->where('prerequisite_id', $prerequisiteId);
    }
}
