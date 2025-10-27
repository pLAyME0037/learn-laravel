<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a more readable version of the action.
     */
    public function getFormattedActionAttribute(): string
    {
        return ucfirst($this->action);
    }

    /**
     * Scope a query to only include audit logs by a specific user.
     */
    public function scopeByUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include audit logs by action type.
     */
    public function scopeByAction(Builder $query, string $action): void
    {
        $query->where('action', $action);
    }

    /**
     * Scope a query to order audit logs by the latest entries.
     */
    public function scopeLatest(Builder $query): void
    {
        $query->latest();
    }

    /**
     * Checks if the audit log entry was performed by a specific user.
     */
    public function wasPerformedByUser(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
