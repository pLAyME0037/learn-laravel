<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'professor_id',
        'room_number',
        'capacity',
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
}
