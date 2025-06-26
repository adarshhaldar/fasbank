<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
            'fbid' => (int)$this->fbid,
            'perDayTransactions' => (int)$this->per_day_transactions,
            'perDayMaxAmountSpend' => $this->per_day_max_amount_spend ? (string)$this->per_day_max_amount_spend : null,
            'monthlyTransactions' => (int)$this->monthly_transactions,
            'monthlyMaxAmountSpend' => $this->monthly_max_amount_spend ? (string)$this->monthly_max_amount_spend : null
        ];
    }
}
