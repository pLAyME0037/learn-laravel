<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePrerequisite extends Model
{
    protected $table = 'course_prerequisites';

    protected $fillable = [
        'course_id',
        'prerequisite_course_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function prerequisite()
    {
        return $this->belongsTo(Course::class, 'prerequisite_course_id');
    }
}