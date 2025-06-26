<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountDetailResource extends JsonResource
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
            'avatar' => $this->avatar ? (string)$this->avatar : asset('assets/images/default-profile-avatar.svg') ,
            'name' => (string)$this->name,
            'email' => (string)$this->email,
            'bank' => $this->bank ? new BankDetailResource($this->bank) : null,
            'joined' => (string)Carbon::parse($this->email_verified_at)->format('d M Y')
        ];
    }
}
