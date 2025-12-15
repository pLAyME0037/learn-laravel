<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model {
    protected $fillable = ['faculty_id', 'name', 'code', 'contact_info'];
    protected $casts = ['contact_info' => 'array'];

    public function faculty() { return $this->belongsTo(Faculty::class); }
    public function majors() { return $this->hasMany(Major::class); }
}
