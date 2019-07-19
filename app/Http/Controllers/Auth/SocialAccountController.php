<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use App\SocialAccount;
use App\User;

class SocialAccountController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->user();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();

            $authUser = $this->findUser($user, $provider);

            $result = Auth::login($authUser, true);
            return $result;
        } catch (Exception $e) {
            return [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => StatusHttp::getStatusCode500()
            ];
        }
    }

    public function findUser($socialUser, $provider)
    {
        $account = SocialAccount::where([
            ['provider_name', $provider],
            ['provider_id', $socialUser->getId()]
        ])->first();

        if ($account) {
            return $account->user;
        } else {
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                //create a new user
            }

            $user->accounts()->create([
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);

            return $user;
        }
    }
}
