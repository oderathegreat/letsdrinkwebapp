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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/6fc8fca0c81a9d449c4fb555201c0c0b/stk-push',"Front\MpesaCallbacksController@receivePayment");
Route::post('/status/stk-push',"Front\MpesaCallbacksController@statusCheck");