<?php

namespace App\Http\Controllers;

use App\Foodcategory;
use Illuminate\Http\Request;
use App\Traits\StatusHttp;
use App\Traits\AccountHelper;
use Session;
use Log;
use Validator;
use App\Store;

class FoodcategoryController extends Controller
{
    use StatusHttp, AccountHelper;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $FoodCategory = new Foodcategory();

        $result = $FoodCategory->getAllFoodCategoryWithStoreInfo();

        return response()->json($result, $this->getStatusCode200())->header(__('messages.header_convo'), Session::getId());
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
        $rules = [
            'store_id' => 'required|exists:stores',
            'food_category_name' => 'required|string|max:191'
        ];

        //validate food category name
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'http_code' => $this->getStatusCode400(),
                'status' => __('messages.status_error')
            ], $this->getStatusCode400())->header(__('messages.header_convo'), Session::getId());
        }

        try {
            $params = [
                'store_id' => $request->store_id,
                'food_category_id' => $this->uuidStoreKeyGeneration(),
                'food_category_name' => strtoupper($request->food_category_name),
            ];

            Foodcategory::create($params);

            return response()->json([
                'message' => __('messages.create_food_cateogry'),
                'http_code' => $this->getStatusCode200(),
                'status' => __('messages.status_success'),
            ], $this->getStatusCode200());
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => $this->getStatusCode500()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Foodcategory  $foodcategory
     * @return \Illuminate\Http\Response
     */
    public function show(Foodcategory $foodcategory)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Foodcategory  $foodcategory
     * @return \Illuminate\Http\Response
     */
    public function edit(Foodcategory $foodcategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Foodcategory  $foodcategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Foodcategory $foodcategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Foodcategory  $foodcategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Foodcategory $foodcategory)
    {
        //
    }

    public function getAllFoodCategoryStoreSpecific($id)
    {
        $store = Store::where('store_id', $id)->first();

        if (!$store) {
            return [
                'message' => __('messages.store_not_found'),
                'status' => __('messages.status_error'),
                'http_code' => $this->getStatusCode404(),
            ];
        }

        $FoodCategory = new Foodcategory();

        $result = $FoodCategory->getSpecificStoreFoodCategoryWithStoreInfo($id);

        return response()->json($result, $this->getStatusCode200())->header(__('messages.header_convo'), Session::getId());
    }
}
