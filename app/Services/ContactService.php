<?php

namespace App\Services;

use App\Exceptions\ContactException;
use App\Http\Resources\Api\ContactResource;
use App\Http\Resources\Api\UserResource;
use App\Models\Bank;
use App\Models\Contact;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContactService
{
    public function getList($currentPage, $perPage, $search)
    {
        try {
            $user = Auth::guard('api')->user();
            $contacts = Contact::where('from_user_id', $user->id);
            if ($search) {
                $contacts = $contacts->whereHas('toUser', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%' . $search . '%')->orWhereHas('bank', function ($q3) use ($search) {
                        $q3->where('fbid', 'like', '%' . $search . '%');
                    });
                });
            }
            $contacts = $contacts->with('toUser')
                ->join('users as u', 'contacts.to_user_id', '=', 'u.id')
                ->orderBy('u.name', 'ASC')
                ->select('contacts.*')
                ->paginate($perPage, ['*'], 'page', $currentPage);

            return ContactResource::collection($contacts);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getRecentList($currentPage, $perPage, $search)
    {
        try {
            $user = Auth::guard('api')->user();
            $currentUserBankFbid = $user->bank->fbid;

            $recents = User::select('users.*')
                ->join('banks', 'users.id', '=', 'banks.user_id')
                ->whereIn('banks.fbid', function ($query) use ($currentUserBankFbid) {
                    $query->selectRaw('DISTINCT CASE
            WHEN from_account = ? THEN to_account
            WHEN to_account = ? THEN from_account
        END', [$currentUserBankFbid, $currentUserBankFbid])
                        ->from('transactions')
                        ->whereRaw('? IN (from_account, to_account)', [$currentUserBankFbid]);
                })
                ->where('users.id', '!=', $user->id)
                ->orderByDesc(
                    Transaction::select('created_at')
                        ->whereColumn('from_account', 'banks.fbid')
                        ->orWhereColumn('to_account', 'banks.fbid')
                        ->latest()
                        ->limit(1)
                )
                ->withTrashed()->paginate($perPage, ['*'], 'page', $currentPage);

            return UserResource::collection($recents);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function findNew($search)
    {
        try {
            $user = Auth::guard('api')->user();
            $contact = User::where('id', '!=', $user->id)->where(function ($q) use ($search) {
                $q->whereHas('bank', function ($q1) use ($search) {
                    $q1->where('fbid', $search);
                });
            })->with('bank')->first();

            return $contact ? new UserResource($contact) : null;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getUserByFbid($fbid)
    {
        return User::where('status', User::STATUS_ACTIVE)->whereHas('bank', function ($q) use ($fbid) {
            $q->where('fbid', $fbid);
        })->first() ?? false;
    }

    public function addNew($fbid)
    {
        try {
            $toUser = $this->getUserByFbid($fbid);
            if (!$toUser) {
                throw new ContactException('Contact not found');
            }

            DB::beginTransaction();
            $fromUser = Auth::guard('api')->user();
            Contact::create([
                'from_user_id' => $fromUser->id,
                'to_user_id' => $toUser->id
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
