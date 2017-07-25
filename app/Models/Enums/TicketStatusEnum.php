<?php

namespace App\Models\Enums;


class TicketStatusEnum
{

    const UnPaid               = 1;
    const UnUsed               = 2;
    const WaitRemark           = 3;
    const Finished             = 4;
    const Refund               = 5;



    public static function transform($key)
    {
        $transformMap = array(
            self::UnPaid                        => "未支付",
            self::UnUsed                        => "未使用",
            self::WaitRemark                    => "待评价",
            self::Finished                      => "已完成",
            self::Refund                        => "已退票",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
