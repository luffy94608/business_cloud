<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  


use Carbon\Carbon;

class BusRepositories
{
    /**
     * 按班次正序排序
     * @param $list
     */
    public static function busShiftListSort(&$list)
    {
        if (!empty($list)) {
            $sortStatus = [];
            $sortTime = [];
            foreach ($list as $item) {
                $sortStatus[] = $item['line_schedule_status'];
                $sortTime[] = $item['line_schedule_date'];
            }
            array_multisort($sortStatus, SORT_ASC, $sortTime, SORT_ASC, $list);
        }
    }

    /**
     * 获取最近的可购买班次
     * @param $lineSchedules
     * @return string
     */
    public static function getRecentlyCanBuyShift($lineSchedules)
    {
        $result = '';
        self::busShiftListSort($lineSchedules);
        if (count($lineSchedules)) {
            $current = array_shift($lineSchedules);
            $result = $current['dept_time'];
        }
        return $result;
    }

    /**
     * @param $shifts
     * @return array
     */
    public static function getShiftMap($shifts)
    {
        $res = [];
        if ($shifts) {
            foreach ($shifts as $shift) {
                $res[$shift['line_frequency_date']] = $shift;
            }
        }
        return $res;
    }

    /**
     * 调度根据班次聚合
     * @param $lineSchedules
     * @return array
     */
    public  static function toShiftGroupData($lineSchedules)
    {
        $result = [];
        if (!empty($lineSchedules)) {
            foreach ($lineSchedules as $lineSchedule) {
                $key = $lineSchedule['dept_time'];
                if (!isset($result[$key])) {
                    $result[$key] = [];
                }
                $result[$key][] = $lineSchedule;
            }
        }
        return $result;
    }

    /**
     * 获取月票优惠信息 文案
     * @param $monthlySchedule
     * @param $monthlyPriceRule
     * @param $paidTicketCount
     * @return string
     */
    public static function getMonthlyDiscountDesc($monthlySchedule, $monthlyPriceRule, $paidTicketCount)
    {
        if (empty($monthlySchedule)) {
            return '';
        }
        $now = Carbon::now();
        $month = $now->month;
        $ticketMonth = $monthlySchedule['month'];
        $rules = $month == $ticketMonth ? $monthlyPriceRule['current'] : $monthlyPriceRule['next'];
        $baseNum = $month == $ticketMonth ? $paidTicketCount['current'] : $paidTicketCount['next'];

        $total = $baseNum + $monthlySchedule['days'];
        $list = [];
        $listStrArr = [];
        for ($i = $baseNum+1; $i<=$total; $i++) {
            $status = false;
            foreach ($rules  as $rule) {
                if($i>=$rule['begin'] && $i<=$rule['end']){
                    $tk = $rule['value']*100;

                    if (!isset($list[$tk])) {
                        $list[$tk] = 0;
                    }
                    $list[$tk]+=1;
                    $status=true;
                }
            }
            if(!$status){
                if (!isset($list[100])) {
                    $list[100] = 0;
                }
                $list[100]+=1;
            }
        }

        foreach ($list as $k=>$v){
            $discount = floor($k/10);
            $title = $discount==10 ? '原价' : $discount.'折';
            $str = sprintf('%s%s张', $title, $v);
            array_push($listStrArr, $str);
        }
        $result = implode('，', $listStrArr);
        return $result;
    }


    /**
     * 合并上下车站点 并添加tag
     * @param $upStation
     * @param $downStation
     * @return array
     */
    public static function mergeStationWithType($upStation, $downStation)
    {
        $stations = [];
        if (!empty($upStation)) {
            foreach ( $upStation as $usItem ) {
                $usItem['station_type'] = 'up';
                $stations[] = $usItem;
            }
        }
        if (!empty($downStation)) {
            foreach ( $downStation as $dsItem ) {
                $dsItem['station_type'] = 'down';
                $stations[] = $dsItem;
            }
        }
        return $stations;
    }


}