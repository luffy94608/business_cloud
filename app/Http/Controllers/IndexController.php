<?php

namespace App\Http\Controllers;

use App\Helper\Util;
use App\Http\Requests;
use App\Repositories\UserRepositories;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{

    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 主页
     * @return mixed
     */
    public function index()
    {

        $params = [
            'page' =>'page-index',
        ];
        return View::make('index.index',$params);
    }

    /**
     * 列表页
     * @return mixed
     */
    public function searchList()
    {

        $params = [
            'page' =>'page-index',
        ];
        return View::make('index.list',$params);
    }

    /**
     * 招标主页
     * @return mixed
     */
    public function bidCall()
    {

        $params = [
            'page' =>'page-index',
        ];
        return View::make('index.bid_call',$params);
    }

    /**
     * 中标主页
     * @return mixed
     */
    public function bidWinner()
    {

        $params = [
            'page' =>'page-index',
        ];
        return View::make('index.bid_winner',$params);
    }

    /**
     * 竞争对手
     * @return mixed
     */
    public function rival()
    {

        $params = [
            'page' =>'page-index',
        ];
        return View::make('index.rival',$params);
    }

    /**
     * 登录
     * @return mixed
     */
    public function login()
    {

        $params = [
            'page' =>'page-login',
        ];
        return View::make('index.login',$params);
    }

    /**
     * 退出
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Util::clearCacheUserInfo();
        return redirect()->to('/login');
    }

    /**
     * 注册
     * @return mixed
     */
    public function register()
    {

        $params = [
            'page' =>'page-register',
        ];
        return View::make('index.register',$params);
    }

    /**
     * 个人信息
     * @return mixed
     */
    public function profile()
    {
        $user = UserRepositories::getProfile($this->uid);
        $params = [
            'user'=>$user,
            'page' =>'page-profile',
        ];
        return View::make('index.profile',$params);
    }


    /**
     * 竞争对手
     * @return mixed
     */
    public function reset()
    {
        $params = [
            'page' =>'page-reset',
        ];
        return View::make('index.reset',$params);
    }

    /**
     * 企业数据分析
     * @return mixed
     */
    public function companyStat()
    {

        $params = [
            'page' =>'page-company',
        ];
        return View::make('index.company',$params);
    }

    /**
     * 市场数据分析
     * @return mixed
     */
    public function businessStat()
    {

        $params = [
            'page' =>'page-business',
        ];
        return View::make('index.business',$params);
    }
}
