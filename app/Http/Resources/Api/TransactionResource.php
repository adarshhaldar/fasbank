<?php

namespace App\Http\Resources\Api;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends JsonResource
{
    public function getGroupDate()
    {
        $date = Carbon::parse($this->created_at);
        if ($date->isToday()) {
            $groupDate = 'Today';
        } else if ($date->isYesterday()) {
            $groupDate = 'Yesterday';
        } else {
            $groupDate = $date->format('d M y');
        }

        return $groupDate;
    }

    public function getStatusLabel()
    {
        return $this->status == Transaction::STATUS_PENDING ? 'Pending' : ($this->status == Transaction::STATUS_REQUESTED ? 'Requested' : 'Paid');
    }

    public function getPaymentTitle()
    {
        $currentUser = Auth::guard('api')->user();
        $toUser = $currentUser->bank->fbid == $this->from_account ? $this->toAccountBank->user : $this->fromAccountBank->user;

        if ($currentUser->bank->fbid == $this->from_account) {
            return $this->expires_on ? 'Received from ' . $toUser->name : 'Transfer to ' . $toUser->name;
        } else {
            return $this->expires_on ? 'Transfer to ' . $toUser->name : 'Received from ' . $toUser->name;
        }
    }

    public function getActionClass(){
        $currentUser = Auth::guard('api')->user();

        if ($currentUser->bank->fbid == $this->from_account) {
            return $this->expires_on ? 'transaction-credit' : 'transaction-debit';
        } else {
            return $this->expires_on ? 'transaction-debit' : 'transaction-credit';
        }
    }

    public function getAmountTitle(){
        $currentUser = Auth::guard('api')->user();

        if ($currentUser->bank->fbid == $this->from_account) {
            return $this->expires_on ? '+'.$this->amount : '-'.$this->amount;
        } else {
            return $this->expires_on ? '-'.$this->amount : '+'.$this->amount;
        }
    }

    public function getDateTitle(){
        $currentUser = Auth::guard('api')->user();

        if ($currentUser->bank->fbid == $this->from_account) {
            return $this->expires_on ? 'Credited on '.Carbon::parse($this->created_at)->format('d M y') . ' at ' . Carbon::parse($this->created_at)->format('h:i a') : 'Debited on '.Carbon::parse($this->created_at)->format('d M y') . ' at ' . Carbon::parse($this->created_at)->format('h:i a');
        } else {
            return $this->expires_on ? 'Debited on '.Carbon::parse($this->created_at)->format('d M y') . ' at ' . Carbon::parse($this->created_at)->format('h:i a') : 'Credited on '.Carbon::parse($this->created_at)->format('d M y') . ' at ' . Carbon::parse($this->created_at)->format('h:i a');
        }
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
            'paymentTitle' => $this->getPaymentTitle(),
            'transactionId' => (string)$this->transaction_id,
            'fromAccount' => (string)$this->from_account,
            'toAccount' => (string)$this->to_account,
            'fromAccountDetail' => new UserResource($this->fromAccountBank->user),
            'toAccountDetail' => new UserResource($this->toAccountBank->user),
            'note' => (string)$this->note,
            'amount' => (string)$this->amount,
            'status' => (string)$this->status,
            'statusLabel' => $this->getStatusLabel(),
            'createdAt' => $this->created_at ? Carbon::parse($this->created_at)->format('d M y') . ' at ' . Carbon::parse($this->created_at)->format('h:i a') : null,
            'groupDate' => $this->getGroupDate(),
            'actionClass' => $this->getActionClass(),
            'amountTitle' => $this->getAmountTitle(),
            'dateTitle' => $this->getDateTitle(),
        ];
    }
}
