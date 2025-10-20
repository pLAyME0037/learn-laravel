<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'department_id',
        'name',
        'code',
        'description',
        'level',
        'duration_years',
        'total_semesters',
        'total_credits_required',
        'tuition_fee',
        'curriculum',
        'is_active',
    ];

    protected $casts = [
        'tuition_fee' => 'decimal:2',
        'curriculum'  => 'array',
        'is_active'   => 'boolean',
    ];

    // Relationship
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function students()
    {
        return $this->belongsTo(Student::class);
    }
    public function courses()
    {
        return $this->belongsTo(Course::class);
    }

    // Accessors
    public function getStudentCountAttribute()
    {
        return $this->students->count();
    }
    public function getActiveStudentCountAttribute()
    {
        return $this->students->active()->count();
    }
    public function getFullNameAttribute()
    {
        return "{$this->name} - {$this->code}";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // Helper Methods
    public function canDelete()
    {
        return $this->students_count === 0;
    }
}
