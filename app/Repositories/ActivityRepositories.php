<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  


use App\Models\Activity;
use App\Models\ActivityRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ActivityRepositories
{

    /**
     * 获取中奖结果
     * @param int $total
     * @param int $rate
     * @return bool
     */
    public static function getLotteryResult($total = 0,$rate = 0)
    {
        $res = false;
        Log::info(sprintf('$total is ::%s ==$rate :: %s',$total,$rate));
        if($total>0)
        {
            $randNum = mt_rand(1, 10) /10;
            Log::info(sprintf('$randNum is ::%s ==$rate :: %s',$randNum,$rate));
            if($randNum<=$rate){
                $res = true;
            }
        }
        return $res;
    }

    /**
     * 获取抽奖活动详情
     * @return mixed
     */
    public static function getCurrentActivity()
    {
        $res = Activity::orderBy('id', -1)
            ->first();
        return $res;
    }

    /**
     * 获取中奖记录
     * @param $openId
     * @return mixed
     */
    public static function getAwardList($openId)
    {
        $res = ActivityRecord::where('open_id', $openId)
            ->where('status', 1)
            ->orderBy('created_at', -1)
            ->limit(5)
            ->get();
        return $res->all();
    }

    /**
     * 获取最近的抽奖记录
     * @param $aid
     * @param $openId
     * @return mixed
     */
    public static function getRecentAward($aid, $openId)
    {
        $res = ActivityRecord::where('aid', $aid)
            ->where('open_id', $openId)
            ->orderBy('created_at', -1)
            ->first();
        return $res;
    }

    /**
     * 插入中奖记录
     * @param $type
     * @param $code
     * @param $openId
     * @param $aid
     * @return mixed
     */
    public static function insertAwardRecord($type,$code = '',$openId, $aid)
    {
        $activityRecord = new ActivityRecord();
        $activityRecord->aid = $aid;
        $activityRecord->code = $code;
        $activityRecord->open_id = $openId;
        $activityRecord->type = $type;
        $activityRecord->status = empty($code) ? 0 : 1;
        $activityRecord->save();
    }

    /**
     * 获取中奖code
     * @param $type    0班车 1快捷巴士
     * @return mixed
     */
    public static function getDrawCode($aid, $type)
    {
        $key = "bus_lottery_coupon";
        if($type ==1)//快捷巴士
        {
            $key = "shuttle_lottery_coupon";
        }
        $code = Redis::lpop($key);
        return $code ? : '';
    }



}