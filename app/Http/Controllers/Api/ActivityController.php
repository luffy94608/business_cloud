<?php

namespace App\Http\Controllers\Api;

use App\Http\Builders\DataBuilder;
use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use App\Models\Enums\PayTypeEnum;
use App\Repositories\ActivityRepositories;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
{
    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    
    /**
     * 抽奖
     * @param Request $request
     * @return mixed
     */
    public function lotteryDraw(Request $request)
    {
        $pattern = [
            'type' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $params['open_id']=$this->openId;

        $info = ActivityRepositories::getCurrentActivity();
        if(empty($info))
        {
            return response()->json((new ApiResult(-1, '活动不存在', []))->toJson());
        }
        $drawInfo = ActivityRepositories::getRecentAward($info['id'],$params['open_id']);
        if($drawInfo)
        {
            return response()->json((new ApiResult(-1, '您已经抽奖，下次活动即将开始', []))->toJson());
        }

        if($params['type'] == 0)//班车
        {
            $total = $info['bus_total'];
            $rate = $info['bus_rate'];
        }
        else//快捷巴士
        {
            $total = $info['schedule_total'];
            $rate = $info['schedule_rate'];
        }

        $code = '';
        $res = ActivityRepositories::getLotteryResult(intval($total),floatval($rate));
        Log::info('getLotteryResult'.$res);
        if($res)
        {
            $code = ActivityRepositories::getDrawCode($info['id'],$params['type']);
            Log::info('getLotteryResult code'.$code);
        }

        if(!empty($code))
        {
            $html = DataBuilder::toGetItModalHtml($code);
        }
        else
        {
            $html = DataBuilder::toNotGetItModalHtml();
        }

        //插入抽奖记录
        ActivityRepositories::insertAwardRecord($params['type'],$code,$this->openId,$info['id']);

        $awardList = ActivityRepositories::getAwardList($this->openId);
        $listHtml = DataBuilder::toBuildAwardListHtml($awardList);

        $data = [
            'listHtml'=>$listHtml,
            "contentSection"=>DataBuilder::toBuildAwardedHtml($params['type']),
            "html"=>$html
        ];
        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());

    }
}
