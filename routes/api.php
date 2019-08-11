<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//non token based routing
Route::group(['middlware' => ['api', 'secure.content']], function() {
    Route::post('/registration',['as' => 'registration', 'uses' => 'Api\ApiController@registration']);
    Route::post('/login', ['as' => 'login', 'uses' => 'Api\ApiController@login']);
    Route::resource('/security-questions', 'AccountSecurity\UserSecurityQuestionController', ['except' => ['create', 'edit']]);
    Route::get('/account-verification', ['as' => 'verification', 'uses' => 'Api\ApiController@verification']);
    Route::get('/auth-error', ['as' => 'auth-error', 'uses' => 'Api\ApiController@unauthenticated']);
});

Route::group(['middleware' => ['api', 'auth:api', 'secure.content']], function() {
    Route::resource('/users', 'User\UserController', ['except' => ['create', 'edit']]);
    Route::post('/users/restore-account', 'User\UserController@restoreAccount');
    Route::get('/auth/user-details', ['as' => 'user-details', 'uses' => 'Api\ApiController@userDetails']);
    Route::resource('/stores', 'UserStoreController', ['except' => ['create', 'edit']]);
    Route::resource('/food/categories', 'FoodcategoryController', ['except' => ['create', 'edit']]);
    Route::resource('/food/menus', 'Menu\StoreFoodmenuController', ['except' => ['create', 'edit']]);
});

Route::post('oauth/token ', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');