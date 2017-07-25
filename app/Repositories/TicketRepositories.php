<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  




use App\Models\Enums\ShuttleTicketStatusEnum;
use App\Models\Enums\TicketStatusEnum;
use App\Models\Enums\TicketTypeEnum;

class TicketRepositories
{
    /**
     * 多维数组排序 日期车票列表
     * @param $list
     * @return array
     */
    public static function ticketDateListSort($list)
    {
        $result = [];
        if (!empty($list)) {
            $sortArr = [];
            foreach ($list as $item) {
                $ticket = '';
                $status = 0;
                if ($item['type'] == TicketTypeEnum::Bus) {
                    $ticket = $item['bus_ticket'];
                    $status = $ticket['date_seats'][0]['use_status']; //1：未支付，2：未使用，3：待评价 4：已完成 5：已退票，
                }

                if ($item['type'] == TicketTypeEnum::Shuttle) {
                    $ticket = $item['shuttle_ticket'];
                    $status = $ticket['status']; //0:已过期, 1:可使用，2:已检票，3:已退票
                    if ($status ==ShuttleTicketStatusEnum::Expired) {
                        $status = 4;
                    }
                }
                if (!isset($sortArr[$status])) {
                    $sortArr[$status] = [];
                }
                $sortArr[$status][] = $item;
            }
            ksort($sortArr);
            foreach ($sortArr as $v) {
                $result = array_merge($result, $v);
            }
        }
        return $result;
    }

    /**
     * 修改状态
     * @param $status
     * @return int
     */
    public static function shuttleStatusToBusStatus($status)
    {
        $res = 0;
        switch (intval($status)) {
            case ShuttleTicketStatusEnum::UnUsed:
                $res = TicketStatusEnum::UnUsed;
                break;
            case ShuttleTicketStatusEnum::Checked:
                $res = TicketStatusEnum::WaitRemark;
                break;
            case ShuttleTicketStatusEnum::Refund:
                $res = TicketStatusEnum::Refund;
                break;
            case ShuttleTicketStatusEnum::Expired:
                $res = TicketStatusEnum::WaitRemark;
                break;
        }
        return $res;
    }
}