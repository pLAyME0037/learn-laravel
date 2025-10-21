<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'department_id',
        'program_id',
        // Student Identification
        'student_id',
        'registration_number',
        // Personal Information
        'date_of_birth',
        'gender',
        'nationality',
        'id_card_number',
        'passport_number',
        // Contact Information
        'phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        // Address Information
        'current_address',
        'permanent_address',
        'city',
        'state',
        'country',
        'postal_code',
        // Academic Information
        'admission_date',
        'expected_graduation',
        'current_semester',
        'cgpa',
        'total_credits_earned',
        // Academic Status
        'academic_status',
        'enrollment_status',
        // Financial Information
        'fee_category',
        'has_outstanding_balance',
        // Additional Information
        'previous_education',
        'blood_group',
        'has_disability',
        'disability_details',
        'metadata',
    ];

    protected $casts = [
        'date_of_birth'           => 'date',
        'admission_date'          => 'date',
        'expected_graduation'     => 'date',
        'cgpa'                    => 'decimal:2',
        'has_outstanding_balance' => 'boolean',
        'has_disability'          => 'boolean',
        'metadata'                => 'array',
        'id_card_number'          => 'encrypted',
        'passport_number'         => 'encrypted',
        'emergency_contact_name'  => 'encrypted',
        'emergency_contact_phone' => 'encrypted',
        'permanent_address'       => 'encrypted',
        'disability_details'      => 'encrypted',
    ];

    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    public function academicRecords()
    {
        return $this->hasMany(AcademicRecord::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->user->name;
    }
    public function getEmailAttribute()
    {
        return $this->user->email;
    }
    public function getProfilePictureUrlAttribute()
    {
        return $this->user->profile_picture_url;
    }
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
    public function getAcademicProgressAttribute()
    {
        if (
            ! $this->program
            || $this->program->total_credits_required === 0
        ) {
            return ($this->total_credits_earned / $this->program->total_credits_required) * 100;
        }
    }
    public function getAcademicStanding()
    {
        if ($this->cgpa >= 3.5) {
            return 'Excellent';
        }

        if ($this->cgpa >= 3.0) {
            return 'Good';
        }

        if ($this->cgpa >= 2.0) {
            return 'Satisfatory';
        }

        return 'Probation';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('academic_status', 'active');
    }
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('student_id', 'like', "%{$search}%")
                ->orWhere('registration_number', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->where('email', 'like', "%{$search}%")
                        ->where('username', 'like', "%{$search}%")
                    ;
                })
            ;
        });
    }
    public function scopeWithAcadamicStatus($query, $status)
    {
        return $query->where('academic_status', $status);
    }

    // Helper Methods
    public function canGraduate()
    {
        return $this->program
        && $this->total_credits_earned >= $this->program->total_credits_required
        && $this->cgpa >= 2.0;
    }

    public function generateStudentId($departmentId = null)
    {
        // Get department code. If departmentId is provided, use it to fetch the department.
        // Otherwise, assume $this->department_id is available (though it might not be set on a new instance).
        $departmentCode = 'GEN';
        if ($departmentId) {
            $department     = Department::find($departmentId);
            $departmentCode = $department->code ?? 'GEN';
        } elseif ($this->department_id) {
            $department     = Department::find($this->department_id);
            $departmentCode = $department->code ?? 'GEN';
        }

        $year = now()->format('y');

        // Count students created in the current year for the given department.
        // Use 'created_at' instead of 'create_at'.
        $sequence = Student::where('department_id', $departmentId ?? $this->department_id)
            ->whereYear('created_at', now()->year)
            ->count() + 1;

        return $departmentCode . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Boot method to auto-generate student_id on creating
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($student) {
            if (empty($student->student_id)) {
                $student->student_id = (new static())->generateStudentId();
            }
        });
    }
}
