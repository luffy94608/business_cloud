<?php

namespace App\Models\Enums;


class BusLineStatusEnum
{

    const Normal                    = 0;
    const Reversed                  = 1;
    const Full                      = 2;
    const Not_Operation             = 3;



    public static function transform($key)
    {
        $transformMap = array(
            self::Normal                        => "可预约",
            self::Reversed                      => "已预约",
            self::Full                          => "满员",
            self::Not_Operation                 => "即将开放",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
