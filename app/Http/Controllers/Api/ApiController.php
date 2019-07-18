<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;

//helper
use Validator;
use App\Traits\StatusHttp;

class ApiController extends Controller
{
    use StatusHttp;

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($credentials)) {
            $accessToken = auth()->user()->createToken('secret_ordery')->accessToken;

            return response()->json([
                'access_token' => $accessToken,
                'http_code' => $this->getStatusCode200(),
                'status' => __('messages.status_success')
            ], $this->getStatusCode200());
        } else {
            return response()->json([
                'message' => __('messages.unauthorized_login'),
                'http_code' => $this->getStatusCode401(),
                'status' => __('messages.status_error')
            ], $this->getStatusCode401());
        }
    }

    public function userDetails()
    {
        return response()->json([
            'data' => auth()->user(),
            'http_code' => $this->getStatusCode200(),
            'status' => __('messages.status_success'),
        ], $this->getStatusCode200());
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
            'store_name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'http_code' => $this->getStatusCode400(),
                'status' => __('messages.status_error')
            ], $this->getStatusCode400());
        }

        $params = $request->all();

        $result = $user->userRegistration($params);

        return response()->json($result);
    }
}
