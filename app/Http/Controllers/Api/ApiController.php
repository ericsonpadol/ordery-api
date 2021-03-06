<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;

//helper
use Validator;
use App\Traits\StatusHttp;
use App\Traits\AccountSecurity;
use Session;
use Log;
use App\Traits\MailHelper;

class ApiController extends Controller
{
    use StatusHttp, AccountSecurity, MailHelper;

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if ($this->isCredentialsValid($credentials) === false) {
            return response()->json([
                'message' => __('auth.failed'),
                'http_code' => $this->getStatusCode401(),
                'status' => __('messages.status_error'),
            ], $this->getStatusCode401())
                ->header(__('messages.header_convo'), Session::getId());
        }

        if (auth()->attempt($credentials)) {
            $accessToken = auth()->user()->createToken('secret_ordery')->accessToken;

            //check if account is active
            if ($this->isAccountActive($credentials) === false) {
                return response()->json([
                    'message' => __('messages.unverified_account'),
                    'http_code' => $this->getStatusCode401(),
                    'status' => __('messages.status_error')
                ], $this->getStatusCode401())
                    ->header(__('messages.header_convo'), Session::getId());
            }

            return response()->json([
                'access_token' => $accessToken,
                'http_code' => $this->getStatusCode200(),
                'status' => __('messages.status_success')
            ], $this->getStatusCode200())->header(__('messages.header_convo'), Session::getId());
        } else {
            return response()->json([
                'message' => __('messages.unauthorized_login'),
                'http_code' => $this->getStatusCode401(),
                'status' => __('messages.status_error')
            ], $this->getStatusCode401())->header(__('messages.header_convo'), Session::getId());
        }
    }

    public function userDetails()
    {
        //auth user details
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => __('auth.invalid_token'),
                'http_code' => $this->getStatusCode401(),
                'status' => __('messages.status_error')
            ],$this->getStatusCode401());
        }

        //get full user details
        $User = new User();
        $result = $User->getUserDetails($user['user_account']);

        return response()->json($result, $this->getStatusCode200())->header(__('messages.header_convo'), Session::getId());
    }

    public function registration(Request $request)
    {
        $user = new User();

        //validation
        $rules = [
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'mobile_number' => 'required|unique:users',
            'account_type' => 'required',
            'full_name' => 'required',
            'addr_city' => 'required',
            'store.store_name' => 'required|string',
            'store.street' => 'required|string',
            'store.store_lat' => 'required',
            'store.store_long' => 'required',
            'store.phone_number' => 'required',
            'store.is_always_open' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'http_code' => $this->getStatusCode400(),
                'status' => __('messages.status_error')
            ], $this->getStatusCode400())->header(__('messages.header_convo'), Session::getId());
        }

        $params = $request->all();

        $result = $user->userRegistration($params);

        if ($result['status'] === 'success') {
            //fire an email to the subscriber account
            $this->accountVerificationMail([
                'to_name' => $request->full_name,
                'to_email' => $request->email,
                'activation_link' => url('user/verify') . '?active=' . $user->userVerificationCode . '&account=' . $request->email,
            ]);
        }

        return response()->json($result)->header(__('messages.header_convo'), Session::getId());
    }

    public function verification(Request $request)
    {
        //check user account
        $user = User::where('email', $request->account);

        if (!$user) {
            return response()->json([
                'message' => __('messages.user_not_found'),
                'status' => __('messages.status_error'),
            ]);
        }

        //updater user account to verified status
        try {
            $user->where('active', $request->active)
                ->update(['is_verified' => USER::VERIFIED_USER]);
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

    public function unauthenticated()
    {
        return response()->json([
            'message' => __('auth.unauthenticated'),
            'http_code' => 401,
            'status' => __('messages.status_error')
        ], 401)->header(__('messages.header_convo'), Session::getId());
    }
}
