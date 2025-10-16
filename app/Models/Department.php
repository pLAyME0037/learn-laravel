<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'hod_id',
        'email',
        'phone',
        'office_location',
        'established_year',
        'budget',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'budget' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relationship
    public function hod() {
        return $this->belongsTo(User::class, 'hod_id');
    }

    public function users() {
        return $this->hasMany(User::class);
    }

    public function programs() {
        return $this->hasMany(Program::class);
    }

    public function students() {
        return $this->hasMany(Student::class);
    }

    // Scopes
    public function scopeActive($query) {
        return $query->where('is_active', true);
    }
    public function scopeWithHod($query) {
        return $query->with('hod');
    }
    public function scopeSearch($query, $search) {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
        ;
    }

    // Accessors
    public function getStaffCountAttribute() {
        return $this->users()->staff()->count();
    }
    public function getStudentCountAttribute() {
        return $this->users()->students()->count();
    }
    public function getProgramCountAttribute() {
        return $this->users()->programs()->count();
    }

    // Helper Methods
    public function canDelete() {
        return $this->users_count === 0 && $this->program_count === 0;
    }
}
