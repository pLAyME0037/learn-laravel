<?php

declare (strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Village;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'current_address',
        'city',
        'district',
        'commune',
        'village',
        'postal_code',
        'addressable_id',
        'addressable_type',
        'village_id',
    ];

    /**
     * Get the parent addressable model (User, Student, or Instructor).
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * Get the village associated with the address.
     */
    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }
}
