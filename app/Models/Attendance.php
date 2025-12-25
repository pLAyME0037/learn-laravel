<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['enrollment_id', 'date', 'status', 'remarks'];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
    
    // Helper to get Label from Dictionary
    public function getStatusLabelAttribute()
    {
        return Dictionary::label('attendance_status', $this->status_code);
    }
}