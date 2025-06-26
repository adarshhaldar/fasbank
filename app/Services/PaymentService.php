<?php

namespace App\Services;

use App\Exceptions\PaymentException;
use App\Helpers\NotificationHelper;
use App\Http\Resources\Api\PaymentResource;
use App\Http\Resources\Api\TransactionResource;
use App\Models\Bank;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    use NotificationHelper;

    public function getToUserFromFbid($toUserFbid)
    {
        $user = Auth::guard('api')->user();
        
        return User::whereHas('bank', function ($q) use ($toUserFbid, $user) {
            $q->where('fbid', '!=', $user->bank->fbid)->where('fbid', $toUserFbid);
        })->whereIn('status', [User::STATUS_ACTIVE, User::STATUS_DELETED])->withTrashed()->first() ?? null;
    }

    public function getList($toUserFbid, $currentPage, $perPage)
    {
        try {
            $user = Auth::guard('api')->user();
            $payments = Transaction::where(function ($q) use ($user, $toUserFbid) {
                $q->where('from_account', $user->bank->fbid)->where('to_account', $toUserFbid);
            })->orWhere(function ($q) use ($user, $toUserFbid) {
                $q->where('from_account', $toUserFbid)->where('to_account', $user->bank->fbid);
            });

            $payments = $payments
                ->orderBy('created_at', 'DESC')
                ->paginate($perPage, ['*'], 'page', $currentPage);

            return PaymentResource::collection($payments);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function applySearchByFbId($request, $payments, $user)
    {
        return $request->fbId ? $payments->where(function ($q) use ($request, $user) {
            $q->where('to_account', 'like', '%' . $request->fbId . '%')->where('to_account', '!=', $user->bank->fbid);
        })->orWhere(function ($q) use ($request, $user) {
            $q->where('from_account', 'like', '%' . $request->fbId . '%')->where('from_account', '!=', $user->bank->fbid);
        }) : $payments;
    }

    public function applySearchByTransactionId($request, $payments)
    {
        return $request->transactionId ? $payments->where('transaction_id', 'like', '%' . $request->transactionId . '%') : $payments;
    }

    public function applyDateFilter($request, $payments)
    {
        switch ($request->date) {
            case 'custom_date':
                $payments = $payments->whereDate('created_at', Carbon::parse($request->customDate));
                break;
            case 'range_date':
                $payments = $payments->whereDate('created_at', '>=', Carbon::parse($request->fromDate))->whereDate('created_at', '<=', Carbon::parse($request->toDate));
                break;
            case 'today':
                $payments = $payments->whereDate('created_at', now());
                break;
            case 'yesterday':
                $payments = $payments->whereDate('created_at', now()->subDay());
                break;
            case 'this_week':
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                $payments = $payments->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                break;
            case 'last_week':
                $startOfWeek = now()->subWeek()->startOfWeek();
                $endOfWeek = now()->subWeek()->endOfWeek();
                $payments = $payments->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                break;
            case 'this_month':
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                $payments = $payments->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                break;
            case 'last_month':
                $startOfMonth = now()->subMonth()->startOfMonth();
                $endOfMonth = now()->subMonth()->endOfMonth();
                $payments = $payments->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                break;
            case 'this_year':
                $startOfYear = now()->startOfYear();
                $endOfYear = now()->endOfYear();
                $payments = $payments->whereBetween('created_at', [$startOfYear, $endOfYear]);
                break;
            case 'last_year':
                $startOfYear = now()->subYear()->startOfYear();
                $endOfYear = now()->subYear()->endOfYear();
                $payments = $payments->whereBetween('created_at', [$startOfYear, $endOfYear]);
                break;
            default:
                $payments = $payments;
        }

        return $payments;
    }

    public function applyFilterOnTransactionList($request, $payments, $user)
    {
        $payments = $this->applyDateFilter($request, $payments);

        $payments = $this->applySearchByTransactionId($request, $payments);

        $payments = $this->applySearchByFbId($request, $payments, $user);
        return $payments;
    }

    public function getTransactionList($request, $currentPage, $perPage)
    {
        try {
            $user = Auth::guard('api')->user();
            $payments = Transaction::whereNotIn('status', [Transaction::STATUS_REQUESTED, Transaction::STATUS_EXPIRED])
                ->where(function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('from_account', $user->bank->fbid);
                    })->orWhere(function ($q) use ($user) {
                        $q->where('to_account', $user->bank->fbid);
                    });
                });

            $payments = $this->applyFilterOnTransactionList($request, $payments, $user);
            $payments = $payments
                ->orderBy('created_at', 'DESC')
                ->paginate($perPage, ['*'], 'page', $currentPage);

            return TransactionResource::collection($payments);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function generateTransactionId()
    {
        $length = 10;
        $idInBytes = random_bytes($length);
        $id = bin2hex($idInBytes);


        $transactionIdExist = Transaction::where(['transaction_id' => $id])->first();

        if ($transactionIdExist) {
            $this->generateTransactionId();
        }

        return $id;
    }

    public function getPayloadForTransactionCreation($request, $user, $isRequest)
    {
        return [
            'transaction_id' => $this->generateTransactionId(),
            'from_account' => $user->bank->fbid,
            'to_account' => $request->toUserFbid,
            'note' => $request->note ?? null,
            'amount' => $request->amount,
            'status' => $isRequest ? Transaction::STATUS_REQUESTED : Transaction::STATUS_PAID,
            'paid_at' => $isRequest ? null : now(),
            'expires_on' => $isRequest ? now()->addDay() : null
        ];
    }

    public function checkForBalanceLimit($user, $amount)
    {
        $bank = $user->bank;
        if ($bank->balance < $amount) {
            throw new PaymentException('Insufficient balance');
        }
    }

    public function deductAmount($user, $amount)
    {
        $bank = $user->bank;
        $bank->balance -= $amount;
        $bank->save();
    }

    public function creditAmount($toUserFbid, $amount)
    {
        $bank = Bank::where('fbid', $toUserFbid)->first();
        $bank->balance += $amount;
        $bank->save();
    }

    public function paymentOrRequest($request, $user, $isRequest = false)
    {
        DB::beginTransaction();
        Transaction::create($this->getPayloadForTransactionCreation($request, $user, $isRequest));
        if (!$isRequest) {
            $this->deductAmount($user, $request->amount);
            $this->creditAmount($request->toUserFbid, $request->amount);
        }
        DB::commit();
    }

    public function pay($request)
    {
        try {
            $user = Auth::guard('api')->user();

            $toUser = $this->getToUserFromFbid($request->toUserFbid);
            if (!$toUser) {
                throw new PaymentException('Invalid recipient');
            }

            $this->checkForBalanceLimit($user, $request->amount);
            $this->paymentOrRequest($request, $user);

            $this->sendPayNotification($toUser, $user, $request->amount);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function request($request)
    {
        try {
            $user = Auth::guard('api')->user();

            $toUser = $this->getToUserFromFbid($request->toUserFbid);
            if (!$toUser) {
                throw new PaymentException('Invalid recipient');
            }

            $this->paymentOrRequest($request, $user, true);
            $this->sendRequestNotification($toUser, $user, $request->amount);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function paidStatusForPayRequest($transaction)
    {
        $transaction->paid_at = now();
        $transaction->status = Transaction::STATUS_PAID;
        $transaction->save();
    }

    public function expirePayRequest($transaction)
    {
        $expireDate = Carbon::parse($transaction->expires_on);
        if (Carbon::now()->gt($expireDate)) {
            $transaction->status = Transaction::STATUS_EXPIRED;
            $transaction->save();
            throw new PaymentException('Request expired');
        }
    }

    public function payRequest($request)
    {
        try {
            $user = Auth::guard('api')->user();

            $toUser = $this->getToUserFromFbid($request->toUserFbid);
            if (!$toUser) {
                throw new PaymentException('Invalid recipient');
            }

            $transaction = Transaction::where(['transaction_id' => $request->transactionId, 'from_account' => $request->toUserFbid, 'to_account' => $user->bank->fbid, 'status' => Transaction::STATUS_REQUESTED])->first();
            if (!$transaction) {
                throw new PaymentException('Invalid transaction');
            }

            $this->expirePayRequest($transaction);

            $this->checkForBalanceLimit($user, $transaction->amount);

            $this->paidStatusForPayRequest($transaction);
            $this->deductAmount($user, $transaction->amount);
            $this->creditAmount($request->toUserFbid, $transaction->amount);

            $this->sendPayNotification($toUser, $user, $transaction->amount);

            return new PaymentResource($transaction);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
