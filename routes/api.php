<?php

use Illuminate\Support\Facades\Route;

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

Route::apiResource('clients', 'ClientController');
Route::apiResource('products', 'ProductController');

Route::group(['prefix' => 'auth', 'namespace' =>  'Auth'], function () {
    Route::post('/login', 'LoginController@login');
    Route::post('/register', 'RegisterController@create');
    Route::get('/confirm-account/{token}', 'RegisterController@confirmAccount');
});

Route::group(['middleware' => 'jwt.auth'], function () {

    Route::post('/auth/refreshToken', 'Auth\LoginController@refreshToken');
    Route::post('/auth/logout', 'Auth\LoginController@logout');


    Route::group(['middleware' => 'auth.user'], function () {
        Route::get('/test', function () {
            return "API.autorizated";
        });
    });
});
