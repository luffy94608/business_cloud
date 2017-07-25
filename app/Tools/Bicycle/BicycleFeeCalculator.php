<?php

/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 5/16/16
 * Time: 5:44 PM
 */

namespace App\Tools\Bicycle;
use App\Repositories\SettingRepositories;
use Carbon\Carbon;

class BicycleFeeCalculator
{
    /**
     * 根据提车还车时间计算费用
     *
     * @param $pickUpAt
     * @param $returnAt
     * @return int
     * @throws \Exception
     */
    public static function calculate($pickUpAt, $returnAt)
    {
        $chargePerHour = SettingRepositories::bicycleChargePerHour();

        return intval(ceil($returnAt->diffInSeconds($pickUpAt, true) / (Carbon::MINUTES_PER_HOUR * Carbon::SECONDS_PER_MINUTE))) * $chargePerHour;
    }
}
