<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 10/02/2017
 * Time: 16:26
 */

namespace App\Helper;


use App\Models\Bus\BusPath;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\BusPathScheduleRepositories;
use App\Repositories\SettingRepositories;
use Carbon\Carbon;

class RuleEngine
{
    public static function getMaxScheduleCountForBusPath(BusPath $busPath)
    {
        $maxScheduleCount = 1;
        if (strpos($busPath->code, 'L') === 0) {    //L开头的线路按单天放
            $maxScheduleCount = 1;
        } elseif (strpos($busPath->code, 'HX') === 0) { //HX开头的线路按单天放
            $maxScheduleCount = 1;
        } else {
            $maxScheduleCount = SettingRepositories::maxScheduleCountForBusPath();
        }

        return $maxScheduleCount;
    }

    public static function getShuttleLinePrice($path)
    {
        $originalPrice = $path->price;

        $r = SettingRepositories::getShuttleDiscountActivityPeroid();
        $startAt = $r[0];
        $endAt = $r[1];

        if ($startAt <= Carbon::now()->timestamp && Carbon::now()->dayOfWeek == 5) {
            if ($originalPrice > 1.0) {
                $originalPrice = 1.0;
            }
        }
        return $originalPrice;
    }

    public static function getPayId($type, $option=null)
    {
        return env('APP_ID');
    }


    public static function busPathTimeStrTransform(BusPath $busPath, $timeStr, $day = null)
    {
        $day = $day ?: Carbon::today();

        $timeDT = $day->modify($timeStr);

        if (in_array($busPath->code, SettingRepositories::getHXLineCodes()) &&
            in_array($day->dayOfWeek, [6, 0])) {
            $hour = 1;
            $minute = 0;
            if ($busPath->code == 'HX001') {
                $hour = 1;
                $minute = 30;
            }
            $timeDT->addHours($hour)->addMinutes($minute);
        }

        return $timeDT->format('H:i');

    }

    /**
     * 线路编号转换
     *
     * @param BusPath $busPath
     * @param User $user
     * @return mixed
     */
    public static function busPathCodeTransform(BusPath $busPath, User $user)
    {
        $code = $busPath->code;

        if ($code == SettingRepositories::getJiutianlijianLineCode()) {
            if ($user && in_array($user->name, SettingRepositories::getJiutianlijianMobiles())) {
                $code = SettingRepositories::getJiutianlijianLineName();
            }
        } elseif (in_array($code, SettingRepositories::getDaneiCodes())) {
            $code = SettingRepositories::getDaneiName();
        }

        return $code;
    }

}
