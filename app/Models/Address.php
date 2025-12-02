<?php

declare (strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The primary key for the model.
     *
     * @var string
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'current_address',
        'permanent_address',
        'city',
        'district',
        'commune',
        'village',
        'postal_code',
        'user_id',
        'student_id',
        'instructor_id',
    ];

    /**
     * Get the user that owns the contact detail.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student that owns the contact detail.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the instructor that owns the contact detail.
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
