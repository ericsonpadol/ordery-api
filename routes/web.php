<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->middleware('guest');

Route::get('/app.info', function() {
    echo '<pre>' ;
    echo '<h1>' . config('app.name') . ': Health-Check</h1>';
    foreach ($_SERVER as $key => $value) {
        echo '<strong>' . $key . ':</strong> ' . $value .'<br/>';
    }
    echo '</pre>';
});
// Auth::routes();

Route::get('auth/register', [
    'as' => 'register',
    'uses' => 'Auth\RegisterController@register '
  ]);

Route::get('/home', 'HomeController@index')->name('home');
