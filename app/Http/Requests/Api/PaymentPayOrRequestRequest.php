<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PaymentPayOrRequestRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

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
            'toUserFbid' => 'required|exists:banks,fbid',
            'amount' => 'required|integer|min:1|max:999999999999',
            'note' => 'nullable|min:1|max:30',
        ];
    }

    public function messages(): array
    {
        return [
            'toUserFbid.required' => 'Recipient is required',
            'toUserFbid.exists' => 'Invalid recipient',
            'amount.required' => 'Amount is required',
            'amount.integer' => 'Amount must be a number',
            'amount.min' => 'Amount must be greater than 1',
            'amount.max' => 'Amount must be less than 999999999999',
            'note.min' => 'Note must have at lease 1 character',
            'note.max' => 'Note must not exceed 30 character'
        ];
    }
}
