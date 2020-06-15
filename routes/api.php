<?php

use Illuminate\Http\Request;
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


Route::get('/clients', 'ClientController@index');
Route::post('/clients', 'ClientController@store');
Route::put('/clients/{id}', 'ClientController@update');
Route::get('/clients/{id}', 'ClientController@show');
Route::delete('/clients/{id}', 'ClientController@destroy');


Route::get('/products', 'ProductController@index');
Route::post('/products', 'ProductController@store');
Route::put('/products/{id}', 'ProductController@update');
Route::get('/products/{id}', 'ProductController@show');
Route::delete('/products/{id}', 'ProductController@destroy');

// Route::apiResource('clients', 'ClientController');
