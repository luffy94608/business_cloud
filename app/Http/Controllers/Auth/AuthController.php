<?php

namespace App\Http\Controllers\Auth;

use App\Helper\Util;
use App\Http\Controllers\Api\OtherApi;
use App\Http\Controllers\Api\UserApi;
use App\Models\Enums\ErrorEnum;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

//    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/auth/login';

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        parent::__construct();
//        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * 验证码登录
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        $params = [
            'page'=>'page-login',
        ];
        return View::make('auth.login',$params);
    }

    /**
     * 密码登录
     *
     * @return \Illuminate\Http\Response
     */
    public function getLoginPsw()
    {
        $params = [
            'page'=>'page-psw',
        ];
        return View::make('auth.psw',$params);
    }


    /**
     * 注册
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        $params = [
            'page'=>'page-register',
        ];
        return View::make('auth.register',$params);

    }


    /**
     * Display the password reset view for the given token.
     *
     * @return mixed
     */
    public function getReset()
    {
        $params = [
            'page'=>'page-reset',
        ];
        return View::make('auth.reset',$params);
    }

    /**
     * 个人中心
     *
     * @return mixed
     */
    public function account()
    {
//        $data = UserApi::getProfile();
        $profile = [];
//        if (isset($data['code']) && $data['code']===0) {
//            $profile = $data['data']['profile'];
//        } else {
//            if (isset($data['code']) && $data['code']=== ErrorEnum::InvalidToken) {
//                $loginUrl = '/auth/login';
//                $refer = sprintf('%s%s', Config::get('app')['url'], $_SERVER['REQUEST_URI']);
//                $url = sprintf('%s?callback=%s', $loginUrl, urlencode($refer));
//                return redirect($url);
//            }
//        }
        $params = [
            'page'=>'page-account',
            'profile'=>$profile,
        ];
        return View::make('auth.account',$params);
    }

    /**
     * 余额明细
     *
     * @return mixed
     */
    public function cash()
    {
        $data = UserApi::getProfile();
        $profile = [];
        if (isset($data['code']) && $data['code']===0) {
            $profile = $data['data']['profile'];
        }
        $params = [
            'page'=>'page-cash',
            'profile'=>$profile,
        ];
        return View::make('auth.cash',$params);
    }


    /**
     * 优惠券
     *
     * @return mixed
     */
    public function coupons()
    {
        $total = 0;
        $data = UserApi::getProfile();
        if (isset($data['code']) && $data['code']===0) {
            $total = $data['data']['profile']['coupon_count'];
        }
        $params = [
            'total'=>$total,
            'page'=>'page-coupon',
        ];
        return View::make('auth.coupon',$params);
    }

    /**
     * 红包
     *
     * @return mixed
     */
    public function bonus()
    {
        $params = [
            'page'=>'page-bonus',
        ];
        return View::make('auth.bonus',$params);
    }

    /**
     * 退出
     * @return mixed
     */
    public function getLogout()
    {
        UserApi::logout();
//        Session::forget('account_info');
        Util::clearCacheUserInfo();
        return Redirect::to('/auth/login');
    }
}
