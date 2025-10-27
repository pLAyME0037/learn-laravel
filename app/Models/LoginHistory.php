<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'Sec_Ch_Ua',
        'Sec_Ch_Ua_Platform',
        'user_agent',
        'login_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns a human-readable login time.
     */
    public function getFormattedLoginTimeAttribute(): string
    {
        return $this->login_at->format('M d, Y H:i:s');
    }

    /**
     * Combines platform and user agent for a concise string.
     */
    public function getPlatformAndBrowserAttribute(): string
    {
        $platform = $this->Sec_Ch_Ua_Platform ?? 'Unknown Platform';
        $userAgent = $this->user_agent ?? 'Unknown Browser';
        return "{$platform} - {$userAgent}";
    }

    /**
     * Scope a query to only include login history by a specific user.
     */
    public function scopeByUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include login history by IP address.
     */
    public function scopeByIpAddress(Builder $query, string $ipAddress): void
    {
        $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope a query to filter login history after a specific date.
     */
    public function scopeAfterDate(Builder $query, string $date): void
    {
        $query->where('login_at', '>=', Carbon::parse($date)->startOfDay());
    }

    /**
     * Scope a query to filter login history before a specific date.
     */
    public function scopeBeforeDate(Builder $query, string $date): void
    {
        $query->where('login_at', '<=', Carbon::parse($date)->endOfDay());
    }

    /**
     * Checks if the login is from a previously seen device.
     * (Placeholder - requires additional logic/data for known devices)
     */
    public function isFromKnownDevice(): bool
    {
        // Implement logic to check against a list of known devices/user agents/IPs
        return false;
    }
}
