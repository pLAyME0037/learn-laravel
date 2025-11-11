<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLedger extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_type',
        'debit',
        'credit',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns debit amount formatted as currency.
     */
    public function getFormattedDebitAttribute(): string
    {
        return '$' . number_format($this->debit, 2);
    }

    /**
     * Returns credit amount formatted as currency.
     */
    public function getFormattedCreditAttribute(): string
    {
        return '$' . number_format($this->credit, 2);
    }

    /**
     * Returns the net effect of the transaction (credit - debit).
     */
    public function getBalanceEffectAttribute(): float
    {
        return $this->credit - $this->debit;
    }

    /**
     * Scope a query to only include transactions by user.
     */
    public function scopeByUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include transactions by type (e.g., 'debit', 'credit').
     */
    public function scopeByType(Builder $query, string $type): void
    {
        $query->where('transaction_type', $type);
    }

    /**
     * Scope a query to filter for debit transactions.
     */
    public function scopeDebits(Builder $query): void
    {
        $query->where('transaction_type', 'debit');
    }

    /**
     * Scope a query to filter for credit transactions.
     */
    public function scopeCredits(Builder $query): void
    {
        $query->where('transaction_type', 'credit');
    }

    /**
     * Scope a query to filter transactions within a date range.
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(), 
            Carbon::parse($endDate)->endOfDay()
        ]);
    }

    /**
     * Checks if the transaction is a debit.
     */
    public function isDebit(): bool
    {
        return strtolower($this->transaction_type) === 'debit';
    }

    /**
     * Checks if the transaction is a credit.
     */
    public function isCredit(): bool
    {
        return strtolower($this->transaction_type) === 'credit';
    }

    /**
     * Returns the net change (credit - debit) for the transaction.
     */
    public function getNetChange(): float
    {
        return $this->credit - $this->debit;
    }
}
