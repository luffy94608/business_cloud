<?php

namespace App\Models\Enums;


class ShuttleTicketStatusEnum
{

    const Expired               = 0;
    const UnUsed                = 1;
    const Checked               = 2;
    const Refund                = 3;



    public static function transform($key)
    {
        $transformMap = array(
            self::Expired                           => "已过期",
            self::UnUsed                            => "可使用",
            self::Checked                           => "已检票",
            self::Refund                            => "已退票",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
