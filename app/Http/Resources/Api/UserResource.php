<?php

namespace App\Http\Resources\Api;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    public function getContactStatus(){
        $toUserId = $this->id;
        $fromUserId = Auth::guard('api')->user()->id;

        return Contact::where(['from_user_id' => $fromUserId, 'to_user_id' => $toUserId])->first() ? true : false;
    }

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
            'fbid' => $this->bank ? (string)$this->bank->fbid : null,
            'isContact' => $this->getContactStatus(),
            'isDeleted' => $this->deleted_at != null ? (bool)true : (bool)false
        ];
    }
}
