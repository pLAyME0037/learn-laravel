<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'department_id',
        'name',
        'username',
        'email',
        'password',
        'profile_pic',
        'bio',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    /**
     * Get the instructor associated with the user.
     */
    public function instructor(): HasOne
    {
        return $this->hasOne(Instructor::class);
    }

    /**
     * Get the contact detail associated with the user.
     */
    public function contactDetail(): HasOne
    {
        return $this->hasOne(ContactDetail::class);
    }

    /**
     * Get the audit logs for the user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the transaction ledgers for the user.
     */
    public function transactionLedgers(): HasMany
    {
        return $this->hasMany(TransactionLedger::class);
    }

    // Accessors
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_pic) {
            $url = filter_var($this->profile_pic, FILTER_VALIDATE_URL)
                ? $this->profile_pic
                : asset('storage/' . $this->profile_pic);

            return $url;
        }
        return $this->generateDefaultAvatar();
    }

    public function getRoleNamesAttribute(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Returns "Active" or "Inactive" for `is_active`.
     */
    public function getIsActiveStatusAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Returns a human-readable last login time.
     */
    public function getFormattedLastLoginAttribute(): string
    {
        return $this->last_login_at ? $this->last_login_at->diffForHumans() : 'Never logged in';
    }

    // Helper Methods
    private function generateDefaultAvatar(): string
    {
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF&bold=true";
    }

    // Custom permission checks
    public function isSuperUser(): bool
    {
        return $this->hasRole('Super Administrator');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole([
            'super_user', 
            'admin', 
            'registrar',
            'hod',
            'professor', 
            'staff'
        ]);
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function isHod(): bool
    {
        return $this->hasRole('hod');
    }

    /**
     * Checks if the user has the 'professor' role.
     */
    public function isProfessor(): bool
    {
        return $this->hasRole('professor');
    }

    /**
     * Checks if the user has the 'registrar' role.
     */
    public function isRegistrar(): bool
    {
        return $this->hasRole('registrar');
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
    public function scopeStaff(Builder $query): void
    {
        $query->whereHas('roles', function ($q) {
            $q->whereIn('name', ['super_user', 'admin', 'registrar', 'hod', 'professor', 'staff']);
        });
    }
    public function scopeStudents(Builder $query): void
    {
        $query->whereHas('roles', function ($q) {
            $q->where('name', 'student');
        });
    }
    public function scopeByDepartment(Builder $query, int $departmentId): void
    {
        $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter users by a specific role.
     */
    public function scopeByRole(Builder $query, string $roleName): void
    {
        $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Scope a query to filter users who last logged in before a certain date.
     */
    public function scopeLastLoggedInBefore(Builder $query, string $date): void
    {
        $query->where('last_login_at', '<', Carbon::parse($date)->startOfDay());
    }

    public function updateLoginInfo(): void
    {
        $this->update([
            'last_login_at' => now(),
        ]);
    }
}
