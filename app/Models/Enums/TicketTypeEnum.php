<?php

namespace App\Models\Enums;


class TicketTypeEnum
{

    const Bus                   = 0;
    const Shuttle               = 1;


    /**
     * 显示名称
     * @param $key
     * @return mixed|string
     */
    public static function transform($key)
    {
        $transformMap = array(
            self::Bus                        => "班车车票",
            self::Shuttle                    => "快捷巴士",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }

    /**
     * key
     * @param $key
     * @return mixed|string
     */
    public static function transformKey($key)
    {
        $transformMap = array(
            self::Bus                        => "bus_ticket",
            self::Shuttle                    => "shuttle_ticket",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }

    /**
     * 车票列表key
     * @param $key
     * @return mixed|string
     */
    public static function transformListKey($key)
    {
        $transformMap = array(
            self::Bus                        => "bus_tickets",
            self::Shuttle                    => "shuttle_tickets",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
