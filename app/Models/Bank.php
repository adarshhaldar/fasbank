<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';
    protected $fillable = [
        'fbid',
        'user_id',
        'balance'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function transactionFrom()
    {
        return $this->hasMany(Transaction::class, 'from_account', 'fbid');
    }

    public function transactionTo()
    {
        return $this->hasMany(Transaction::class, 'to_account', 'fbid');
    }

    public function paidTransactions()
    {
        return $this->hasMany(Transaction::class, 'from_account', 'fbid')->where('status', Transaction::STATUS_PAID);
    }

    public function paidTransactionsReceived()
    {
        return $this->hasMany(Transaction::class, 'to_account', 'fbid')->where('status', Transaction::STATUS_PAID);
    }

    public function pendingTransactions()
    {
        return $this->hasMany(Transaction::class, 'from_account', 'fbid')->where('status', Transaction::STATUS_PENDING);
    }

    public function requestedTransactions()
    {
        return $this->hasMany(Transaction::class, 'from_account', 'fbid')->where('status', Transaction::STATUS_REQUESTED);
    }

    public function expiredTransactions()
    {
        return $this->hasMany(Transaction::class, 'from_account', 'fbid')->where('status', Transaction::STATUS_EXPIRED);
    }
}
