<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
