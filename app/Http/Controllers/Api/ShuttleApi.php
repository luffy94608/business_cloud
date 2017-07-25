<?php

namespace App\Http\Controllers\Api;



use App\Models\Enums\HttpURLEnum;
use Carbon\Carbon;

class ShuttleApi extends BaseApi
{

    /**
     * 线路实时位置
     * @param $lineId
     * @return mixed
     */
    public static function shuttleLinePosition($lineId)
    {
        $url = HttpURLEnum::Shuttle_Line_Position;
        $params = [
            'line_id'=>$lineId,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }


    /**
     * 线路列表
     * @param int $timestamp
     * @param array $loc
     * @return mixed
     */
    public static function LinesList($timestamp = 0, $loc = [])
    {
        $url = HttpURLEnum::Shuttle_Line_List;
        $params = [
//            'timestamp'=>$timestamp ? : Carbon::now()->timestamp,
        ];
        if (!empty($loc)) {
            $params['loc'] = $loc;
        }
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
        $url = HttpURLEnum::Shuttle_Ticket_Detail;
        $params = [
            'ticket_id'=>$id,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 退票 快捷巴士
     * @param $ticketId
     * @return mixed
     */
    public static function refund($ticketId)
    {
        $url = HttpURLEnum::Shuttle_Refund;
        $params = [
            'ticket_id'=>$ticketId,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * * 验票快捷巴士
     * @param $ticket
    "tickets":{
    {"id":"xxxxxd","check_time":1235523455},
    {"id":"xxxxxd","check_time":1235523455},
    {"id":"xxxxxd","check_time":1235523455},
    {"id":"xxxxxd","check_time":1235523455},
    }
     * @return mixed
     */
    public static function checkTicket($ticket)
    {
        $url = HttpURLEnum::Check_Shuttle_Ticket;
        $params = [
            'tickets'=>$ticket,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }


    /**
     * 快捷巴士车票列表
     * @param $type int 0: 全部 1： 可使用
     * @param int $cursorId
     * @param int $timeStamp
     * @param int $past
     * @return mixed
     */
    public static function shuttleTicketList($type, $cursorId=0, $timeStamp=0, $past=0)
    {
        $url = HttpURLEnum::Shuttle_Ticket_List;
        $params = [
            'type'=>intval($type),
            'cursor_id'=>intval($cursorId),
            'is_next'=>intval($past),
            'timestamp'=>intval($timeStamp) ? intval($timeStamp) : 0
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

}
