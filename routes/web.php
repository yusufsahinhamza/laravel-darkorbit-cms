<?php

use Illuminate\Support\Facades\Route;

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

Route::post('/auth/check', 'App\Http\Controllers\AuthController@check');
Route::post('/auth/register', 'App\Http\Controllers\AuthController@register');
Route::post('/auth/login', 'App\Http\Controllers\AuthController@login');
Route::post('/auth/logout', 'App\Http\Controllers\AuthController@logout');
Route::post('/auth/get-user', 'App\Http\Controllers\AuthController@getUser');

Route::post('/user/select-company', 'App\Http\Controllers\UserController@selectCompany');
Route::post('/user/change-pilot-name', 'App\Http\Controllers\UserController@changePilotName');
Route::post('/user/change-password', 'App\Http\Controllers\UserController@changePassword');

Route::get('/{any}', function () {
    return view('index');
})->where('any', '.*')->name('index');