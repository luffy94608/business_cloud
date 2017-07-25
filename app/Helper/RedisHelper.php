<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 2/13/17
 * Time: 2:55 PM
 */

namespace App\Helper;


use Illuminate\Support\Facades\Redis;

class RedisHelper
{
    private static $userKey = 'user:token:';
    private static $userId = 'user:id:';

    public static function getOrderNoCount() {
        $count = Redis::incrby('order_no_count', 1);

        if ($count > 9999) {
            Redis::set('order_no_count', 0);
            $count = 0;
        }
        return $count;
    }

    public static function getUserToken($userId)
    {
        $key = self::$userKey . $userId;
        return Redis::get($key);
    }

    public static function setUserToken($userId, $token)
    {
        $key = self::$userKey . $userId;
        return Redis::set($key, $token);
    }

    public static function sendShuttlePosition($shuttlePathId, $positions) {
        Redis::select(3);
        Redis::setEx($shuttlePathId, 10, json_encode($positions));
    }

    public static function getShuttlePosition($shuttlePathId) {
        Redis::select(3);
        $result = Redis::get($shuttlePathId);
        if (is_null($result)) {
            return [];
        }

        return json_decode($result, true);

    }

    public static function getUserToBuySeatNumRecently($userId, $lineId)
    {
        $key = self::$userId . $userId;
        $seatNum = Redis::hget($key, $lineId);
        return $seatNum;
    }

    public static function setUserToBuySeatNumRecently($userId, $lineId, $seatNum)
    {
        $key = self::$userId . $userId;
        Redis::hset($key, $lineId, $seatNum);
    }
}