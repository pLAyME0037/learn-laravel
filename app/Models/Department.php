<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name'
        , 'code'
        , 'description'
        , 'email'
        , 'phone'
        , 'office_location'
        , 'head_of_department_id'
        , 'founded_year'
        , 'budget'
        , 'total_faculty'
        , 'total_students'
        , 'website'
        , 'contact_info'
        , 'is_active'
        , 'display_order'
        ,
    ];

    protected $cast = [
        'contact_info' => 'array'
        , 'founded_year' => 'dicimal:2'
        , 'total_faculty' => 'boolean'
        , 'total_students' => 'integer'
        , 'display_order' => 'integer'
        , 'budget' => 'integer'
        , 'is_active' => 'integer'
        ,
    ];

    // relationship
    public function headOfDepartment()
    {
        return $this->belongsTo(User::class, 'head_of_department_id');
    }
    public function faculty()
    {
        return $this->hasMany(User::class)->whereHas('role', function ($query) {
            $query->whereIn('slug', ['professor', 'HOD']);
        });
    }
    public function programs()
    {
        return $this->hasMany(Program::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // accessers
    public function getFormattedBudgetAttribute()
    {
        return '$' . number_format($this->budget, 2);
    }
    public function getYearActiveAttribute()
    {
        return $this->founded_year
            ? now()->year - $this->founded_year
            : 0
        ;
    }
    public function getFacultyCountAttribute()
    {
        return $this->faculty()->count();
    }
    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }
    public function getActiveProgramsCountAttribute()
    {
        return $this->programs()
            ->where('is_active', true)
            ->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeWithFaculty($query)
    {
        return $query->whereHas('faculty');
    }
    public function scopeWithSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('decription', 'like', "%{$search}%");
        });
    }
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    // methods
    public function canBeDeleted()
    {
        return $this->faculty_count === 0
        && $this->student_count === 0
        && $this->active_programs_count === 0;
    }
    public function getStatusBadgeClass()
    {
        return $this->is_active
            ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
            : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
    }
    public function getStatusText() {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}
