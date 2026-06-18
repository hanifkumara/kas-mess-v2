<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'status' => ['required', 'in:paid,unpaid'],
            'method' => ['nullable', 'string', 'max:60'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'amount' => 'nominal bayar',
            'paid_at' => 'tanggal bayar',
            'method' => 'metode bayar',
            'notes' => 'catatan',
        ];
    }
}
