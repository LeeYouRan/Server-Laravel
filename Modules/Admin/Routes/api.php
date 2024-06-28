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

Route::middleware('auth:api')->get('/admin', function (Request $request) {
    return $request->user();
});

Route::group(["prefix"=>"v1/admin","middleware"=>"AdminApiAuth"],function (){
    //登录
    Route::post('login/login', 'v1\LoginController@login');
//    Route::any('excel/export', 'v1\ExcelController@export');
});

Route::any('excel/export', 'v1\ExcelController@export');
