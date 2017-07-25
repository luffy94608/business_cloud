<?php

namespace App\Http\Controllers\Api;



use App\Models\Enums\HttpURLEnum;
use Carbon\Carbon;

class BusApi extends BaseApi
{

    /**
     * 班车线路列表
     * @param $type 0:上班 1:下班
     * @param int $cursorId
     * @param int $timeStamp
     * @param int $past
     * @param string $cityId
     * @return mixed
     */
    public static function getBusList($type, $cursorId=0, $timeStamp=0, $past=0, $cityId = '')
    {
        $url = HttpURLEnum::Bus_Line_List;
        $params = [
//            'city_id'=>$cityId,
            'type'=>intval($type),
            'cursor_id'=>intval($cursorId),
            'is_next'=>intval($past),
            'timestamp'=>intval($timeStamp) ? intval($timeStamp) : 0
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }


    /**
     * 班车线路详情
     * @param $lineId
     * @return mixed
     */
    public static function getBusDetail($lineId)
    {
        $url = HttpURLEnum::Bus_Line_Detail;
        $params = [
            'line_id'=>$lineId,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }


    /**
     * 获取车票列表 根据日期
     * @param $timestamp
     * @return mixed
     */
    public static function getTicketListWithDate($timestamp)
    {
        $url = HttpURLEnum::Ticket_List_With_Date;
        $params = [
            'timestamp'=>intval($timestamp),
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }


    /**
     * 获取有效车票列表
     * @return mixed
     */
    public static function getAvailableTickets()
    {
        $url = HttpURLEnum::Ticket_List_With_Date;
        $params = [
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 获取当月车票购买情况
     * @param $timestampArr
    [
    {
    "year" : 2015,
    "month" : 2,
    },
    {
    "year" : 2015,
    "month" : 3,
    },
    {
    "year" : 2015,
    "month" : 4,
    }
    ]
     *
     * @return mixed
     */
    public static function ticketMonthMap($timestampArr, $type)
    {
        $url = HttpURLEnum::Ticket_Month_Map;
        $months = [];
        $list = [];
        if (is_array($timestampArr)) {
            $list = $timestampArr;
        } else {
            $list[] = $timestampArr;
        }
        foreach ( $list as $v ) {
            $time = Carbon::createFromTimestamp(intval($v));
            $item = [
                'year' => $time->year,
                'month' => $time->month
            ];
            $months[] = $item;
        };

        $params = [
             'months'=>$months,
             'type'=>intval($type),
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 获取车票详情
     * @param $id
     * @return mixed
     */
    public static function getTicketDetail($id)
    {
        $url = HttpURLEnum::Ticket_Detail;
        $params = [
            'ticket_id'=>$id,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * * 验票(班车)
     * @param $ticket
    "tickets":{
    {"id":"xxxxxd","check_time":1235523455},
    {"id":"xxxxxd","check_time":1235523455},
    {"id":"xxxxxd","check_time":1235523455},
    {"id":"xxxxxd","check_time":1235523455},
    }
     * @return mixed
     */
    public static function checkBusTicket($ticket)
    {
        $url = HttpURLEnum::Check_Bus_Ticket;
        $params = [
            'tickets'=>$ticket,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 评价班车
     * @param $ticketId
     * @param $score
     * @param string $comment
     * @return mixed
     */
    public static function remark($ticketId, $score, $comment = '')
    {
        $url = HttpURLEnum::Remark_Bus;
        $params = [
            'ticket_id'=>$ticketId,
            'score'=>$score,
            'comment'=>$comment,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 快捷出示电子车票
     * @param $type  //当type = 0 返回班车结构 type = 1 返回摆渡车结构
     * @return mixed
     */
    public static function quickShowTicket($type)
    {
        $url = HttpURLEnum::Quick_Show_Ticket;
        $params = [
            'type'=>intval($type),
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }


    /**
     * 退票（日票）
     * @param $ticketId 
     * @return mixed
     */
    public static function refund($ticketId)
    {
        $url = HttpURLEnum::Refund;
        $params = [
            'ticket_id'=>$ticketId,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 线路实时位置
     * @param $lineId
     * @return mixed
     */
    public static function busLinePosition($lineId)
    {
        $url = HttpURLEnum::Line_Real_Loc;
        $params = [
            'line_id'=>$lineId,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 线路搜索
     * @param $name
     * @param string $location
     * @return mixed
     */
    public static function searchLine($name, $location = '')
    {
        $url = HttpURLEnum::Line_Search;
        $params = [
            'name'=>$name,
        ];
        if (empty($location)) {
            $params['location'] = $location;
        }
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 获取所有线路
     * @return mixed
     */
    public static function allLine()
    {
        $url = HttpURLEnum::Get_All_Line;
        $params = [
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    //锁座相关

    /**
     * 日票座位信息
     * @param $schedules
     * {
    schedules:[{
    "line_schedule_id": "", //String,1,line_schedule_id
    "bus_schedule_id": "", //String,0-1,bus_schedule_id
    },
    {
    "line_schedule_id": "", //String,1,line_schedule_id
    "bus_schedule_id": "", //String,0-1,bus_schedule_id
    },
    ...
    ]
    }
     * @return mixed
     */
    public static function seatsStatusByDay($schedules)
    {
        $url = HttpURLEnum::Seats_Status_Day;
        $params = [
            'schedules'=>$schedules,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 月票座位信息
     * @param $lineId
     * @param $year
     * @param $month
     * @param $frequency
     * @return mixed
     */
    public static function seatsStatusByMonth($lineId, $year, $month, $frequency)
    {
        $url = HttpURLEnum::Seats_Status_Month;
        $params = [
            'year'      =>$year,
            'month'     =>$month,
            'line_id'   =>$lineId,
            'frequency' =>$frequency,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 锁定座位(日票)
     * @param $scheduleIds
     * @param $seat
     * {
    "bus_schedule_ids": ["123455","123455"] //string array, 1, 班车调度ids
    "seat": 23       //int,1，座位号
    }
     * @return mixed
     */
    public static function lockSeatsByDay($scheduleIds, $seat)
    {
        $url = HttpURLEnum::Lock_Seats_By_Day;
        $params = [
            'bus_schedule_ids'  =>$scheduleIds,
            'seat'              =>intval($seat)
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 锁定座位(月票)
     * @param $lineId
     * @param $year
     * @param $month
     * @param $frequency
     * @param $seat
     * @return mixed
     */
    public static function lockSeatsByMonth($lineId, $year, $month, $frequency, $seat)
    {
        $url = HttpURLEnum::Lock_Seats_By_Month;
        $params = [
            'year'      =>$year,
            'month'     =>$month,
            'line_id'   =>$lineId,
            'seat'      =>intval($seat),
            'frequency' =>$frequency,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 解锁座位(日票)
     * @param $scheduleIds
     * @param $seat
    {
    "bus_schedule_ids": ["22f23faf34d", "2334sddf3424"],    //string array, 1, bus_schedule_ids
    "seat": 23    // int, 1
    }
     * @return mixed
     */
    public static function unLockSeatsByDay($scheduleIds, $seat)
    {
        $url = HttpURLEnum::UnLock_Seats_By_Day;
        $params = [
            'seat'              =>intval($seat),
            'bus_schedule_ids'  =>$scheduleIds,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 解锁座位(月票)
     * @param $lineId
     * @param $year
     * @param $month
     * @param $frequency
     * @param $seat
     * @return mixed
     */
    public static function unLockSeatsByMonth($lineId, $year, $month, $frequency, $seat)
    {
        $url = HttpURLEnum::UnLock_Seats_By_Month;
        $params = [
            'year'      =>$year,
            'month'     =>$month,
            'line_id'   =>$lineId,
            'seat'      =>intval($seat),
            'frequency' =>$frequency,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }


}
