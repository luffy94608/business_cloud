<?php

namespace App\Models\Enums;


class TicketMonthTypeEnum
{

    const Day                   = 1;
    const Month                 = 2;



    public static function transform($key)
    {
        $transformMap = array(
            self::Day                        => "日票",
            self::Month                     => "月票",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
