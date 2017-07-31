<?php

namespace App\Http\Controllers;

use App\Http\Requests;
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
     * 竞争对手
     * @return mixed
     */
    public function login()
    {

        $params = [
            'page' =>'page-index',
        ];
        return View::make('index.login',$params);
    }

    /**
     * 竞争对手
     * @return mixed
     */
    public function register()
    {

        $params = [
            'page' =>'page-index',
        ];
        return View::make('index.register',$params);
    }


    /**
     * 竞争对手
     * @return mixed
     */
    public function reset()
    {

        $params = [
            'page' =>'page-index',
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
            'page' =>'page-index',
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
            'page' =>'page-index',
        ];
        return View::make('index.business',$params);
    }
}
