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
        'username',
        'name',
        'email',
        'profile_pic',
        'bio',
        'password',
        'role',
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
            'last_login_at'    => 'datetime',
        ];
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

    public function scopeNormalUser($query)
    {
        return $query->where('role', 'user');
    }
    public function scopeStaff($query)
    {
        return $query->where('role', 'staff');
    }
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }
    public function isStaff()
    {
        return $this->role === 'staff';
    }
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function isActive()
    {
        return $this->is_active;
    }

}
