<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\User;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use stdClass;

class SocialController extends Controller
{
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function generateFbid($userId)
    {
        return now()->getTimestampMs() . $userId;
    }

    public function googleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

            $finduser = User::where('email', $user->email)->orWhere('google_id', $user->id)->first();

            if ($finduser) {
                $finduser->token = $finduser->createToken(env('APP_NAME'))->accessToken;
                $user = $finduser;
            } else {
                DB::beginTransaction();
                $password = \Str::random(10);

                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'avatar' => $user->avatar ?? null,
                    'google_id' => $user->id,
                    'status' => User::STATUS_ACTIVE,
                ]);

                $newUser->save();

                $bankDetail = Bank::create([
                    'fbid' => $this->generateFbid($newUser->id),
                    'user_id' => $newUser->id,
                    'balance' => 1000
                ]);

                $bankDetail->save();

                DB::commit();

                $newUser->token = $newUser->createToken(env('APP_NAME'))->accessToken;
                $user = $newUser;
            }

            if ($user) {
                return redirect(route('index'))->with('token', $user->token);
            } else {
                return redirect(route('index'))->with('error', trans('messages.login_fail'));
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect(route('index'))->with('error', trans('messages.google_failed'));
        }
    }
}
