<?php

namespace App\Http\Controllers;

use App\Foodcategory;
use Illuminate\Http\Request;
use App\Traits\StatusHttp;
use Session;

class FoodcategoryController extends Controller
{
    use StatusHttp;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $foodCategories = Foodcategory::all();

        return response()->json([
            'data' => $foodCategories ?  $foodCategories : [],
            'http_code' => $this->getStatusCode200(),
            'status' => __('messages.status_success'),
        ]);
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
     * @param  \App\Foodcategory  $foodcategory
     * @return \Illuminate\Http\Response
     */
    public function show(Foodcategory $foodcategory)
    {
        //
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
}
