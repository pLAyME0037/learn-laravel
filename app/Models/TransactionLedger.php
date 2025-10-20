<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLedger extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_type',
        'debit',
        'credit',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
