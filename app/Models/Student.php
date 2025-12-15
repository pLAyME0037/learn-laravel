<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'program_id',
        'student_id', // Generated ID e.g. STU-2025-001
        'year_level',
        'current_term',
        'cgpa',
        'academic_status',
        'attributes',     // JSON: { blood_group, nationality, dob... }
        'sensitive_data', // Encrypted JSON: { id_card, passport... }
    ];

    protected $casts = [
        'attributes' => 'array',
        'cgpa'       => 'decimal:2',
    ];

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Polymorphic relation to Address
    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    // Polymorphic relation to Contact Detail
    public function contactDetail()
    {
        return $this->morphOne(ContactDetail::class, 'contactable');
    }

    // --- Accessors & Mutators ---

    /**
     * Handle Encrypted Sensitive Data automatically.
     */
    protected function sensitiveData(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? json_decode(Crypt::decryptString($value), true) : [],
            set: fn($value) => Crypt::encryptString(json_encode($value))
        );
    }

    public function scopeApplyFilters($query, array $filters)
    {
        // 1. Eager Load
        $query->with(['user', 'program.major.department', 'address.village']);

        // 2. Status / Trash
        $query->when($filters['academic_status'] ?? null, function ($q, $status) {
            if ($status === 'trashed') {
                $q->onlyTrashed();
            } else {
                $q->where('academic_status', $status);
            }
        });

        // 3. Department (New Schema: Student -> Program -> Major -> Department)
        $query->when($filters['department_id'] ?? null, function ($q, $deptId) {
            $q->whereHas('program.major', fn($m) => $m->where('department_id', $deptId));
        });

        // 4. Program
        $query->when($filters['program_id'] ?? null, fn($q, $id) => $q->where('program_id', $id));

        // 5. Search
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(fn($sub) =>
                $sub->where('student_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) =>
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                    )
            );
        });

        return $query;
    }

    // Helper to get Department via Program
    public function getDepartmentAttribute()
    {
        return $this->program?->major?->department;
    }

    public function generateStudentId(?int $departmentId = null): string
    {
        // Get department code. If departmentId is provided, use it to fetch the department.
        // Otherwise, assume $this->department_id is available (though it might not be set on a new instance).
        $departmentCode = 'GEN';
        if ($departmentId) {
            $department     = Department::find($departmentId);
            $departmentCode = (string) ($department->code ?? 'GEN');
        } elseif ($this->department_id) {
            $department     = Department::find($this->department_id);
            $departmentCode = (string) ($department->code ?? 'GEN');
        }

        $year = now()->format('y');

        $sequence = Student::where('department_id', $departmentId ?? $this->department_id)
            ->whereYear('created_at', now()->year)
            ->count() + 1;

        return $departmentCode
        . $year
        . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
