<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'cash_period_id',
        'member_id',
        'amount',
        'paid_at',
        'status',
        'method',
        'notes',
    ];

    protected $casts = [
        'cash_period_id' => 'integer',
        'member_id' => 'integer',
        'amount' => 'integer',
        'paid_at' => 'date',
        'status' => 'string',
    ];

    public function cashPeriod(): BelongsTo
    {
        return $this->belongsTo(CashPeriod::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }
}
