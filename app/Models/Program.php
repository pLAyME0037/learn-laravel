<?php

declare (strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model {
    protected $fillable = ['major_id', 'degree_id', 'name'];

    public function major() { return $this->belongsTo(Major::class); }
    public function degree() { return $this->belongsTo(Degree::class); }
    public function programStructures() { return $this->belongsTo(ProgramStructure::class); }
    // Helper to get full name like "Bachelor of Computer Science"
    public function getFullNameAttribute() { return $this->name; }
}
