<?php

//use Illuminate\Support\Facades\Redirect;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * api 路由
 */
    Route::post('get-verify-code', 'Api\UserController@verifyCode');
    Route::post('/user/register', 'Api\UserController@register');
    Route::post('/user/login', 'Api\UserController@login');
    Route::post('/user/reset', 'Api\UserController@reset');
    Route::post('/user/update', 'Api\UserController@update');
    Route::post('/other/track-error', 'Api\OtherController@trackError');

    Route::group(['middleware' => ['auth']], function () {
        Route::post('/profile', 'Api\UserController@getProfile');
        Route::post('/user/edit', 'Api\UserController@edit');
        Route::post('/user/feedback', 'Api\UserController@feedback');
        Route::post('get-bill-list', 'Api\UserController@getCashBillList');
        Route::post('get-bonus-list', 'Api\UserController@getBonusList');
        Route::post('get-bonus-detail', 'Api\UserController@getBonusDetail');
        Route::post('get-coupon-list', 'Api\UserController@getCouponList');
        Route::post('exchange-share-code', 'Api\UserController@exchangeCode');

    });



