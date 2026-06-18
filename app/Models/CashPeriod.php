<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashPeriod extends Model
{
    protected $fillable = [
        'name',
        'month',
        'year',
        'monthly_due',
        'starting_balance',
        'is_active',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'monthly_due' => 'integer',
        'starting_balance' => 'integer',
        'is_active' => 'boolean',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function expenseBatches(): HasMany
    {
        return $this->hasMany(ExpenseBatch::class)->orderBy('sort_order');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
