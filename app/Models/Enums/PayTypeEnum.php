<?php

namespace App\Models\Enums;


class PayTypeEnum
{

    const Bus               = 0;
    const Shuttle           = 1;
    const Recharge          = 2;
    const Deposit           = 3;



    public static function transform($key)
    {
        $transformMap = array(
            self::Bus                       => "班车",
            self::Shuttle                   => "摆渡车",
            self::Recharge                  => "充值",
            self::Deposit                   => "支付押金",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
