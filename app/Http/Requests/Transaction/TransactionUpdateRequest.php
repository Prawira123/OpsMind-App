<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class TransactionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id'       => ['required', 'exists:categories,id'],
            'debit_account_id'  => ['required', 'exists:accounts,id'],
            'credit_account_id' => ['required', 'exists:accounts,id'],
            'date'              => ['required', 'date'],
            'description'       => ['required', 'string', 'max:255'],
            'type'              => ['required', 'in:expense,income,transfer'],
            'reference_no'      => ['nullable', 'string'],
            'client_id'         => ['nullable', 'exists:clients,id'],

            // Items — validasi array
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.description'    => ['required', 'string', 'max:255'],
            'items.*.qty'            => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'     => ['required', 'numeric', 'min:0'],
            'items.*.amount_per_item'=> ['required', 'numeric', 'min:0'],
        ];
    }
}
