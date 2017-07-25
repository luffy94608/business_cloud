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
$wechatAuth = ['middleware' => ['wechat.oauth']];
$env = env('APP_ENV');
if ($env == 'local') {
    $wechatAuth = [];
}
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
 * 微信菜单入口
 */
Route::any('/api/wechat/wechat-menu','Api\WechatController@menu');


/**
 * 项目路由
 */
Route::group($wechatAuth, function () {
    /**
     * 账户相关
     */
    Route::get('auth/login', 'Auth\AuthController@getLogin');
    Route::get('auth/psw', 'Auth\AuthController@getLoginPsw');
    Route::get('auth/logout', 'Auth\AuthController@getLogout');
    Route::get('auth/reset', 'Auth\AuthController@getReset');
    Route::get('/', 'BusController@lines');
    Route::get('/seat', 'BusController@seat');
    /**
     * 快捷巴士
     */
    Route::get('shuttle-map', 'ShuttleController@shuttleMap');
    Route::get('shuttle-list', 'ShuttleController@shuttleList');

    Route::group(['middleware' => ['auth']], function () {
        /**
         * 账户相关
         */
        Route::get('auth/account', 'Auth\AuthController@account');
        Route::get('auth/cash', 'Auth\AuthController@cash');
        Route::get('auth/coupons', 'Auth\AuthController@coupons');
        Route::get('auth/bonus', 'Auth\AuthController@bonus');
        /**
         * 班车相关
         */
        Route::get('pay/{lineId}', 'BusController@pay');
        Route::get('my-order', 'BusController@myOrder');
        Route::get('order-detail/{id}', 'BusController@orderDetail');
        Route::get('bus-map/{id}', 'BusController@map');
        Route::get('bus-location/{id}', 'BusController@location');
        Route::get('remark/{id}', 'BusController@remark');

        /**
         * 快捷巴士
         */
        Route::get('pay-shuttle/{lineId}', 'ShuttleController@payShuttle');
        Route::get('ticket-detail/{id}', 'ShuttleController@ticketDetail');

        Route::group(['prefix' => 'other'], function () {
            Route::get('feedback', 'IndexController@feedback');
        });
    });
});

/**
 * 其他相关
 */
Route::group($wechatAuth, function () {

    Route::get('download', 'IndexController@download');
    Route::get('guide', 'IndexController@guide');
    Route::get('activity', 'IndexController@activity');

    Route::group(['prefix' => 'other'], function () {
        Route::get('lottery', 'IndexController@lottery');

    });
});


