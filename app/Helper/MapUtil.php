<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 28/12/2016
 * Time: 14:14
 */

namespace App\Helper;


use Illuminate\Support\Facades\Log;

class MapUtil
{
    public static function reverseGeo($lnglat)
    {

        try {
            $ak = env('BAIDU_MAP_AK', 'LdUV6F1yaejNTWqPlAnmLk7ZEQqvcR7t');

            $host = 'http://api.map.baidu.com/geocoder/v2/';
            $client = app('http_client');
            $params = [
                'location' => sprintf('%s,%s', $lnglat['lat'], $lnglat['lng']),
                'output' => 'json',
                'ak' => $ak
            ];
            $result = $client->get($host, $params);
            if ($result['status'] == 0) {
                return $result['result'];
            }
        }
        catch (\Exception $e) {
            Log::info($e);
        }

        return null;
    }

    /**
     * 判断点是否多边形内
     *
     * @param $point 测试点
     * @param $polygon 多边形（点数组）
     * @return bool 点在多边形内返回true, 否则返回false
     */
    public static function isPointInPolygon($point, $polygon)
    {
        $rect = self::getBounds($polygon);

        if (!self::isPointInRect($point, $rect)) return false;

        $N = count($polygon);
        $boundOrVertex = true;  // 如果点位于多边形的顶点或边上，也算做在多边形内，直接返回true
        $intersectCount = 0;    // cross points count of x
        $precision = 2e-10; // 浮点类型计算时候与0比较时候的容差
        $p1 = null; // neighbour bound vertices
        $p2 = null;
        $p = $point;    // 测试点

        $p1 = $polygon[0];  // left vertex
        for ($idx = 1; $idx <= $N; $idx++) {    // check all rays

            if (self::equal($p, $p1)) return $boundOrVertex;    // p is an vertex

            $p2 = $polygon[$idx % $N];  // right vertex

            if ($p['lat'] < min($p1['lat'], $p2['lat']) || $p['lat'] > max($p1['lat'], $p2['lat'])) {   // ray is outside of our interests
                $p1 = $p2;
                continue;   // next ray left point
            }

            if ($p['lat'] > min($p1['lat'], $p2['lat']) && $p['lat'] < max($p1['lat'], $p2['lat'])) {   // ray is crossing over by the algorithm (common part of)
                if ($p1['lat'] == $p2['lat'] && $p['lng'] >= min($p1['lng'], $p2['lng'])) { // overlies on a horizontal ray
                    return $boundOrVertex;
                }

                if ($p1['lng'] == $p2['lng']) { // ray is vertical
                    if ($p1['lng'] == $p['lng']) {  // overlies on a vertical ray
                        return $boundOrVertex;
                    } else {
                        $intersectCount += 1;
                    }
                } else {    // cross point on the left side
                    $xinters = ($p['lat'] - $p1['lat']) * ($p2['lng'] - $p1['lng']) / ($p2['lat'] - $p1['lat']) + $p1['lng'];   // cross point of lng
                    if (abs($p['lng'] - $xinters) < $precision) {
                        return $boundOrVertex;
                    }

                    if ($p['lng'] < $xinters) { // before ray
                        $intersectCount += 1;
                    }
                }
            } else {    // special case when ray is crossing through the vertex
                if ($p['lat'] == $p2['lat'] && $p['lng'] <= $p2['lng']) {   // p crossing over p2
                    $p3 = $polygon[($idx+1) % $N]; // next vertex
                    if ($p['lat'] >= min($p1['lat'], $p3['lat']) && $p['lat'] <= max($p1['lat'], $p3['lat'])) { // p.lat lies between p1.lat & p3.lat
                        $intersectCount += 1;
                    } else {
                        $intersectCount += 2;
                    }
                }
            }
            $p1 = $p2;  // next ray left point
        }

        if ($intersectCount % 2 == 0) { // 偶数在多边形外
            return false;
        } else {    // 奇数在多边形内
            return true;
        }
    }

    private static function equal($point1, $point2)
    {
        return $point1['lng'] == $point2['lng'] && $point1['lat'] == $point2['lat'];
    }

    private static function getBounds($polygon)
    {
        // 获取西南和东北的坐标
        $swLng = 180;
        $swLat = 90;
        $neLng = 0;
        $neLat = 0;

        foreach ($polygon as $point) {
            $swLng = min($swLng, $point['lng']);
            $swLat = min($swLat, $point['lat']);
            $neLng = max($neLng, $point['lng']);
            $neLat = max($neLat, $point['lat']);
        }

        $rect = [
            'sw' => [
                'lng' => $swLng,
                'lat' => $swLat,
            ],
            'ne' => [
                'lng' => $neLng,
                'lat' => $neLat
            ]
        ];

        return $rect;
    }


    public static function isPointInRect($point, $rect)
    {
        // 获取西南和东北的坐标
        $swLng = $rect['sw']['lng'];
        $swLat = $rect['sw']['lat'];
        $neLng = $rect['ne']['lng'];
        $neLat = $rect['ne']['lat'];

        return $point['lng'] >= $swLng && $point['lng'] <= $neLng && $point['lat'] >= $swLat && $point['lat'] <= $neLat;
    }

    /**
     * 根据两点间的经纬度计算距离
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return float / 米
     */
    public static function calcDistanceByPoints($lat1, $lng1, $lat2, $lng2)
    {
         $earthRadius = 6367000; //approximate radius of earth in meters

         /*
           Convert these degrees to radians
           to work with the formula
         */

         $lat1 = ($lat1 * pi() ) / 180;
         $lng1 = ($lng1 * pi() ) / 180;

         $lat2 = ($lat2 * pi() ) / 180;
         $lng2 = ($lng2 * pi() ) / 180;

         /*
           Using the
           Haversine formula

           http://en.wikipedia.org/wiki/Haversine_formula

           calculate the distance
         */

         $calcLongitude = $lng2 - $lng1;
         $calcLatitude = $lat2 - $lat1;
         $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
         $calculatedDistance = $earthRadius * $stepTwo;

         return round($calculatedDistance);
    }

}