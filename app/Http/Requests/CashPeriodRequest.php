<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CashPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $periodId = $this->route('cash_period');

        return [
            'name' => ['required', 'string', 'max:120'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'digits:4'],
            'monthly_due' => ['required', 'integer', 'min:0'],
            'starting_balance' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            Rule::unique('cash_periods', 'month')->where(fn ($q) => $q->where('year', $this->year))->ignore($periodId),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama periode',
            'month' => 'bulan',
            'year' => 'tahun',
            'monthly_due' => 'iuran per anggota',
            'starting_balance' => 'kas awal',
        ];
    }
}
