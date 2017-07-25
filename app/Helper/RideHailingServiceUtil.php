<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 7/27/16
 * Time: 2:14 PM
 */

namespace App\Helper;


use App\Models\DedicatedBus;
use App\Models\DedicatedDriver;
use App\Models\DedicatedDriverPosition;
use App\Models\DedicatedDriverStatus;
use App\Models\DedicatedDriverStatusEnum;
use App\Models\DedicatedFlagEnum;
use App\Models\DedicatedSchedule;
use App\Models\ScheduleStatusEnum;
use App\Models\Setting;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Redis;
use Log;

class RideHailingServiceUtil
{

    /**
     * 根据乘客需求预估价格
     *
     * @param $passengerCount
     * @param $dept
     * @param $dest
     * @param $rideType
     * @param $timeRequirement 'now' or 'booking'
     * @param string $tripType  'single' or 'round'
     * @return float
     */
    public static function calculateApproximatePrice($passengerCount, $dept, $dest, $rideType, $timeRequirement='now', $tripType='single')
    {
        $deptCoord = $dept['lnglat'];
        $destCoord = $dest['lnglat'];

        $result = RideHailingServiceUtil::getNaviDistanceAndDuration($deptCoord['lat'], $deptCoord['lng'], $destCoord['lat'], $destCoord['lng']);
        $distance = $result['distance'];    //返回为米
        $duration = $result['duration'];    //返回的应该是秒

        return RideHailingServiceUtil::doCalculatePrice($passengerCount, $distance, $duration, $rideType, $timeRequirement, $tripType);
    }


    /**
     * 计算实际价格
     *
     * @param $task
     * @return float
     */
    public static function calculateTaskPrice($task)
    {
        $passengerCount = 0;
        $deptAt = $task->loading_at;
        $destAt = $task->arrived_at;
        $rideType = $task->dedicated_bus_type_code;
        $timeRequirement = $task->schedule_type == 1 ? 'booking' : 'now';

        $distance = RideHailingServiceUtil::calculateTaskDistance($task);
        $duration = $destAt->diffInSeconds($deptAt);
        $price = RideHailingServiceUtil::doCalculatePrice($passengerCount, $distance, $duration, $rideType, $timeRequirement);

        return $price;
    }

    /**
     * 根据车型，距离，时间计算价格
     *
     * @param $passengerCount
     * @param $distance
     * @param $duration
     * @param $rideType
     * @param $timeRequirement
     * @param $tripType
     * @return float
     */
    private static function doCalculatePrice($passengerCount, $distance, $duration, $rideType, $timeRequirement, $tripType='single')
    {
        $ride = DedicatedBus::where('code', $rideType)->first();

        if (is_null($ride)) return false;

        $seatCount = $ride->seat_count;

        if ($passengerCount > $seatCount) return false;

        //TODO:算价公式
        //包车费用=起步价+里程进阶费+超时等候费。起步价120元，含20公里运距及1小时运行时间；进阶费率每5公里20元；超时等候费每10分钟10元。实际里程和运行时间由地图功能自动计算。
        $priceConfig = Setting::rideHailingPriceConfig();
//        $priceConfig = [
//            'base_price' => 120.0,
//            'base_mileage' => 20.0,
//            'base_period_in_min' => 60,
//            'mileage_step' => 5,
//            'price_per_mileage_step' => 20.0,
//            'waiting_overdue_step_in_min' => 10,
//            'price_per_waiting_overdue_step' => 10.0
//        ];
        $basePrice = $priceConfig['base_price'];
        $baseMileage = $priceConfig['base_mileage'];
        $basePeriodInMin = $priceConfig['base_period_in_min'];
        $mileageStep = $priceConfig['mileage_step'];
        $pricePerMileageStep = $priceConfig['price_per_mileage_step'];
        $waitingOverdueStepInMin = $priceConfig['waiting_overdue_step_in_min'];
        $pricePerWaitingOverdueStep = $priceConfig['price_per_waiting_overdue_step'];

        $totalPrice = $basePrice;

        $distanceInKm = ceil($distance/1000);
        $durationInMin = ceil($duration/Carbon::MINUTES_PER_HOUR);
        if ($distanceInKm > $baseMileage || $durationInMin > $basePeriodInMin) {
            $accumulatedPrice = 0;
            if ($distanceInKm > $baseMileage) {
                $accumulatedPrice += ceil(($distanceInKm - $baseMileage)/$mileageStep) * $pricePerMileageStep;
            }

            if ($duration > $basePeriodInMin) {
                $accumulatedPrice += ceil(($durationInMin - $basePeriodInMin)/$waitingOverdueStepInMin) * $pricePerWaitingOverdueStep;
            }

            $totalPrice += $accumulatedPrice;
            $totalPrice = intval($totalPrice);
        }

        return $totalPrice;
    }


    /**
     * 专车-匹配拼车
     * @param $passengerCount
     * @param $mobile
     * @param $deptAt
     * @param $dept
     * @param $dest
     * @param $rideType
     * @param string $timeRequirement
     * @return array|null
     */
    public static function searchForCarPoolMatch($passengerCount, $mobile, $deptAt, $dept, $dest, $rideType, $timeRequirement='now')
    {
        $ride = DedicatedBus::where('code', $rideType)->first();

        if (is_null($ride)) return null;

        $seatCount = $ride->seat_count;

        $isOut = RideHailingServiceUtil::isOutTask($dept);
        $schedules = DedicatedSchedule::where('is_out', $isOut)
            ->where('mobile', '!=', $mobile)
            ->where('status', '!=', ScheduleStatusEnum::FREE)
            ->where('preset_at', '<', Carbon::now())    //只在现在用车里找
            ->get();

        foreach ($schedules as $schedule) {
            $result = [
                'can_carpool' => false
            ];
            if ($isOut) {
                $loc = $schedule->end_loc;
                $result = RideHailingServiceUtil::canCarPool(
                    $dest['lnglat']['lat'],
                    $dest['lnglat']['lng'],
                    $loc['lnglat']['lat'],
                    $loc['lnglat']['lng']
                );
            } else {
                $loc = $schedule->start_loc;
                $result = RideHailingServiceUtil::canCarPool(
                    $dept['lnglat']['lat'],
                    $dept['lnglat']['lng'],
                    $loc['lnglat']['lat'],
                    $loc['lnglat']['lng']
                );
            }

            if ($result['can_carpool'] && $schedule->passenger_count + $passengerCount < $seatCount) {
                return [
                    'loc' => $result['loc'],
                    'schedule' => $schedule
                ];
            }

        }

        return null;
    }

    /**
     * 选择和此任务最匹配的司机
     *
     * @param $passengerCount
     * @param $deptAt
     * @param $dept
     * @param $dest
     * @param $rideType
     * @param $timeRequirement 'now' or 'booking'
     * @param string $tripType  'single' or 'round'
     * @return mixed
     */
    public static function dispatchTask($passengerCount, $deptAt, $dept, $dest, $rideType, $timeRequirement='now', $tripType='single')
    {
        $ride = DedicatedBus::where('code', $rideType)->first();

        if (is_null($ride)) return false;

        $seatCount = $ride->seat_count;

        if ($passengerCount > $seatCount) return false;

        $driver = null;
        if ($timeRequirement == 'now') {
            $driver = RideHailingServiceUtil::dispatchInstantTask($dept, $rideType);
        } else {
            $driver = RideHailingServiceUtil::dispatchBookTask($deptAt, $dept, $rideType);
        }

        return $driver;
    }

    /**
     * 派单-立即用车
     * @param $dept
     * @param $rideType
     * @return null
     */
    private static function dispatchInstantTask($dept, $rideType)
    {
        $driverId = null;
        $driver = null;

        $validAt = Carbon::now()->addMinutes(-1);
        $dayEnd = Carbon::tomorrow()->addMinute(-1);
        $radius = 2000;

        //TODO: 首先判断上车点是否在预定义的点附近，则从司机的排班里取，否则按就近原则取
        $isOut = RideHailingServiceUtil::isOutTask($dept, $radius);

        if ($isOut) {   //从机场出发的，司机从机场队列里面选
            $key = 'drivers_at_dept';
            if (Redis::llen($key) > 0) {
                $driverId = Redis::lpop($key);
                //TODO: 司机的排班如何定？管理人员排班还是司机签到式排班？
            }
        } else { //在市区
            //先过滤掉当天当前时间之后有预约任务在身的司机
            $filteredDriverIds = [];
            $tasks = DedicatedSchedule::where('preset_at', '>', $validAt)
                ->where('preset_at', '<', $dayEnd )
                ->where('driver_id', 'exists', true)
                ->get();
            foreach ($tasks as $task) {
                $filteredDriverIds[] = $task->driver_id;
            }

            //在当前无任务的司机中，根据司机的有效的当前位置(最近1分钟传的位置)选最近的
            $driverStatuses = DedicatedDriverStatus::where('status', DedicatedDriverStatusEnum::FREE)
                ->whereNotIn('driver_id', $filteredDriverIds)
                ->where('updated_at', '>', $validAt)
                ->where('current_location.lnglat', [
                    '$near' => [
                        '$geometry' => [
                            'type' => 'Point',
                            'coordinates' => [$dept['lnglat']['lng'], $dept['lnglat']['lat']]
                        ],
                        'distanceField' => 'dist',
                        '$maxDistance' => $radius
                    ]
                ])
                ->orderBy('dist', 'asc')
                ->get();

            foreach ($driverStatuses as $driverStatus) {
                $driverId = $driverStatus->driver_id;
                break;
            }

        }

        if (!is_null($driverId)) {
            $driver = DedicatedDriver::find($driverId);
        }

        return $driver;
    }

    /**
     * 派单-预约用车
     * @param $deptAt
     * @param $dept
     * @param $rideType
     * @return null
     */
    private static function dispatchBookTask($deptAt, $dept, $rideType)
    {
        if ($deptAt->day == Carbon::now()->day) {
            return RideHailingServiceUtil::dispatchTodayTask($deptAt, $dept, $rideType);
        } else {
            return RideHailingServiceUtil::dispatchTomorrowTask($deptAt, $dept, $rideType);
        }
    }

    /**
     * 派单-预约当时用车
     * @param $deptAt
     * @param $dept
     * @param $rideType
     * @return null
     */
    private static function dispatchTodayTask($deptAt, $dept, $rideType)
    {
        $driver = null;

        $validAt = Carbon::now()->addMinutes(-1);
        $dayEnd = Carbon::tomorrow()->addMinute(-1);
        $radius = 2000;

        $isOut = RideHailingServiceUtil::isOutTask($dept, $radius);

        if ($isOut) {
            $key = 'drivers_at_dept';
            if (Redis::llen($key) > 0) {
                $driverId = Redis::lpop($key);
                $driver = DedicatedDriver::find($driverId);
                //TODO: 司机的排班如何定？管理人员排班还是司机签到式排班？
                return $driver;
            }
        }

        $driver = RideHailingServiceUtil::findFreeDriver($deptAt, $dept, $isOut, $radius, $validAt, $dayEnd);

        if ($driver) return $driver;

        $driver = RideHailingServiceUtil::findWorkingDriver($deptAt, $dept, $isOut);

        return $driver;
    }

    /**
     * 派单-预约明天用车
     * @param $deptAt
     * @param $dept
     * @param $rideType
     * @return null
     */
    private static function dispatchTomorrowTask($deptAt, $dept, $rideType)
    {
        $driver = null;

        $key = 'drivers_at_dept_tomorrow';
        if (Redis::llen($key) > 0) {
            $driverId = Redis::lpop($dept);
            $driver = DedicatedDriver::find($driverId);
        }

        return $driver;
    }


    /**
     * 是否是出机场的任务
     * @param $dept
     * @param $radius
     * @return bool
     */
    private static function isOutTask($dept, $radius=2000)
    {
        //TODO: 首先判断上车点是否在预定义的点附近，则从司机的排班里取，否则按就近原则取
        $preDefinedLocations = Setting::rideHailingPreDefinedDeptLocations();

        $isOut = false;
        foreach ($preDefinedLocations as $location) {
            $distance = RideHailingServiceUtil::getDistance(
                $dept['lnglat']['lat'],
                $dept['lnglat']['lng'],
                $location['lnglat']['lat'],
                $location['lnglat']['lng']);
            if ($distance < $radius) {
                $isOut = true;
            }
        }

        return $isOut;
    }


    /**
     * 找一个范围内，在一段时间内空闲的司机
     * @param $deptAt
     * @param $dept
     * @param $isOut
     * @param $radius
     * @param $validStartAt
     * @param $validEndAt
     * @return null
     */
    private static function findFreeDriver($deptAt, $dept, $isOut, $radius, $validStartAt, $validEndAt)
    {
        $driverId = null;
        $driver = null;

        //先过滤掉当天当前时间之后有预约任务在身的司机
        $filteredDriverIds = [];
        $tasks = DedicatedSchedule::where('preset_at', '>', $validStartAt)
            ->where('preset_at', '<', $validEndAt )
            ->where('driver_id', 'exists', true)
            ->get();
        foreach ($tasks as $task) {
            $filteredDriverIds[] = $task->driver_id;
        }

        //在当前无任务的司机中，根据司机的有效的当前位置(最近1分钟传的位置)选最近的
        $driverStatuses = DedicatedDriverStatus::where('status', DedicatedDriverStatusEnum::FREE)
            ->whereNotIn('driver_id', $filteredDriverIds)
            ->where('updated_at', '>', $validStartAt)
            ->where('current_location.lnglat', [
                '$near' => [
                    '$geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$dept['lnglat']['lng'], $dept['lnglat']['lat']]
                    ],
                    'distanceField' => 'dist',
                    '$maxDistance' => $radius
                ]
            ])
            ->orderBy('dist', 'asc')
            ->get();

        $optDriver = [];
        foreach ($driverStatuses as $driverStatus) {
            if ($isOut) {
                $driverId = $driverStatus->driver_id;
                break;
            }
            $driverPos = $driverStatus->current_location['lnglat'];
            $result = RideHailingServiceUtil::getNaviDistanceAndDuration(
                $driverPos['lat'],
                $driverPos['lng'],
                $dept['lnglat']['lat'],
                $dept['lnglat']['lng']
            );

            $estimateArriveAt = Carbon::now()->addSeconds($result['duration']);

            if ($estimateArriveAt > $deptAt) continue; //超过预约时间，则不考虑

            if (!array_key_exists('estimate_arrive_at', $optDriver)) {
                $optDriver = [
                    'estimate_arrive_at' => $estimateArriveAt,
                    'driver_id' => $driverStatus->driver_id
                ];
            } elseif ($estimateArriveAt < $optDriver['estimate_arrive_at']) {
                $optDriver = [
                    'estimate_arrive_at' => $estimateArriveAt,
                    'driver_id' => $driverStatus->driver_id
                ];
            }
        }

        if (array_key_exists('driver_id', $optDriver)) {
            $driverId = $optDriver['driver_id'];
        }

        if (!is_null($driverId)) {
            $driver = DedicatedDriver::find($driverId);
        }

        return $driver;
    }

    /**
     * 查找正在执行任务的，但是可以接任务的司机
     * @param $deptAt
     * @param $dept
     * @param $isOut
     * @return mixed
     */
    private static function findWorkingDriver($deptAt, $dept, $isOut)
    {
        $driver = null;

        $validStatuses = [
            ScheduleStatusEnum::CLAIMED,
            ScheduleStatusEnum::RUNNING
        ];

        $deptAtDay = clone $deptAt->startOfDay();
        //当前有任务的司机
        $tasks = DedicatedSchedule::whereIn('status', $validStatuses)
            ->where('estimate_arrive_at', '<', $deptAt)
            ->where('estimate_arrive_at', '>', $deptAtDay)
            ->orderBy('estimate_arrive_at', 'asc')
            ->get();

        $outTasks = []; //出机场的任务
        $inTasks = []; //回机场的任务
        foreach ($tasks as $task) {
            $task->is_out ? $outTasks[] = $task : $inTasks[] = $task;
        }

        if ($isOut) {
            //回机场的任务
            foreach ($inTasks as $task) {
                $driver = $task->driver;
                return $driver;
            }
        }


        $optTask = [];
        foreach ($outTasks as $task) {
            $endLoc = $task->end_loc['lnglat'];

            if ($isOut) { //从机场出发的任务，找最快回机场的
                $startLoc = $task->start_loc['lnglat'];
                $result = RideHailingServiceUtil::getNaviDistanceAndDuration(
                    $endLoc['lat'],
                    $endLoc['lng'],
                    $startLoc['lat'],
                    $startLoc['lng']
                );
            } else {
                $result = RideHailingServiceUtil::getNaviDistanceAndDuration(
                    $endLoc['lat'],
                    $endLoc['lng'],
                    $dept['lnglat']['lat'],
                    $dept['lnglat']['lng']
                );
            }

            $estimateArriveAt = $task->estimate_arrive_at->addSeconds($result['duration']);

            if ($estimateArriveAt > $deptAt) continue; //超过预约时间，则不考虑

            if (!array_key_exists('estimate_arrive_at', $optTask)) {
                $optTask = [
                    'estimate_arrive_at' => $estimateArriveAt,
                    'task' => $task
                ];
            } elseif ($estimateArriveAt < $optTask['estimate_arrive_at']) {
                $optTask = [
                    'estimate_arrive_at' => $estimateArriveAt,
                    'task' => $task
                ];
            }
        }

        if (array_key_exists('task', $optTask)) {
            $driver = $optTask['task']->driver;
        }

        return $driver;
    }


    /**
     * 判断两个地点是否可以拼车
     *
     * @param $loc1
     * @param $loc2
     * @param $isOut: 从机场出发还是回机场
     * @return array
     */
    private static function canCarPool($loc1, $loc2, $isOut)
    {
        $preDefinedLocations = Setting::rideHailingPreDefinedDeptLocations();

        //TODO: 机场是当做多点还是一点处理，先选定一个点
        $loc = array_shift($preDefinedLocations);
        $locLoc1Res = null;
        $locLoc2Res = null;
        $loc1Loc2Res = null;
        if ($isOut) {
            $locLoc1Res = RideHailingServiceUtil::getNaviDistanceAndDuration(
                $loc['lnglat']['lat'],
                $loc['lnglat']['lng'],
                $loc1['lnglat']['lat'],
                $loc1['lnglat']['lng']
            );
            $locLoc2Res = RideHailingServiceUtil::getNaviDistanceAndDuration(
                $loc['lnglat']['lat'],
                $loc['lnglat']['lng'],
                $loc2['lnglat']['lat'],
                $loc2['lnglat']['lng']
            );
        } else {
            $locLoc1Res = RideHailingServiceUtil::getNaviDistanceAndDuration(
                $loc1['lnglat']['lat'],
                $loc1['lnglat']['lng'],
                $loc['lnglat']['lat'],
                $loc['lnglat']['lng']
            );
            $locLoc2Res = RideHailingServiceUtil::getNaviDistanceAndDuration(
                $loc2['lnglat']['lat'],
                $loc2['lnglat']['lng'],
                $loc['lnglat']['lat'],
                $loc['lnglat']['lng']
            );
        }

        $loc1Loc2Res = RideHailingServiceUtil::getNaviDistanceAndDuration(
            $loc1['lnglat']['lat'],
            $loc1['lnglat']['lng'],
            $loc2['lnglat']['lat'],
            $loc2['lnglat']['lng']
        );

        //TODO: 这是应该还需要返回上车点坐标，这样有助于后序匹配司机

        $result = [
            'can_carpool' => false
        ];

        $thresholdDistInMeter = Setting::rideHailingCarPoolDistThresholdInMeter();

        if ($loc1Loc2Res['distance'] - abs($locLoc1Res['distance'] - $locLoc2Res['distance']) < $thresholdDistInMeter) {
            $result['can_carpool'] = true;

            $locLoc1Res['distance'] > $locLoc2Res['distance'] ? $result['loc'] = $loc1 : $result['loc'] = $loc2;
        }

        return $result;
    }

    /**
     * 计算任务实际的里程数
     *
     * @param $task
     * @return int
     */
    private static function calculateTaskDistance(DedicatedSchedule $task)
    {
        $wayPoints = $task->getWayPoints();
        $prePoint = null;
        $distance = 0;
        foreach ($wayPoints as $point) {
            if (is_null($prePoint)) {
                $prePoint = $point;
            } else {
                $distance += RideHailingServiceUtil::getDistance($prePoint['lat'], $prePoint['lng'], $point['lat'], $point['lng']);
            }
        }
        return $distance;
    }

    /**
     * 计算两点之前导航距离(使用地图api)
     *
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return int
     */
    private static function getNaviDistanceAndDuration($lat1, $lng1, $lat2, $lng2)
    {
        //计算两点之前导航的距离
        $client = new HttpClient();
        $params = [
            'origin' => sprintf('%f,%f', $lat1, $lng1),
            'destination' => sprintf('%f,%f', $lat2, $lng2),
            'mode' => 'driving',
            'region' => '北京',
            'origin_region' => '北京',
            'destination_region' => '北京',
            'output' => 'json',
            'ak' => env('BAIDU_MAP_AK')
        ];

        $queryStr = http_build_query($params);

        $url = 'http://api.map.baidu.com/direction/v1?';

        $url = $url . $queryStr;

        $res = $client->get($url);

        $result = null;
        if ($res->getStatusCode() == 200) {
            $contents = $res->getBody()->getContents();
            Log::debug($contents);
            Log::debug(gettype($contents));
            $jsonObj = json_decode($contents, true);
            $taxi = $jsonObj['result']['taxi'];
            $result = [
                'distance' => $taxi['distance'],
                'duration' => $taxi['duration']
            ];
        }
        return $result;
    }

    /**
     * 根据两点间的经纬度计算距离
     *
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return float
     */
    private static function getDistance($lat1, $lng1, $lat2, $lng2)
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
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }

}