<?php

namespace App\Http\Controllers;

use App\Http\Builders\ActivityBuilder;
use App\Http\Controllers\Api\BusApi;
use App\Http\Controllers\Api\OrderApi;
use App\Http\Controllers\Api\OtherApi;
use App\Http\Requests;
use App\Models\Activity;
use App\Models\AirportLocation;
use App\Models\Enums\OrderStatusEnum;
use App\Models\User;
use App\Repositories\ActivityRepositories;
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
     * 主页页面 创建订单页面
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $params = [
            'page' =>'page-index',
        ];
        return View::make('index.index',$params);
    }

    /**
     * 下载页面
     * @return mixed
     */
    public function download()
    {
        $params = [
            'page' =>'page-download',
        ];
        return View::make('other.download',$params);
    }

    /**
     * 用户引导页
     * @return mixed
     */
    public function guide()
    {
        $params = [
        ];
        return View::make('other.guide',$params);
    }

    /**
     *新用户注册
     * @return mixed
     */
    public function activity()
    {
        $params = [
            'page' =>'page-activity',
        ];
        return View::make('other.activity',$params);
    }

    /**
     *投诉建议
     * @return mixed
     */
    public function feedback()
    {
        $config = \Cache::get('hollo_config');
        $complaints = [];
        if (isset($config['complain_items'])) {
            $complaints = $config['complain_items'];
        }
        $lines = [];
        $result = BusApi::allLine();
        if (isset($result['code']) && $result['code'] === 0) {
            $lines = $result['data']['lines'];
        }
        $params = [
            'lines'=>$lines,
            'complaints'=>$complaints,
            'page' =>'page-feedback',
        ];
        return View::make('other.feedback',$params);
    }

    /**
     *抽奖页面
     * @return mixed
     */
    public function lottery()
    {
        $activity = ActivityRepositories::getCurrentActivity();
        switch (intval($activity->status))
        {
            case  1 ://已开启
                $drawInfo = ActivityRepositories::getRecentAward($activity->id,$this->openId);
                if($drawInfo)
                {
                    $contentHtml = ActivityBuilder::toBuildAwardedHtml($drawInfo->type);
                }
                else
                {
                    $contentHtml = ActivityBuilder::toBuildStartHtml();
                }
                break;
            default:
                $contentHtml = ActivityBuilder::toBuildEndHtml();
                break;
        }

        $awardList = ActivityRepositories::getAwardList($this->openId);
        $listHtml = ActivityBuilder::toBuildAwardListHtml($awardList);

        $params = [
            'page' =>'page-lottery',
            'listHtml'=>$listHtml,
            'contentHtml'=>$contentHtml,
        ];

        return View::make('other.lottery',$params);
    }
}
