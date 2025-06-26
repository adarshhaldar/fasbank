<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\AccountDetailResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function getDetail()
    {
        try {
            $user = Auth::guard('api')->user();
            return new AccountDetailResource($user);
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function logOutFromAllDevices()
    {
        try {
            DB::beginTransaction();
            $user = Auth::guard('api')->user();
            $user->tokens->each(function ($token) {
                $token->delete();
            });
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function logOut()
    {
        try {
            DB::beginTransaction();
            $user = Auth::guard('api')->user();
            $user->token()->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(){
         try {
            DB::beginTransaction();
            $user = Auth::guard('api')->user();
            $user->status = User::STATUS_DELETED;
            $user->save();
            $user->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
