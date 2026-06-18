<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_batch_id' => ['nullable', 'exists:expense_batches,id'],
            'item_name' => ['required', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:60'],
            'amount' => ['required', 'integer', 'min:0'],
            'expense_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'expense_batch_id' => 'batch',
            'item_name' => 'nama item',
            'category' => 'kategori',
            'amount' => 'harga',
            'expense_date' => 'tanggal',
            'notes' => 'catatan',
        ];
    }
}
