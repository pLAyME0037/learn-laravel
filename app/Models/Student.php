<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'gender_id',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
    public function academicRecords(): HasMany
    {
        return $this->hasMany(AcademicRecord::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class);
    }

    public function contactDetails(): HasOne
    {
        return $this->hasOne(ContactDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the credit scores for the student.
     */
    public function creditScores(): HasMany
    {
        return $this->hasMany(CreditScore::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }
    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }
    public function getProfilePictureUrlAttribute(): ?string
    {
        return $this->user->profile_picture_url;
    }
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
    public function getAcademicProgressAttribute(): float
    {
        if (
            ! $this->program
            || $this->program->total_credits_required === 0
        ) {
            return 0.0; // Handle division by zero or missing program
        }
        return ($this->total_credits_earned / $this->program->total_credits_required) * 100;
    }

    public function getFormattedAddressAttribute(): string
    {
        $addressParts = [];
        if ($this->current_address) {
            $addressParts[] = $this->current_address;
        }
        if ($this->city) {
            $addressParts[] = $this->city;
        }
        if ($this->state) {
            $addressParts[] = $this->state;
        }
        if ($this->country) {
            $addressParts[] = $this->country;
        }
        if ($this->postal_code) {
            $addressParts[] = $this->postal_code;
        }

        return implode(', ', $addressParts);
    }

    /**
     * Returns the formatted admission date.
     */
    public function getFormattedAdmissionDateAttribute(): string
    {
        return $this->admission_date->format('M d, Y');
    }

    /**
     * Returns the formatted expected graduation date.
     */
    public function getFormattedExpectedGraduationAttribute(): string
    {
        return $this->expected_graduation->format('M d, Y');
    }

    /**
     * Returns "Yes" or "No" for `has_outstanding_balance`.
     */
    public function getHasOutstandingBalanceStatusAttribute(): string
    {
        return $this->has_outstanding_balance ? 'Yes' : 'No';
    }

    /**
     * Returns "Yes" or "No" for `has_disability`.
     */
    public function getHasDisabilityStatusAttribute(): string
    {
        return $this->has_disability ? 'Yes' : 'No';
    }

    /**
     * Combines emergency contact details into a single string.
     */
    public function getEmergencyContactInfoAttribute(): string
    {
        return "Name: {$this->emergency_contact_name}, Phone: {$this->emergency_contact_phone}, Relation: {$this->emergency_contact_relation}";
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('academic_status', 'active');
    }
    public function scopeByDepartment(Builder $query, int $departmentId): void
    {
        $query->where('department_id', $departmentId);
    }
    public function scopeByProgram(Builder $query, int $programId): void
    {
        $query->where('program_id', $programId);
    }
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('student_id', 'like', "%{$search}%")
                ->orWhere('registration_number', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->where('email', 'like', "%{$search}%")
                        ->where('username', 'like', "%{$search}%");
                });
        });
    }
    public function scopeWithAcadamicStatus(Builder $query, string $status): void
    {
        $query->where('academic_status', $status);
    }

    /**
     * Scope a query to filter students with an outstanding balance.
     */
    public function scopeHasOutstandingBalance(Builder $query): void
    {
        $query->where('has_outstanding_balance', true);
    }

    /**
     * Scope a query to filter students expected to graduate within a specified number of months.
     */
    public function scopeGraduatingSoon(Builder $query, int $months = 6): void
    {
        $query->where('expected_graduation', '<=', Carbon::now()->addMonths($months));
    }

    /**
     * Scope a query to filter students by their academic standing.
     */
    public function scopeByAcademicStanding(Builder $query, string $standing): void
    {
        // This scope would require a more complex implementation if academic standing is not a direct column.
        // For now, it assumes 'academic_standing' is a column or can be derived easily.
        // A more robust solution might involve calculating it on the fly or having a dedicated column.
        $query->where('academic_status', $standing); // Reusing academic_status for simplicity
    }

    // Helper Methods
    public function getAcademicStanding(): string
    {
        if ($this->cgpa >= 3.5) {
            return 'Excellent';
        }

        if ($this->cgpa >= 3.0) {
            return 'Good';
        }

        if ($this->cgpa >= 2.0) {
            return 'Satisfactory';
        }

        return 'Probation';
    }

    public function canGraduate(): bool
    {
        return $this->program
        && $this->total_credits_earned >= $this->program->total_credits_required
        && $this->cgpa >= 2.0;
    }

    public function calculateGpa(): float
    {
        // Assuming AcademicRecord has 'grade' and 'credits_earned'
        $totalCredits = 0;
        $totalGradePoints = 0;

        foreach ($this->academicRecords as $record) {
            // You might need a method to convert grade to grade points (e.g., A=4, B=3)
            $gradePoints = $this->convertGradeToPoints($record->grade);
            $totalGradePoints += ($gradePoints * $record->credits_earned);
            $totalCredits += $record->credits_earned;
        }

        return $totalCredits > 0 ? round($totalGradePoints / $totalCredits, 2) : 0.0;
    }

    protected function convertGradeToPoints(string $grade): float
    {
        return match (strtoupper($grade)) {
            'A'     => 4.0,
            'A-'    => 3.7,
            'B+'    => 3.3,
            'B'     => 3.0,
            'B-'    => 2.7,
            'C+'    => 2.3,
            'C'     => 2.0,
            'C-'    => 1.7,
            'D+'    => 1.3,
            'D'     => 1.0,
            'F'     => 0.0,
            default => 0.0,
        };
    }

    public function isEnrolledInCourse(int $courseId): bool
    {
        return $this->enrollments()->where('course_id', $courseId)->exists();
    }

    public function getLatestEnrollment(): ?Enrollment
    {
        return $this->enrollments()->latest('enrollment_date')->first();
    }

    public function generateStudentId(?int $departmentId = null): string
    {
        // Get department code. If departmentId is provided, use it to fetch the department.
        // Otherwise, assume $this->department_id is available (though it might not be set on a new instance).
        $departmentCode = 'GEN';
        if ($departmentId) {
            $department     = Department::find($departmentId);
            $departmentCode = (string) ($department->code ?? 'GEN'); // Explicitly cast to string
        } elseif ($this->department_id) {
            $department     = Department::find($this->department_id);
            $departmentCode = (string) ($department->code ?? 'GEN'); // Explicitly cast to string
        }

        $year = now()->format('y');

        // Count students created in the current year for the given department.
        // Use 'created_at' instead of 'create_at'.
        $sequence = Student::where('department_id', $departmentId ?? $this->department_id)
            ->whereYear('created_at', now()->year)
            ->count() + 1;

        return $departmentCode . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Recalculates and updates the student's CGPA.
     */
    public function updateCgpa(): void
    {
        $this->cgpa = $this->calculateGpa();
        $this->save();
    }

    /**
     * Creates a new payment record for the student.
     */
    public function addPayment(float $amount, string $periodDescription): Payment
    {
        return $this->payments()->create([
            'amount' => $amount,
            'payment_date' => Carbon::now(),
            'payment_period_description' => $periodDescription,
        ]);
    }

    /**
     * Updates the student's academic status to 'graduated'.
     */
    public function markAsGraduated(): void
    {
        $this->academic_status = 'graduated';
        $this->save();
    }

    // Boot method to auto-generate student_id on creating
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($student) {
            if (empty($student->student_id)) {
                $student->student_id = (new static())->generateStudentId();
            }
        });
    }
}
