<?php

namespace App\Models\Enums;


class WebsiteEnum
{

    const IndexTop                     = 0;
    const IndexBottom                  = 1;



    public static function transform($key)
    {
        $transformMap = array(
            self::IndexTop                         => "首页顶部",
            self::IndexBottom                      => "首页底部",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
