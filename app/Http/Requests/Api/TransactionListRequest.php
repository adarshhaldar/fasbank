<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TransactionListRequest extends FormRequest
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
            'date' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom_date,range_date',
            'customDate' => 'nullable|required_if:date,custom_date|date|date_format:Y-m-d|before_or_equal:today',
            'fromDate' => 'nullable|required_if:date,range_date|date|date_format:Y-m-d|before_or_equal:'.$this->toDate,
            'toDate' => 'nullable|required_if:date,range_date|date|date_format:Y-m-d|before_or_equal:today',
            'transactionId' => 'nullable',
            'fbId' => 'nullable',
            'name' => 'nullable'
        ];
    }
}
