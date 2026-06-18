<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'cash_period_id',
        'expense_batch_id',
        'item_name',
        'category',
        'amount',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'cash_period_id' => 'integer',
        'expense_batch_id' => 'integer',
        'amount' => 'integer',
        'expense_date' => 'date',
    ];

    public function cashPeriod(): BelongsTo
    {
        return $this->belongsTo(CashPeriod::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ExpenseBatch::class, 'expense_batch_id');
    }
}
