<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'created_by' => 'nullable|exists:users,id',
            'number' => 'required|string',
            'status' => 'required|in:draft,send,paid,overdue,cancelled',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'public_token' => 'nullable|string',
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|decimal:2|min:0',
            'items.*.price' => 'required|decimal:2|min:0',
            'items.*.total' => 'required|decimal:2|min:0',
            'items.*.description' => 'nullable|string',
            'transaction_id' => 'nullable|exists:transactions,id',
        ];
    }
}
