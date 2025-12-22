<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactDetail extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'contactable_id',
        'contactable_type',
        'phone',
        'emergency_name',
        'emergency_phone',
        'emergency_relation',
    ];

    public function contactable()
    {
        return $this->morphTo();
    }
}