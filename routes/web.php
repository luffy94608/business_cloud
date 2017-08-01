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
 * 路由配置
 */

/**
 * 维护模式
 */
Route::get('/maintain',function(){
    return view('maintain');
});                                               

/**
 * 报错页面
 */
Route::get('/error',function(){
    $title = \Illuminate\Support\Facades\Input::get('title');
    return view('layouts.error',['title'=>$title]);
});

/**
 * 路由
 */
Route::get('/login','IndexController@login');
Route::get('/reset','IndexController@reset');
Route::get('/register','IndexController@register');

Route::group(['middleware' => []], function () {
    Route::get('/','IndexController@index');
    Route::get('/search-list','IndexController@searchList');
    Route::get('/bid-call','IndexController@bidCall');
    Route::get('/bid-winner','IndexController@bidWinner');
    Route::get('/rival','IndexController@rival');
    Route::get('/company','IndexController@companyStat');
    Route::get('/business','IndexController@businessStat');
    Route::get('/profile','IndexController@profile');
});



