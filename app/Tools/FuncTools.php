<?php

namespace App\Tools;


use Carbon\Carbon;

class FuncTools
{


    /**
     * 安全输出 变量
     * @return mixed|string
     */
    public static function secureOutput()
    {
        $args = func_get_args();
        $list = [];
        $res = '';
        foreach($args as $key=>$value){
            if($key == 0) {
                $list = $value;
            } else {
                $res = isset($list[$value]) ? $list[$value] : '';
            }

        }
        return $res;
    }

    /**
     * 获取星期几
     * @param $timestamp
     * @return mixed
     */
    public static function getWeekTitle($timestamp)
    {
        $weekMap = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'];
        $time = Carbon::createFromTimestamp($timestamp);
        return $weekMap[$time->dayOfWeek];
    }

    /**
     * 车票是否在退票时间内
     * @param $timestamp
     * @param $ahead
     * @return bool
     */
    public static function canRefundStatus($timestamp, $ahead)
    {
        $now = Carbon::now();
        $ticketTime = Carbon::createFromTimestamp($timestamp)->subSeconds(intval($ahead));
        $diff =  $now->diffInSeconds($ticketTime, false);
        if ( $diff>0 ) {
            return true;
        } else {
            return false;
        }
    }

}
