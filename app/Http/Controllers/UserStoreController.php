<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Store;
use Log;
use Validator;
use Session;

use App\Traits\StatusHttp;

class UserStoreController extends Controller
{
    use StatusHttp;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Store::all();

        return response()->json([
            'data' => $stores ? $stores : [],
            'http_code' => $this->getStatusCode200(),
            'status' => __('messages.status_success'),
        ], $this->getStatusCode200())->header(__('messages.header_convo'), Session::getId());
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
        $user = User::where('user_account', $request->user_account);

        if (!$user) {
            return [
                'message' => __('messages.user_not_found'),
                'status' => __('messages.status_error'),
                'http_code' => $this->getStatusCode404(),
            ];
        }

        $rules = [
            'user_account' => 'required|string',
            'store_name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'mobile_number' => 'required|numeric',
            'store_lat' => 'required',
            'store_long' => 'required',
            'store_opens_at' => 'required_if:is_always_open,false',
            'store_closes_at' => 'required_if:is_always_open,false'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'http_code' => $this->getStatusCode400(),
                'status' => __('messages.status_error')
            ], $this->getStatusCode400())->header(__('messages.header_convo'), Session::getId());
        }

        $params = [
            'user_account' => $request->user_account,
            'store_name' => $request->store_name,
            'address' => $request->address,
            'city' => $request->city,
            'store_lat' => $request->store_lat,
            'store_long' => $request->store_long,
            'mobile_number' => $request->mobile_number,
            'phone_number' => $request->phone_number,
            'is_always_open' => $request->is_always_open,
            'store_opens_at' => $request->store_opens_at,
            'store_closes_at' => $request->store_closes_at,
            'zipcode' => $request->zipcode,
            'food_category_id' => $request->food_category_id,
        ];

        $store = new Store();
        $result = $store->createNewStore($params);
        return response()->json($result, $result['http_code'])->header(__('messages.header_convo'), Session::getId());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $store = Store::where('store_id', $id);

        if (!$store) {
            return [
                'message' => __('messages.store_not_found'),
                'status' => __('messages.status_error'),
                'http_code' => $this->getStatusCode404(),
            ];
        }

        $objStore = new Store();
        $data = $objStore->getStoreInformation($id);

        return response()->json([
            'data' => $data ? $data : [],
            'http_code' => $this->getStatusCode200(),
            'status' => __('messages.status_success')
        ]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
