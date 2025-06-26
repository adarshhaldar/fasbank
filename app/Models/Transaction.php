<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'transaction_id',
        'from_account',
        'to_account',
        'note',
        'amount',
        'status',
        'paid_at',
        'expires_on',
    ];

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';
    const STATUS_REQUESTED = 'requested';
    const STATUS_EXPIRED = 'expired';

    public function toAccountBank()
    {
        return $this->belongsTo(Bank::class, 'to_account', 'fbid');
    }

    public function fromAccountBank()
    {
        return $this->belongsTo(Bank::class, 'from_account', 'fbid');
    }
}
