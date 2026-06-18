<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseBatch extends Model
{
    protected $fillable = [
        'cash_period_id',
        'title',
        'batch_date',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'cash_period_id' => 'integer',
        'sort_order' => 'integer',
        'batch_date' => 'date',
    ];

    public function cashPeriod(): BelongsTo
    {
        return $this->belongsTo(CashPeriod::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class)->orderBy('expense_date')->orderBy('id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
