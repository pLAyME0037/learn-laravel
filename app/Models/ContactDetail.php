<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class ContactDetail extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'address',
        'phone_number',
        'user_id',
        'student_id',
        'instructor_id',
    ];

    /**
     * Get the user that owns the contact detail.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student that owns the contact detail.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the instructor that owns the contact detail.
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Get a consistently formatted phone number.
     */
    public function getFormattedPhoneNumberAttribute(): ?string
    {
        if (empty($this->phone_number)) {
            return null;
        }
        // Basic formatting: (XXX) XXX-XXXX
        $phoneNumber = preg_replace('/[^0-9]/', '', $this->phone_number);
        if (strlen($phoneNumber) === 10) {
            return '(' . substr($phoneNumber, 0, 3) . ') ' . substr($phoneNumber, 3, 3) . '-' . substr($phoneNumber, 6, 4);
        }
        return $this->phone_number; // Return as is if not 10 digits
    }

    /**
     * Get a shortened version of the address.
     */
    public function getShortAddressAttribute(): ?string
    {
        if (empty($this->address)) {
            return null;
        }
        $parts = explode(',', $this->address);
        return trim($parts[0]);
    }

    /**
     * Scope a query to only include contact details by email.
     */
    public function scopeByEmail(Builder $query, string $email): void
    {
        $query->where('email', $email);
    }

    /**
     * Scope a query to only include contact details by phone number.
     */
    public function scopeByPhoneNumber(Builder $query, string $phoneNumber): void
    {
        $query->where('phone_number', $phoneNumber);
    }

    /**
     * Checks if an email address is present.
     */
    public function hasEmail(): bool
    {
        return !empty($this->email);
    }

    /**
     * Checks if a phone number is present.
     */
    public function hasPhoneNumber(): bool
    {
        return !empty($this->phone_number);
    }
}
