<?php

namespace App\Http\Controllers\Api;



use App\Models\Enums\HttpURLEnum;
use App\Models\Enums\PayTypeEnum;

class PayApi extends BaseApi
{

    /**
     * 创建订单(日票/月票)
     * @param $lineId
     * @param $dept_id
     * @param $dest_id
     * @param array $scheduleIds
     * @param string $year
     * @param string $month
     * @param string $frequency
     * @param int $seat //座位号，－1代表随机
     * @return mixed
     */
    public static function createContractMulti($lineId, $dept_id, $dest_id, $scheduleIds = [], $year ='' ,$month = '', $frequency = '', $seat = -1)
    {
        $url = HttpURLEnum::Create_Contract_Multi;

        $params=array(
            'departure_station_id'      =>$dept_id,
            'destination_station_id'    =>$dest_id,
            'seat'                      =>intval($seat),//座位号，－1代表随机
            'sign'                      =>'',
        );
        //日票
        if (!empty($scheduleIds)) {
            $params['schedule_ids'] = $scheduleIds;
        }

        //月票
        if (!empty($year) && !empty($month) && !empty($lineId)) {
            $params['line_id'] = $lineId;
            $params['frequency'] = $frequency;
            $params['year'] = intval($year);
            $params['month'] = intval($month);
        }

        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 支付订单
     * @param $type ////0:班车 1：摆渡车 2：充值 3：支付押金  支付类型
     * @param $openId
     * @param $contractId
     * @param $use_coupon //是否使用优惠券 0:不使用 1:使用
     * @param $coupon_id    //使用的优惠券ID
     * @param $use_balance  //是否使用余额 0:不使用 1:使用
     * @param $use_3rd_trade    //是否使用第三方支付 0:不使用 1:使用
     * @param int $trade_channel    //第三方支付渠道，0：alipay，1：wechat  公众号
     * @return mixed
     */
    public static function payContract($type,$openId, $contractId, $use_coupon, $coupon_id='', $use_balance, $use_3rd_trade, $trade_channel=2)
    {
        $url = HttpURLEnum::Pay_Contract;
        $params=array(
            'type'=>$type,
            'open_id'=>$openId,
            'contract_id'=>$contractId,
            'use_coupon'=>intval($use_coupon),
            'coupon_id'=>$coupon_id ? $coupon_id : '',
            'use_balance'=>intval($use_balance),
            'use_3rd_trade'=>intval($use_3rd_trade),
            'trade_channel'=>intval($trade_channel),
        );
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 支付通知
     * @param $outTradeNo
     * @param $status
     * @return mixed
     */
    public static function payNotify($outTradeNo, $status)
    {
        $url = HttpURLEnum::Pay_Notify;
        $params=array(
            'out_trade_no'=>$outTradeNo,
//            'resultStatus'=>$status,
        );
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 取消订单
     * @param $contractId
     * @return mixed
     */
    public static function cancelOrder($contractId)
    {
        $url = HttpURLEnum::Cancel_Contract;
        $params=array(
            'contract_id'=>$contractId,
        );
        $result = self::postRequestData($url, $params);
        return $result;
    }


    /**
     * 创建摆渡车订单
     * @param $lineId
     * @return mixed
     */
    public static function createShuttleContract($lineId)
    {
        $url = HttpURLEnum::Create_Shuttle_Contract;
        $params=array(
            'line_id'=>$lineId,
        );
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 获取支付成功返回数据
     * @param $contractId
     * @return mixed
     */
    public static function paidBusTicket($contractId)
    {
        $url = HttpURLEnum::Get_Paid_Bus_Ticket;
        $params=array(
            'contract_id'=>$contractId,
        );
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 获取支付成功返回数据
     * @param $contractId
     * @return mixed
     */
    public static function paidShuttleTicket($contractId)
    {
        $url = HttpURLEnum::Get_Paid_Shuttle_Ticket;
        $params=array(
            'contract_id'=>$contractId,
        );
        $result = self::postRequestData($url, $params);
        return $result;
    }


    /**
     * 支付快捷巴士订单
     * @param $openId
     * @param $ticketCount //购买张数
     * @param $contractId
     * @param $use_coupon //是否使用优惠券 0:不使用 1:使用
     * @param $coupon_id    //使用的优惠券ID
     * @param $use_balance  //是否使用余额 0:不使用 1:使用
     * @param $use_3rd_trade    //是否使用第三方支付 0:不使用 1:使用
     * @param int $trade_channel    //第三方支付渠道，0：alipay，1：wechat
     * @return mixed
     */
    public static function payShuttle($ticketCount, $openId, $contractId, $use_coupon, $coupon_id='', $use_balance, $use_3rd_trade, $trade_channel = 2)
    {
        $url = HttpURLEnum::Pay_Contract;
        $params=array(
            'type' => PayTypeEnum::Shuttle,
            'buy_count'=>intval($ticketCount),
            'open_id'=>$openId,
            'contract_id'=>$contractId,
            'use_coupon'=>intval($use_coupon),
            'coupon_id'=>$coupon_id ? $coupon_id : '',//座位号，－1代表随机
            'use_balance'=>intval($use_balance),
            'use_3rd_trade'=>intval($use_3rd_trade),
            'trade_channel'=>intval($trade_channel),
        );
        $result = self::postRequestData($url, $params);
        return $result;
    }


}
