<?php

namespace App\Http\Resources\Api;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends JsonResource
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

    public function formatExpiredDate()
    {
        $date = Carbon::parse($this->expires_on);

        if ($date->isToday()) {
            $format = 'Expired Today at ' . $date->format('h:i a');
        } else if ($date->isYesterday()) {
            $format = 'Expired Yesterday at ' . $date->format('h:i a');
        } else {
            $format = 'Expired on ' . $date->format('d M y') . ' at ' . $date->format('h:i a');
        }

        return $format;
    }

    public function formatRequestedDate()
    {
        $date = Carbon::parse($this->created_at);

        if ($date->isToday()) {
            $format = 'Requested Today at ' . $date->format('h:i a');
        } else if ($date->isYesterday()) {
            $format = 'Requested Yesterday at ' . $date->format('h:i a');
        } else {
            $format = 'Requested on ' . $date->format('d M y') . ' at ' . $date->format('h:i a');
        }

        return $format;
    }

    public function formatPaidDate()
    {
        $date = Carbon::parse($this->paid_at);

        if ($date->isToday()) {
            $format = 'Paid Today at ' . $date->format('h:i a');
        } else if ($date->isYesterday()) {
            $format = 'Paid Yesterday at ' . $date->format('h:i a');
        } else {
            $format = 'Paid on ' . $date->format('d M y') . ' at ' . $date->format('h:i a');
        }

        return $format;
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
            return $this->status == Transaction::STATUS_PAID && !$this->expires_on ? 'Payment to ' . $toUser->name : ($this->status == Transaction::STATUS_REQUESTED ? 'Requested to ' . $toUser->name : ($this->status == Transaction::STATUS_EXPIRED ? 'Requested to ' . $toUser->name : 'Requested to ' . $toUser->name));
        } else {
            return $this->status == Transaction::STATUS_PAID && !$this->expires_on ? 'Payment from ' . $toUser->name : ($this->status == Transaction::STATUS_REQUESTED ? 'Request from ' . $toUser->name : ($this->status == Transaction::STATUS_EXPIRED ? 'Request from ' . $toUser->name : 'Request from ' . $toUser->name));
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
            'paidAt' => $this->paid_at ? Carbon::parse($this->paid_at)->format('d M y') . ' at ' . Carbon::parse($this->paid_at)->format('h:i a') : null,
            'paidOn' => $this->paid_at ? $this->formatPaidDate() : null,
            'createdAt' => $this->created_at ? Carbon::parse($this->created_at)->format('d M y') . ' at ' . Carbon::parse($this->created_at)->format('h:i a') : null,
            'requestedOn' => $this->status == Transaction::STATUS_REQUESTED ? $this->formatRequestedDate() : null,
            'expiredOn' => $this->status == Transaction::STATUS_EXPIRED ? $this->formatExpiredDate() : null,
            'groupDate' => $this->getGroupDate()
        ];
    }
}
