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
     * 免费vip事件
     * @return int
     */
    public static function freeVipSecond()
    {
        $result = 60*60*24*15;
        return $result;
    }


}