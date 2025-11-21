<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'amount',
        'payment_date',
        'payment_period_description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Returns the amount formatted as currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format((float) $this->amount, 2);
    }

    /**
     * Returns a human-readable payment date.
     */
    public function getFormattedPaymentDateAttribute(): string
    {
        return $this->payment_date->format('M d, Y');
    }

    /**
     * Scope a query to only include payments by a specific student.
     */
    public function scopeByStudent(Builder $query, int $studentId): void
    {
        $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to filter payments within a date range.
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('payment_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
    }

    /**
     * Scope a query to filter payments with an amount greater than a specified value.
     */
    public function scopeAmountGreaterThan(Builder $query, float $amount): void
    {
        $query->where('amount', '>', $amount);
    }

    /**
     * Checks if the payment covers a specific period.
     */
    public function isPaidForPeriod(string $periodDescription): bool
    {
        return strtolower($this->payment_period_description) === strtolower($periodDescription);
    }
}
