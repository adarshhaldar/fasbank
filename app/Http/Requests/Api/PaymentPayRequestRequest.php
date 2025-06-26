<?php

namespace App\Http\Requests\Api;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class PaymentPayRequestRequest extends FormRequest
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
            'transactionId' => 'required|exists:transactions,transaction_id,status,' . Transaction::STATUS_REQUESTED,
            'toUserFbid' => 'required|exists:banks,fbid'
        ];
    }

    public function messages(): array
    {
        return [
            'transactionId.required' => 'Transaction ID is required',
            'transactionId.exists' => 'Invalid transction',
            'toUserFbid.required' => 'To user FasBank ID is required',
            'toUserFbid.exists' => 'Invalid FasBank ID'
        ];
    }
}
