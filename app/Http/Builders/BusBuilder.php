<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/4/20
 * Time: 18:46
 */

namespace App\Http\Builders;

use App\Models\Enums\OrderShareTypeEnum;
use App\Models\Enums\OrderStatusEnum;
use App\Models\Enums\ShuttleTicketStatusEnum;
use App\Models\Enums\TicketStatusEnum;
use App\Models\Enums\TicketTypeEnum;
use App\Repositories\SettingRepositories;
use App\Repositories\TicketRepositories;
use Carbon\Carbon;

class BusBuilder
{

    /**
     * 班车列表
     * @param $list
     * @param $cursorId
     * @return string
     */
    public static function toBuildLineListHtml($list, $cursorId)
    {
        $html = "
            <div class='empty-list'>
                暂无记录
            </div>
        ";

        if($cursorId>0){
            $html = "";
        }

        if (count($list)) {
            $html = "";
            $search = '/^L/';
            $i = 1;
            foreach ($list as $v) {
                $lineId = $v['line_id'];
                $code = $v['line_code'];
                $name = $v['line_name'];
                $deptName = $v['dept_name'];
                $destName = $v['dest_name'];
                $price = $v['is_discount']==1 ? $v['discount_price'] : $v['price'];
                $status = $v['status'];//status 0：可预约，1：已预约，2:满员，3:即将开放
                $monthlyHtml = "";
                if (!empty($v['monthly_support'])) {
                    $monthlyHtml = "
                        <div class='color-orange font-12'>月票特惠</div>
                    ";
                }

//                if(preg_match($search,$code)) {
//                    $monthlyHtml .= "
//                        <div class='color-orange font-12'>买一赠一</div>
//                    ";
//                }
//                if (in_array($code, ['L202-1','L202-2','L202-3','L202-4','L231-1','L231-2','L231-3','L231-4'])) {
//                    $monthlyHtml .= "
//                        <div class='color-orange font-12'>特价优惠</div>
//                    ";
//                }
//                if (in_array($code, [ 'L315-1','L373-1','L357-1','L329-1','L330-1','L329-3','L329-2','L330-2','L202-5'])) {
//                    $monthlyHtml .= "
//                        <div class='color-orange font-12'>半价优惠</div>
//                    ";
//                }
                if (isset($v['price_desc']) && !empty($v['price_desc'])) {
                    $monthlyHtml .= "
                        <div class='color-orange font-12'>{$v['price_desc']}</div>
                    ";
                }

                $scheduleHtml = "";
                $moreHtml = "";
                if (!empty($v['line_frequency'])) {
                    $shifts = [];
                    foreach ( $v['line_frequency'] as $schedule ){
                        $timeStr = $schedule['line_frequency_date'];
                        if (!in_array($timeStr, $shifts)) {
                            if (count($shifts)<3) {
                                $scheduleHtml .= "
                                    <li>{$timeStr}</li>
                                ";
                            }
                            $shifts[] = $timeStr;
                        }
                    }
                    if (count($shifts)>3)
                    {
                        $info = \GuzzleHttp\json_encode($shifts);
                        $moreHtml = "<span class='more bus-after-v js_more_btn' data-info='{$info}' >更多</span>";
                    }
                }

                switch (intval($status)) {
                    case 3:
                        $moreHtml = "<span class='bus-warning' >暂未运营</span>";
                        break;
                }

                $html .= "
                    <div class='bus-item animated fadeInUp ant-delay-{$i}' data-line-id='{$lineId}'>
                        <div class='bus-header clearfix'>
                            <span class='code'>{$code}</span>
                            <ul class='shifts'>
                                {$scheduleHtml}
                            </ul>
                               {$moreHtml}
                        </div>
                        <div class='bus-body'>
                            <div class='item-bd'>
                                <p class='bd-tt station-before-circle green'>{$deptName}</p>
                                <p class='bd-tt station-before-circle orange'>{$destName}</p>
                            </div>
                            <div class='item-right text-right'>
                                <div class='font-16 bus-after-v text-right relative pr-15'>{$price}元</div>
                                {$monthlyHtml}
                            </div>
                        </div>
                    </div>
                ";
                $i++;
            }
        }
        return $html;
    }

    /**
     * 车票列表
     * @param $list
     * @param $ticketType 0 //班车 1 //摆渡车
     * @return string
     */
    public static function toBuildTicketList($list, $ticketType = [TicketTypeEnum::Bus, TicketTypeEnum::Shuttle])
    {
        $html = "";
        if (count($list)) {
            $i = 1;
            foreach ($list as $v) {
                $type = $v['type'];
                if (in_array($type, $ticketType)) {
                    $ticket = $v[TicketTypeEnum::transformKey($type)];
                     switch (intval($type)) {
                         case TicketTypeEnum::Bus:
                             $html .= self::toBuildBusTicketHtml($ticket, $i);
                             $i++;
                             break;
                         case TicketTypeEnum::Shuttle:
                             $html .= self::toBuildShuttleTicketHtml($ticket, $i);
                             $i++;
                             break;
                     }
                }
            }
        }
        if (empty($html)) {
            $html = EmptyBuilder::toBuildTicketEmptyHtml();
        }
        return $html;
    }

    /**
     * 生成快捷巴士车票
     * @param $ticket
     * @return string
     */
    private static function toBuildShuttleTicketHtml($ticket, $i)
    {
        $now = Carbon::now();
        $afterSeconds = SettingRepositories::showTicketAfterInSeconds();

        $ticketInfo = self::toBuildShuttleTicketInfo($ticket);
        $id = $ticket['ticket_id'];
        $ticketTitle = TicketTypeEnum::transform(TicketTypeEnum::Shuttle);
        $status =  intval($ticket['status']);
        $style = "";
        $showTitle = "";
        $checkTime = isset($ticket['check_time']) ? $ticket['check_time'] : 0;
        if (($checkTime + $afterSeconds > $now->timestamp) && $status != ShuttleTicketStatusEnum::Refund) {
            $status = ShuttleTicketStatusEnum::UnUsed;
        }

        switch ($status ) {
            case ShuttleTicketStatusEnum::Expired:
                $style = "disabled ";
                $showTitle = "已过期";
                break;
            case ShuttleTicketStatusEnum::UnUsed:
                $style = "js_show_ticket_btn";
                $showTitle = "出示车票";
                break;
            case ShuttleTicketStatusEnum::Checked:
                $style = "disabled js_disabled_btn";
                $showTitle = "已验票 ";
                break;
            case ShuttleTicketStatusEnum::Refund:
                $style = "disabled js_disabled_btn";
                $showTitle = "已退票";
                break;
        };
        $btnHtml = "<button class='btn btn-primary full-width btn-s {$style} ' data-info='{$ticketInfo}' >{$showTitle}</button>";
        $ticketType = TicketTypeEnum::Shuttle;
        $line = $ticket['shuttle_line'];

        $html = "
                 <div class='bt-item animated fadeInUp ant-delay-{$i}' id='ticket_id_{$id}' data-id='{$id}' data-type='{$ticketType}'>
                        <div class='bt-header'>
                            <span class='code'>{$line['line_code']} {$ticketTitle}<span class=''>（{$line['business_hour']}）</span></span>
                        </div>
                        <div class='ticket-gap'></div>
                        <div class='bt-body'>
                            <div class='item-bd'>
                                <h4 class='bd-txt'>一人一票过期作废</h4>
                                <h4 class='bd-txt'>验票后10分钟内可继续出示车票</h4>
                            </div>
                            <div class='item-right'>
                                {$btnHtml}
                            </div>
                        </div>
                    </div>
                    ";
        return $html;
    }

    /**
     * 生成班车车票
     * @param $ticket
     * @return string
     */
    private static function toBuildBusTicketHtml($ticket, $i)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $now = Carbon::now();
        $afterSeconds = SettingRepositories::showTicketAfterInSeconds();
        
        $ticketInfo = self::toBuildTicketInfo($ticket);
        $line = $ticket['desc_info'];
        $lineId = $ticket['line_id'];
        $lineCode = $line['line_code'];
        $lineName = $line['line_name'];
        $dept = $line['dept_station_name'];
        $dest = $line['dest_station_name'];
        $deptTime = $line['dept_time_str'];
        $ticketTitle = TicketTypeEnum::transform(TicketTypeEnum::Bus);
        $seats = isset($ticket['date_seats']) ? array_pop($ticket['date_seats']) : [];
        $status =  intval($seats['use_status']);
        $style = "";
        $showTitle = "";
        $checkTime = isset($seats['check_time']) ? $seats['check_time'] : 0;
        if (($checkTime + $afterSeconds > $now->timestamp) && $status != TicketStatusEnum::Refund) {
//            $status = TicketStatusEnum::UnUsed;
        }

        switch ($status ) {
            case TicketStatusEnum::UnPaid:
                $style = "bg-red js_disabled_btn";
                $showTitle = "待支付";
                break;
            case TicketStatusEnum::UnUsed:
                $style = "js_show_ticket_btn";
                $showTitle = "出示车票";
                break;
            case TicketStatusEnum::WaitRemark:
                $style = "bg-orange js_remark_btn";
                $showTitle = "评价";
//                $style = "disabled js_disabled_btn";
//                $showTitle = "已完成 ";
                break;
            case TicketStatusEnum::Finished:
                $style = "disabled js_disabled_btn";
                $showTitle = "完成 ";
                break;
            case TicketStatusEnum::Refund:
                $style = "disabled js_disabled_btn";
                $showTitle = "已退票";
                break;
        };
        $btnHtml = "<button class='btn btn-primary full-width btn-s {$style} ' data-info='{$ticketInfo}' >{$showTitle}</button>";
        $id = $seats['ticket_id'];

        $locHtml = "";
        if ($seats['dept_at']>$today->timestamp && $seats['dept_at']<$tomorrow->timestamp ) {
            $locHtml = "<div class='icon-location-wrap' data-line-id='{$lineId}'><span  class='icon-location' ></span></div>";
        }
        $ticketType = TicketTypeEnum::Bus;
        $html = "
                 <div class='bt-item animated fadeInUp ant-delay-{$i}' id='ticket_id_{$id}' data-id='{$id}' data-type='{$ticketType}'>
                        <div class='bt-header'>
                            <span class='code'>{$lineCode}</span>
                            <span class='name'>{$ticketTitle}</span>
                             {$locHtml}
                        </div>
                        <div class='ticket-gap'></div>
                        <div class='bt-body'>
                            <div class='item-bd'>
                                <h4 class='bd-txt'>{$lineName}</h4>
                                <h4 class='bd-txt'>乘车时间：{$deptTime}</h4>
                                <h4 class='bd-txt'>上车站点：{$dept}</h4>
                                <h4 class='bd-txt'>下车站点：{$dest}</h4>
                            </div>
                            <div class='item-right'>
                                {$btnHtml}
                            </div>
                        </div>
                    </div>
                    ";
        return $html;
    }

    /**
     * 车票列表构造车票数据
     * @param $item
     * @return string
     */
    private static function toBuildTicketInfo($item)
    {
        $seat = array_pop($item['date_seats']);
        $bus = $item['bus'];
        $desc = $item['desc_info'];
        $res = [
            'ticket_id' => $seat['ticket_id'],
            'dept_at' => $seat['dept_at'],
            'dest_at' => $seat['dest_at'],
            'check_time' => isset($seat['check_time']) ? $seat['check_time'] : 0,
            'ticket_color' => $seat['ticket_color'],
            'use_status' => $seat['use_status'],
            'plate' => $bus['plate'],
            'seat' => $seat['seat'],
            'price' => $desc['price'],
            'line_code' => $desc['line_code'],
            'line_name' => $desc['line_name'],
            'ticket_type' => TicketTypeEnum::Bus,
            'dept_date' => Carbon::createFromTimestamp($seat['dept_at'])->format('m月d日'),
            'dept_at_str' => $desc['dept_time_str'],
            'show_color_ahead_in_seconds' => $seat['show_color_ahead_in_seconds'],
//            'show_color_ahead_in_seconds' =>  SettingRepositories::showColorAheadInSeconds()
        ];

        return \GuzzleHttp\json_encode($res);
    }

    /**
     * 快捷巴士车票列表构造车票数据
     * @param $item
     * @return string
     */
    private static function toBuildShuttleTicketInfo($item)
    {
        $item['ticket_type'] = TicketTypeEnum::Shuttle;
        $item['use_status'] = TicketRepositories::shuttleStatusToBusStatus($item['status']);
        $item['dept_at'] = Carbon::now()->modify($item['shuttle_line']['business_start'])->timestamp;
        $item['dest_at'] = Carbon::now()->modify($item['shuttle_line']['business_end'])->timestamp;
//        $item['dept_at'] = Carbon::now()->modify('06:00')->timestamp;
//        $item['dest_at'] = Carbon::now()->modify('23:00')->timestamp;
        return \GuzzleHttp\json_encode($item);
    }



}