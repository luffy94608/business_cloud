<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  


class SettingRepositories
{
    /**
     * 获取验票后多久继续显示车票
     * @return int
     */
    public static function showTicketAfterInSeconds()
    {
        $result = 60*10;
        return $result;
    }

    /**
     * 车票提前多久变色
     * @return int
     */
    public static function showColorAheadInSeconds()
    {
        $result = 60*10;
        return $result;
    }


}