<?php

namespace App\Helper;
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 2/10/17
 * Time: 7:04 PM
 */

use App\Exceptions\MessageException;
use App\Models\Bus\BusPath;
use App\Models\User;
use Carbon\Carbon;

class DataUtils
{

    public static function getTimestampFromTimeStr($timeStr, $day)
    {
        if (is_null($day)) {
            $day = Carbon::today();
        }
        return $day->copy()->modify($timeStr)->timestamp;
    }

    public static function generateOrderNo($orderPre = '1')
    {
        $ymd = Carbon::now()->format('ymd');
        $seconds = strval(Carbon::now()->secondsSinceMidnight());
        while (strlen($seconds) < 5) {
            $seconds = '0' . $seconds;
        }
        $currStr = strval(RedisHelper::getOrderNoCount());

        while (strlen($currStr) < 4) {
            $currStr = '0' . $currStr;
        }

        return $orderPre . $ymd . $seconds . $currStr;
    }

    public static function generateId()
    {
        return md5(uniqid(md5(microtime().rand()), true));
    }

    public static function genUUID() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    public static function genShareCode()
    {
        $characters = 'QWERTYUIOPASDFGHJKLZXCVBNM';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($c=0; $c < 10; $c++) {
            $randomString = '';
            for ($i = 0; $i < 6; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            if (is_null(User::where('share_code', $randomString)->first()))
            {
                break;
            }
        }
        if ($c == 10)
        {
            # todo 生成share code 报警
        }
        return $randomString;
    }

    public static function formatDates(array $deptAts)
    {
        $dateMap = [];
        foreach ($deptAts as $deptAt) {
            $deptDt = Carbon::createFromTimestamp($deptAt);
            $key = sprintf('%d月', $deptDt->month);
            if (!array_key_exists($key, $dateMap)) {
                $dateMap[$key] = [];
            }
            $dateMap[$key][] = sprintf('%d日', $deptDt->day);
        }

        $monthDateList = [];
        foreach ($dateMap as $k => $v) {
            $monthDateList[] = sprintf('%s%s', $k, implode('/', $v));
        }

        return implode('；', $monthDateList);
    }

    public static function wrapBusPathStation(BusPath $busPath, $station, $applyRule=true)
    {
        $photos = [];
        if (isset($station['photo']) && !empty(trim($station['photo']))) {
            $photos[] = trim($station['photo']);
        }
        if (isset($station['photos']) &&
            gettype($station['photos']) == 'array' &&
            count($station['photos']) > 0) {
            $photos = $station['photos'];
        }
        return [
            'short_name' => $station['name'],
            'arrived_at' => $applyRule ? RuleEngine::busPathTimeStrTransform($busPath, $station['arrived_at']) : $station['arrived_at'],
            'verbose' => $station['verbose'],
            'location' => [
                'lng' => $station['lng'],
                'lat' => $station['lat']
            ],
            'photos' => $photos,
            'station_id' => $station['_id']
        ];
    }
}
