<?php

namespace App\Http\Controllers;

use App\Helper\Util;
use App\Http\Requests;
use App\Repositories\BidRepositories;
use App\Repositories\DataRepositories;
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
        $tender = 0;
        $bid = 0;
        $competitor = 0;
        $tenderToday = 0;
        $bidToday = 0;
        $competitorToday = 0;
        $summary = DataRepositories::getSummaryData($this->uid);
        if (!is_null($summary)) {
            $tender = $summary['tender'];
            $bid = $summary['bid'];
            $competitor = $summary['competitor'];
            $tenderToday = $summary['tender_today'];
            $bidToday = $summary['bid_today'];
            $competitorToday = $summary['competitor_today'];
        }
        $data = [
            'tender'=>$tender,
            'bid'=>$bid,
            'competitor'=>$competitor,
            'tender_today'=>$tenderToday,
            'bid_today'=>$bidToday,
            'competitor_today'=>$competitorToday,
        ];

        $params = [
            'data' =>$data,
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
            'page' =>'page-search-list',
        ];
        return View::make('index.list',$params);
    }

    /**
     * 招标主页
     * @return mixed
     */
    public function bidCall()
    {
        $today = 0;
        $week = 0;
        $month = 0;
        $summary = DataRepositories::getSummaryPageData($this->uid);
        if (!is_null($summary)) {
            $today = $summary['tender_today_total'];
            $week = $summary['tender_week_total'];
            $month = $summary['tender_month_total'];
        }
        $total = $today+$week+$month;
        $data = [
            'today'=>$today,
            'week'=>$week,
            'month'=>$month,
            'today_percent'=>$total ? ($today/$month)*100 : 0,
            'week_percent'=>$total ? ($week/$month)*100 : 0,
            'month_percent'=>$total ? ($month/$month)*100 : 0,
        ];

        $params = [
            'data' =>$data,
            'page' =>'page-bid-call',
        ];
        return View::make('index.bid_call',$params);
    }

    /**
     * 中标主页
     * @return mixed
     */
    public function bidWinner()
    {
        $today = 0;
        $week = 0;
        $month = 0;
        $summary = DataRepositories::getSummaryPageData($this->uid);
        if (!is_null($summary)) {
            $today = $summary['bid_today_total'];
            $week = $summary['bid_week_total'];
            $month = $summary['bid_month_total'];
        }
        $total = $today+$week+$month;
        $data = [
            'today'=>$today,
            'week'=>$week,
            'month'=>$month,
            'today_percent'=>$total ? ($today/$month)*100 : 0,
            'week_percent'=>$total ? ($week/$month)*100 : 0,
            'month_percent'=>$total ? ($month/$month)*100 : 0,
        ];

        $params = [
            'data' =>$data,
            'page' =>'page-bid-winner',
        ];
        return View::make('index.bid_winner',$params);
    }

    /**
     * 竞争对手
     * @return mixed
     */
    public function rival()
    {
        
        $data = [
            'total'=>BidRepositories::getCompetitorListTotal(),
            'power'=>UserRepositories::getMePower($this->uid),
            'company_summary'=>DataRepositories::getCompanySummaryChartData(),
            'category_summary'=>DataRepositories::getCategoryChartData(),
        ];
        $params = [
            'data' =>$data,
            'page' =>'page-rival',
        ];
        return View::make('index.rival',$params);
    }

    /**
     * 竞争对手详情x
     * @param $id
     * @return mixed
     */
    public function rivalDetail($id)
    {
        $data = BidRepositories::getCompetitorDetail($id);
        $chart = [
            'bid'=> DataRepositories::bidStatData(),
            'power'=>DataRepositories::powerStatData(),
            'money'=>DataRepositories::moneyStatData(),
        ];
        $params = [
            'info'=>$data,
            'chart'=>$chart,
            'page' =>'page-rival-detail',
        ];
        return View::make('index.rival_detail',$params);
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
