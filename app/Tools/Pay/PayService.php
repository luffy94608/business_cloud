<?php

/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 2/13/17
 * Time: 11:20 AM
 */
namespace App\Tools\Pay;

use App\Models\ApiResult;
use App\Models\Enum\HttpUrlEnum;
use App\Models\Enum\ErrorEnum;
use App\Models\Enum\ServiceName;
use App\Models\Coupon\Coupon;

class PayService
{
    /**
     * @param $orderId //订单ＩＤ
     * @param $tradeChannel //0支付宝，1微信 2，公众号
     * @param $orderInfo //显示信息
     * @param $orderType //订单类型，OrderTypeEnum
     * @param $totalFee //订单金额
     * @param $appId  //支付id
     * @return string  //签名
     */
    public function getSign($orderId, $tradeChannel, $orderInfo, $orderType, $totalFee, $appId, $openId = '') {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::GetSign, [
            'app_id' => $appId,
            'business_id' => $orderId,
            'payment_type' => $tradeChannel,
            'total_fee' => $totalFee,
            'order_info' => $orderInfo,
            'custom_type' => $orderType,
            'open_id' => $openId
        ]);
        if ($responseData['code'] != 0) {
            return false;
        }
        $request = request();
        if ($tradeChannel != 0)
        {
            $clientType = strtolower($request->header(env('HEADER_PREFIX') . '-OS', ''));
            if (in_array($clientType, ['ios', 'android']))
            {
                $versionCode =intval($request->header(env('HEADER_PREFIX') . '-Version', 0));
                if ($versionCode > 439)
                {
                    $responseData['data']['sign']['outtradeno'] = $orderId;
                }
                else{
                    $old = [
                        "app_id" => $responseData['data']['sign']['appid'],
                        "partner_id" => $responseData['data']['sign']['partnerid'],
                        "prepay_id" => $responseData['data']['sign']['prepayid'],
                        "package" => $responseData['data']['sign']['package'],
                        "nonce_str" => $responseData['data']['sign']['noncestr'],
                        "time_stamp" => $responseData['data']['sign']['timestamp'],
                        "sign" => $responseData['data']['sign']['sign'],
                        "out_trade_no" => $orderId
                    ];
                    return $old;
                }
            }
            else
            {
                $responseData['data']['sign']['out_trade_no'] = $orderId;
            }
        }


        return $responseData['data']['sign'];
    }


    /**
     * @param $orderId
     * @param $appId
     * @param bool $cancel  //未支付时取消订单
     * @return bool
     */
    public function queryOrder($orderId, $appId, $cancel=false) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::Query, [
            'app_id' => $appId,
            'business_id' => $orderId,
            'cancel' => $cancel
        ]);

        if ($responseData['code'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * 退款
     * code 0 退款成功  -49003已退过款  其它退不成功
     * -49003 可以获取退款实际时间$responseData['data']['refund_at']  更新订单时间
     * @param $orderId
     * @param $refundFee
     * @param $appId
     */
    public function refund($orderId, $refundFee, $appId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::Refund, [
            'app_id' => $appId,
            'business_id' => $orderId,
            'refund_fee' => $refundFee
        ]);

        return $responseData['code'] == 0;
    }

    /**
     * 0 取消成功  -49000  已支付 -49003 已退款 -49005 取消失败
     * @param $orderId
     * @param $appId
     * @return mixed
     */
    public function cancel($orderId, $appId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::Cancel, [
            'app_id' => $appId,
            'business_id' => $orderId
        ]);

        return $responseData;
    }

    /**
     * 通过支付订单号获取orderId 支付宝客户端回调时使用
     * @param $outTradeNo
     * @param $appId
     * @return bool
     */
    public function getOrderIdByOutTradeNo($outTradeNo, $appId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::BusinessId, [
            'app_id' => $appId,
            'out_trade_no' => $outTradeNo
        ]);
        if ($responseData['code'] == 0) {
            return $responseData['data']['business_id'];
        }
        return false;
    }

    /**
     * 根据id获取红包
     * @param $userId
     * @param $couponIds
     * @param $appId
     * @return array
     */
    public function getCouponByIds($userId, $couponIds, $appId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::GetCouponInfo, [
            'app_id' => $appId,
            'user_id' => $userId,
            'coupon_ids' => $couponIds
        ]);

        if ($responseData['code'] == 0) {
            $coupons = [];
            foreach ($responseData['data']['coupons'] as $coupon) {
                $coupons[] = new Coupon($coupon, $coupon['expired'] == 0 && !$coupon['used']);
            }
            return $coupons;
        }
        return [];
    }

    /**
     * 发送红包
     * @param $userIds
     * @param $mobiles
     * @param $couponType
     * @param $expireTime
     * @param $couponContent
     * @param $remark
     * @param $appId
     * @return bool
     */
    public function sendCoupon($userIds, $mobiles, $couponType, $expireTime, $couponContent, $remark, $appId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::SendCoupon, [
            'app_id' => $appId,
            'user_ids' => $userIds,
            'mobiles' => $mobiles,
            'coupon_type' => $couponType,
            'expire_time' => $expireTime,
            'coupon_content' => $couponContent,
            'remark' => $remark
        ]);

        if ($responseData['code'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * 绑定手机获得的红包到用户
     * @param $userId
     * @param $mobile
     * @param $appId
     * @return bool
     */
    public function BindCouponsToUserByMobile($userId, $mobile, $appId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::SendCouponByMobile, [
            'app_id' => $appId,
            'user_id' => $userId,
            'mobile' => $mobile
        ]);

        if ($responseData['code'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * 获取红包列表
     * @param $userId
     * @param $status   0可用，1不可用，2全部
     * @param $couponTypes
     * @param $past
     * @param $timestamp
     * @param $limit
     * @param $cursor
     * @param $appId
     * @return array
     */
    public function getCouponsList($userId, $status, $couponTypes, $past, $timestamp, $limit, $cursor, $appId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::GetCoupon, [
            'app_id' => $appId,
            'user_id' => $userId,
            'status' => $status,
            'coupon_types' => $couponTypes,
            'past' => $past,
            'timestamp' => $timestamp,
            'limit' => $limit,
            'cursor' => $cursor
        ]);

        if ($responseData['code'] == 0) {
            $coupons = [];
            foreach ($responseData['data']['coupons'] as $coupon) {
                $coupons[] = new Coupon($coupon, $coupon['expired'] == 0 && !$coupon['used']);
            }
            return $coupons;
        }
        return [];
    }

    /**
     * 使用红包
     * @param $userId
     * @param $couponIds
     * @param $appId
     * @return bool
     */
    public function useCoupon($userId, $couponIds, $appId, $orderId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::UseCoupon, [
            'app_id' => $appId,
            'user_id' => $userId,
            'coupon_ids' => $couponIds,
            'business_id' => $orderId
        ]);

        if ($responseData['code'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * 创建红包集合
     * @param $couponType
     * @param $couponContent
     * @param $packageExpireTime
     * @param $remark
     * @param $couponCount
     * @param $couponExpireType
     * @param $expireNum
     * @param $appId
     * @return bool
     */
    public function createCouponPackage($couponType, $couponContent, $packageExpireTime, $remark, $couponCount, $couponExpireType, $expireNum, $appId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::CreateCouponPackage, [
            'app_id' => $appId,
            'coupon_type' => $couponType,
            'coupon_content' => $couponContent,
            'package_expire_time' => $packageExpireTime,
            'remark' => $remark,
            'coupon_count' => $couponCount,
            'coupon_expire_type' => $couponExpireType,
            'expire_num' => $expireNum
        ]);

        if ($responseData['code'] == 0) {
            return $responseData['data']['package_id'];
        }
        return false;
    }


    /**
     * 从红包集合中获取红包   userId mobile 只能传一个，另一个传null
     * @param $packageId
     * @param $userId
     * @param $mobile
     * @param $appId
     * @return bool
     */
    public function getCouponByPackage($packageId, $userId, $mobile, $appId) {
        $data = [
            'app_id' => $appId,
            'package_id' => $packageId
        ];
        if (isset($userId)) {
            $data['user_id'] = $userId;
        }
        else {
            $data['mobile'] = $mobile;
        }

        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::GetCouponByPackage, $data);

        if ($responseData['code'] == 0) {
            $coupon = $responseData['data']['coupon'];
            return new Coupon($coupon, true);
        }
        return $responseData['code'];
    }

    /**
     * 创建红包码
     * @param $count
     * @param $couponType
     * @param $expireTime
     * @param $couponContent
     * @param $remark
     * @param $appId
     * @return bool
     */
    public function createCodeCoupon($count, $couponType, $expireTime, $couponContent, $remark, $appId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::CreateCodeCoupon, [
            'app_id' => $appId,
            'count' => $count,
            'coupon_type' => $couponType,
            'expire_time' => $expireTime,
            'coupon_content' => $couponContent,
            'remark' => $remark
        ]);

        if ($responseData['code'] == 0) {
            return $responseData['data']['codes'];
        }
        return [];
    }

    /**
     * 通过code 获取优惠券    -40001 code 不正确 -41003 已使用
     * @param $code
     * @param $userId
     * @param $appId
     * @return array
     */
    public function getCouponByCode($code, $userId, $appId) {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::GetCouponByCode, [
            'app_id' => $appId,
            'code' => $code,
            'user_id' => $userId
        ]);

        if ($responseData['code'] == 0) {
            $couponData = $responseData['data']['coupon'];
            return new Coupon($couponData, $couponData['expired'] == 0);
        }
        return [];
    }
    
    public function getValidCouponPackage($userId, $appId, $past, $cursorId, $timestamp, $limit)
    {
        $responseData = app(ServiceName::HttpClient)->postJson(HttpUrlEnum::GetValidCouponPackage, [
            'app_id' => $appId,
            'user_id' => $userId,
            'past' => $past,
            'timestamp' => $timestamp,
            'cursor' => $cursorId,
            'limit' => $limit
        ]);

        if ($responseData['code'] == 0) {
            return $responseData['data']['coupon_packages'];
        }
        return [];
    }
}