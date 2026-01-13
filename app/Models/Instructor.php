<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'staff_id', // e.g. STF-005
        'attributes', // JSON
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class, 'instructor_id', 'user_id');
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function contactDetail()
    {
        return $this->morphOne(ContactDetail::class, 'contactable');
    }
    
    public function getOfficeHoursAttribute()
    {
        // Collect all class times
        return $this->classSessions->map(function($session) {
            return "{$session->day_of_week} {$session->start_time}-{$session->end_time}";
        })->join(', ');
    }
}