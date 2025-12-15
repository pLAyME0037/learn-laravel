<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'student_id',
        'semester_name',
        'amount',
        'paid_amount',
        'status', // 'unpaid', 'partial', 'paid'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getBalanceAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    /**
     * Scope: Find overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid');
    }
}