<?php

namespace App\Http\Controllers\Menu;

use App\Store;
use App\Foodmenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Log;
use Validator;
use App\Traits\AccountHelper;

class StoreFoodmenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'food_menu_name' => 'required|string|max:191',
            'food_menu_price' => 'required|numeric',
            'food_menu_description' => 'required',
            'store_id' => 'required',
            'menu_tags' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'http_code' => $this->getStatusCode400(),
                'status' => __('messages.status_error')
            ], $this->getStatusCode400())->header(__('messages.header_convo'), Session::getId());
        }

        $store = Store::where('store_id', $request->store_id);

        if (!$store) {
            return [
                'message' => __('messages.store_not_found'),
                'status' => __('messages.status_error'),
                'http_code' => $this->getStatusCode404(),
            ];
        }

        $FoodMenu = new Foodmenu();
        $params = [
            'food_menu_name' => $request->food_menu_name,
            'food_menu_description' => $request->food_menu_description,
            'food_menu_price' => $request->food_menu_price,
            'store_id' => $request->store_id,
            'image_uri' => $request->image_uri
        ];

        $result = $FoodMenu->createNewMenuItem($params);

        //store tags

        foreach($request->menu_tags as $tags) {
            $tagsParams = [
                'store_id' => $request->store_id,
                'food_menu_id' => $result['menu_id'],
                'food_category_id' => $tags,
            ];

            $FoodMenu->storeMenuTags($tagsParams);
        }

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
        //
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
