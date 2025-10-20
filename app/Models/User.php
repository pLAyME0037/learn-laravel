<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'department_id',
        'role_id',
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
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    // Accessors
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_pic) {
            if (filter_var($this->profile_pic, FILTER_VALIDATE_URL)) {
                return $this->profile_pic;
            }
            return asset('storage/' . $this->profile_pic);
        }
        return $this->generateDefaultAvatar();
    }

    public function getRoleNamesAttribute(): array
    {
        return $this->roles->pluck('name')->toArray();
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
        return $this->hasRole('Super Administrator'); // Updated to match Spatie role name
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole(['super_user', 'admin', 'registrar', 'hod', 'professor', 'staff']);
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function isHod(): bool
    {
        return $this->hasRole('hod');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeStaff($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->whereIn('name', ['super_user', 'admin', 'registrar', 'hod', 'professor', 'staff']);
        });
    }
    public function scopeStudents($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'student');
        });
    }
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function updateLoginInfo()
    {
        $this->update([
            'last_login_at' => now(),
        ]);
    }

    public function assignRole($roles, $guard = null)
    {
        return parent::assignRole($roles, $guard); // Use Spatie's assignRole method
    }
}
