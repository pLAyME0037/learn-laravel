<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'credits',
        'description',
        'department_id',
    ];

    // --- Relationships ---

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Courses that MUST be taken before this one.
     * (e.g. If this is Calculus II, this returns Calculus I)
     */
    public function prerequisites()
    {
        return $this->belongsToMany(
            Course::class, 
            'course_prerequisites', 
            'course_id', 
            'prerequisite_course_id'
        )->withTimestamps();
    }

    /**
     * Courses that require THIS course.
     * (e.g. If this is Calculus I, this returns Calculus II)
     */
    public function dependentCourses()
    {
        return $this->belongsToMany(
            Course::class, 
            'course_prerequisites', 
            'prerequisite_course_id', 
            'course_id'
        )->withTimestamps();
    }

    public function programStructures()
    {
        return $this->hasMany(programStructure::class);
    }

    // Used for class scheduling
    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    // Helper: "CS101 - Intro to CS"
    public function getFullNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }
}