<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }

    // relationship
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Accessor for profile picture
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_pic) {
            // Check if it's a full URL or stored locally
            if (filter_var($this->profile_pic, FILTER_VALIDATE_URL)) {
                return $this->profile_pic;
            }
            return asset('storage/' . $this->profile_pic) . '?v=' . $this->updated_at->timestamp;
        }

        // Generate default avatar based on name
        return $this->generateDefaultAvatar();
    }

    public function generateDefaultAvatar()
    {
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF";
    }

    /**
     * Summary of has"Role | Permission"
     * @param mixed $role
     * @return bool
     */
    public function hasRole($role)
    {
        $userRole = $this->role;
        if (is_string($userRole)) {
            $userRole = Role::where('slug', $userRole)->first();
        }

        if (! $userRole) {
            return false;
        }

        if (is_string($role)) {
            return $userRole->slug === $role;
        }

        if ($role instanceof Role) {
            return $userRole->id === $role->id;
        }

        return false;
    }
    public function hasPermission($permission)
    {
        return $this->role && $this->role->hasPermission($permission);
    }

    /**
     * Summary of scope"User | Active"
     * @param mixed $query
     */
    public function scopeStudent($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('slug', 'student');
        });
    }
    public function scopeNormalUser($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('slug', 'user');
        });
    }
    public function scopeStaff($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->whereIn('slug', [
                'admin'
                , "super_user"
                , "register"
                , "HOD"
                , "professor"
                ,
            ]);
        });
    }
    public function scopeAdmin($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('slug', 'admin');
        });
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeByRole($query, $role)
    {
        if (is_string($role)) {
            return $query->whereHas('role', function ($q) use ($role) {
                $q->where('slug', $role);
            });
        }
        return $query->where('role_id', $role);
    }

    /**
     * Summary of is"Role | Active"
     * @return bool
     */
    public function isStudent()
    {
        return $this->hasRole('student');
    }
    public function isStaff()
    {
        $role = $this->role;
        if (is_string($role)) {
            $role = Role::where('slug', $role)->first();
        }

        return $role && in_array($role->slug, [
            'admin',
            'super_user',
            'register',
            'HOD',
            'professor',
        ]);
    }
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
    public function isSuperUser()
    {
        return $this->hasRole('super_user');
    }
    public function isActive()
    {
        return $this->is_active;
    }
}
