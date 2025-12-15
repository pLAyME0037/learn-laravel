<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model {
    protected $fillable = ['department_id', 'degree_id', 'name', 'cost_per_term'];

    public function department() { return $this->belongsTo(Department::class); }
    public function degree() { return $this->belongsTo(Degree::class); }
    public function programs() { return $this->hasMany(Program::class); }
}
