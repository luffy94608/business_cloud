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
    Route::post('/other/track-error', 'Api\OtherController@trackError');

    Route::group(['middleware' => ['auth']], function () {
        Route::post('/user/update', 'Api\UserController@update');
        Route::post('/user/company', 'Api\UserController@companyAnalysis');
        Route::post('/user/business', 'Api\UserController@businessAnalysis');
        Route::post('/get-bid-list', 'Api\IndexController@getBidList');
        Route::post('/get-winner-list', 'Api\IndexController@getBidResultList');
        Route::post('/get-competitor-list', 'Api\IndexController@getCompetitorList');

    });



