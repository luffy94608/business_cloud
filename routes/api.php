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
 * 微信相关
 */

Route::any('/wechat', 'Api\WechatController@serve');//微信回调
Route::any('/wechat/create-menu','Api\WechatController@getCreateMenu');//创建menu
Route::any('/wechat/get-access-token', 'Api\WechatController@getAccessToken');//获取token
Route::any('/pay/native', 'Api\WechatPayController@native'); //扫码支付回调
Route::any('/pay/wxpay-notify', 'Api\WechatPayController@notify'); //扫码支付回调
Route::any('/pay/create-pay-code-url', 'Api\WechatPayController@createPayQRcodeUrl'); //生成扫码链接


/**
 * 路由配置
 */
$wechatAuth = ['middleware' => ['wechat.oauth']];
$env = env('APP_ENV');
if ($env == 'local') {
    $wechatAuth = [];
}


/**
 * api 路由
 */
Route::group($wechatAuth, function () {
    Route::post('get-verify-code', 'Api\UserController@verifyCode');
    Route::post('/user/register', 'Api\UserController@register');
    Route::post('/user/login', 'Api\UserController@login');
    Route::post('/user/reset', 'Api\UserController@reset');
    Route::post('/user/update', 'Api\UserController@update');
    Route::post('/other/track-error', 'Api\OtherController@trackError');
    Route::post('/activity/lottery-draw', 'Api\ActivityController@lotteryDraw');

    Route::group(['prefix' => 'bus'], function() {
        Route::post('get-bus-list', 'Api\BusController@getLineList');
    });

    Route::group(['prefix' => 'shuttle'], function() {
        Route::post('shuttle-list', 'Api\ShuttleController@getLineList');
        Route::post('shuttle-real-position', 'Api\ShuttleController@busLinePosition');
    });


    Route::group(['middleware' => ['auth']], function () {
        Route::post('/profile', 'Api\UserController@getProfile');
        Route::post('/user/edit', 'Api\UserController@edit');
        Route::post('/user/feedback', 'Api\UserController@feedback');
        Route::post('get-bill-list', 'Api\UserController@getCashBillList');
        Route::post('get-bonus-list', 'Api\UserController@getBonusList');
        Route::post('get-bonus-detail', 'Api\UserController@getBonusDetail');
        Route::post('get-coupon-list', 'Api\UserController@getCouponList');
        Route::post('exchange-share-code', 'Api\UserController@exchangeCode');

        Route::group(['prefix' => 'bus'], function() {
//            Route::post('get-bus-list', 'Api\BusController@getLineList');
            Route::post('get-date-ticket-list', 'Api\BusController@getTicketListByDate');
            Route::post('ticket-month-map', 'Api\BusController@ticketMonthMap');
            Route::post('refund', 'Api\BusController@busRefund');
            Route::post('check-ticket', 'Api\BusController@checkTicket');
            Route::post('check-off-line-ticket', 'Api\BusController@checkOffLineTicket');
            Route::post('bus-real-position', 'Api\BusController@busLinePosition');
            Route::post('quick-show-ticket', 'Api\BusController@quickShowTicket');
            Route::post('remark', 'Api\BusController@remark');
            Route::post('get-all-lines', 'Api\BusController@allLines');
        });

        Route::group(['prefix' => 'shuttle'], function() {
//            Route::post('shuttle-list', 'Api\ShuttleController@getLineList');
//            Route::post('shuttle-real-position', 'Api\ShuttleController@busLinePosition');
            Route::post('pay-shuttle', 'Api\PayController@payShuttle');
            Route::post('check-ticket', 'Api\ShuttleController@checkTicket');
            Route::post('refund', 'Api\ShuttleController@refund');
        });

        //api
        Route::group(['prefix' => 'pay'], function() {
            //支付
            Route::post('create-order', 'Api\PayController@createOrder');
            Route::post('get-paid-bus-ticket', 'Api\PayController@paidBusTicket');
            Route::post('get-paid-shuttle-ticket', 'Api\PayController@paidShuttleTicket');
            Route::post('cancel-order', 'Api\PayController@cancelOrder');
            Route::post('pay-order', 'Api\PayController@pay');
            Route::post('notify', 'Api\PayController@payNotify');
        });

        //锁座
        Route::group(['prefix' => 'seat'], function() {
            Route::post('status-by-day', 'Api\PayController@busSeatsStatusByDay');
            Route::post('status-by-month', 'Api\PayController@busSeatsStatusByMonth');
            Route::post('lock-or-unlock-by-day', 'Api\PayController@busLockOrUnlockSeatsByDay');
            Route::post('lock-or-unlock-by-month', 'Api\PayController@busLockOrUnlockSeatsByMonth');
        });
    });

});


