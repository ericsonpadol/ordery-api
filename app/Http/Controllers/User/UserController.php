<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Traits\StatusHttp;

use Session;
use Validator;
use function GuzzleHttp\Promise\all;

class UserController extends Controller
{
    use StatusHttp;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = User::getAllUsers();

        return response()->json([
            'data' => $data ? $data : []
        ], 200)->header(__('messages.header_convo'), Session::getId());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = new User();

        $result = $user->getUserDetails($id);

        return response()
            ->json($result, $result['http_code'])
            ->header(__('messages.header_convo'), Session::getId());

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => __('messages.user_not_found'),
                'status' => __('messages.status_error'),
                'http_code' => $this->getStatusCode404(),
            ], $this->getStatusCode404());
        }

        $values = $request->except(array('password'));

        $validator = Validator::make($values, [
            'email' => 'filled|email|max:255|unique:users,email,' . $id,
            'mobile_number' => 'string|filled|numeric|unique:users,mobile_number,' . $id,
            'account_type' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'http_code' => $this->getStatusCode400(),
                'status' => __('messages.status_error')
            ], $this->getStatusCode400());
        }

        $userObj = new User();
        $values['id'] = $id;

        $result = $userObj->updateUserAccount($values);

        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => __('messages.user_not_found'),
                'status' => __('messages.status_error'),
                'http_code' => $this->getStatusCode404(),
            ], $this->getStatusCode404());
        }

        $result = User::deactivateAccount($id);

        return response()->json($result);
    }

    public function restoreAccount(Request $request)
    {
        $user = User::withTrashed()->where('email', $request->email)->get();

        if (!$user) {
            return response()->json([
                'message' => __('messages.user_not_found'),
                'status' => __('messages.status_error'),
                'http_code' => $this->getStatusCode404(),
            ], $this->getStatusCode404());
        }

        $id = $user->map(function ($o) {
            return collect($o->toArray())
                ->only(['id', 'email']);
        });

        $result = User::restoreAccount($id);

        return response()->json($result);
    }
}
