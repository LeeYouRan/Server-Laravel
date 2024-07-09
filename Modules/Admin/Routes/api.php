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
});
//测试DNF-GM -- 不加登录校验
Route::any('dnf/enum', 'v1\DnfController@enum');
Route::any('dnf/subList', 'v1\DnfController@subList');
Route::any('dnf/propList', 'v1\DnfController@propList');
Route::any('dnf/propNum', 'v1\DnfController@propNum');
Route::any('dnf/multiSend', 'v1\DnfController@multiSend');
Route::any('dnf/multiDefaultSend', 'v1\DnfController@multiDefaultSend');
