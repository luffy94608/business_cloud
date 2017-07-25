<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 21/02/2017
 * Time: 16:21
 */

namespace App\Tools\Bus;


use App\Models\Bus\BusPath;
use App\Models\Enum\BusTicketTypeEnum;
use App\Models\Order\Order;
use App\Models\User;
use App\Repositories\BusTicketRepositories;
use App\Repositories\SettingRepositories;
use App\Repositories\WorkDayRepositories;
use Carbon\Carbon;

class BusTicketPriceCalculator
{
    /**
     * 计算月票的票价信息
     *
     * @param BusPath $busPath
     * @param Carbon $monthDT
     * @param User $user
     * @return array
     */
    public static function calculateTicketPriceForMonth(BusPath $busPath, Carbon $monthDT, User $user)
    {
        $singleTicketPrice = $busPath->distance;
        $totalPrice = 0.0;
        $maxRate = 0;

        $workDays = WorkDayRepositories::getWorkDayOfBusPath($busPath, $monthDT->year, $monthDT->month);
        $workDaysCount = count($workDays);

        $lineCodesWithoutApplyingPriceRule = SettingRepositories::getLineCodesWithoutApplyingPriceRule();
        if (in_array($busPath->code, $lineCodesWithoutApplyingPriceRule)) {
            $totalPrice = $singleTicketPrice * $workDaysCount;
            $maxRate = 1.0;
        } else {
            $boughtTicketCount = BusTicketRepositories::boughtTicketCountInMonth($user, $monthDT);
            $monthlyPriceRule = SettingRepositories::getMonthlyPriceRule();

            $discountRate = BusTicketPriceCalculator::getDiscountRate(
                $boughtTicketCount,
                $workDaysCount,
                $monthlyPriceRule['next']
            );

            foreach ($discountRate as $rateItem) {
                $count = $rateItem['count'];
                $rate = $rateItem['rate'];
                $maxRate = max($maxRate, $rate);
                $totalPrice += $singleTicketPrice * $rate * $count;
            }
        }

        return [
            'total_price' => $totalPrice,
            'max_ticket_price' => $singleTicketPrice * $maxRate,
        ];
    }

    /**
     * 计算多天票的票价信息
     *
     * @param BusPath $busPath
     * @param array $schedules
     * @param User $user
     * @return array
     */
    public static function calculateTicketPriceForSchedules(BusPath $busPath, array $schedules, User $user)
    {
        $singleTicketPrice = $busPath->distance;
        $priceList = [];
        $totalPrice = 0.0;
        $curMonthDt = Carbon::now();
        $nextMonthDt = $curMonthDt->copy()->addMonthNoOverflow();
        $curMonthSchedules = [];
        $nextMonthSchedules = [];

        foreach ($schedules as $schedule) {
            $deptAtDt = Carbon::createFromTimestamp($schedule->dept_at);
            if ($deptAtDt->year == $curMonthDt->year && $deptAtDt->month == $curMonthDt->month) {
                $curMonthSchedules[] = $schedule;
            } else {
                $nextMonthSchedules[] = $schedule;
            }
        }

        $curMonthScheduleCount = count($curMonthSchedules);
        $nextMonthScheduleCount = count($nextMonthSchedules);

        $lineCodesWithoutApplyingPriceRule = SettingRepositories::getLineCodesWithoutApplyingPriceRule();
        if (in_array($busPath->code, $lineCodesWithoutApplyingPriceRule)) {
            if ($curMonthScheduleCount > 0) {
                $key = strval($curMonthDt->month);
                foreach ($curMonthSchedules as $schedule) {
                    if (!array_key_exists($key, $priceList)) {
                        $priceList[$key] = [];
                    }
                    $priceList[$key][] = $singleTicketPrice;
                }
            }

            if ($nextMonthScheduleCount > 0) {
                $key = strval($nextMonthDt->month);
                foreach ($nextMonthSchedules as $schedule) {
                    if (!array_key_exists($key, $priceList)) {
                        $priceList[$key] = [];
                    }
                    $priceList[$key][] = $singleTicketPrice;
                }
            }

            return [
                'total_price' => ($curMonthScheduleCount + $nextMonthScheduleCount) * $singleTicketPrice,
                'max_ticket_price' => $singleTicketPrice,
                'ticket_price_list' => $priceList
            ];
        }

        $maxRate = 0;
        $monthlyPriceRule = SettingRepositories::getMonthlyPriceRule();
        if ($curMonthScheduleCount > 0) {
            $boughtTicketCountInCurMonth = BusTicketRepositories::boughtTicketCountInMonth($user, $curMonthDt);
            $discountRate = BusTicketPriceCalculator::getDiscountRate(
                $boughtTicketCountInCurMonth,
                $curMonthScheduleCount,
                $monthlyPriceRule['current']
            );

            $key = strval($curMonthDt->month);
            foreach ($discountRate as $rateItem) {
                $count = $rateItem['count'];
                $rate = $rateItem['rate'];
                $maxRate = max($maxRate, $rate);
                for($i = 0; $i < $count; $i++) {
                    $priceList[$key][] = $singleTicketPrice * $rate;
                }
                $totalPrice += $singleTicketPrice * $rate * $count;
            }
        }

        if ($nextMonthScheduleCount > 0) {
            $boughtTicketCountInNextMonth = BusTicketRepositories::boughtTicketCountInMonth($user, $nextMonthDt);
            $discountRate = BusTicketPriceCalculator::getDiscountRate(
                $boughtTicketCountInNextMonth,
                $nextMonthScheduleCount,
                $monthlyPriceRule['next']
            );

            $key = strval($nextMonthDt->month);
            foreach ($discountRate as $rateItem) {
                $count = $rateItem['count'];
                $rate = $rateItem['rate'];
                for($i = 0; $i < $count; $i++) {
                    $priceList[$key][] = $singleTicketPrice * $rate;
                }
                $totalPrice += $singleTicketPrice * $rate * $count;
            }
        }

        return [
            'total_price' => $totalPrice,
            'max_ticket_price' => $singleTicketPrice * $maxRate,
            'ticket_price_list' => $priceList
        ];
    }

    /**
     * 计算订单的最大单张票价
     *
     * @param Order $order
     * @param User $user
     * @return mixed
     */
    public static function calculateMaxTicketPriceForContract(Order $order, User $user)
    {
        $orderContent = $order->content;
        $busPath = $orderContent->busPath;
        $maxTicketPrice = $busPath->distance;

        if ($orderContent->ticket_type == BusTicketTypeEnum::Days) {
            $schedules = [];
            foreach ($orderContent->orderContentSeats as $orderContentSeat) {
                $schedules[] = $orderContentSeat->busPathSchedule;
            }
            $result = self::calculateTicketPriceForSchedules($busPath, $schedules, $user);
            $maxTicketPrice = $result['max_ticket_price'];
        } elseif ($orderContent->ticket_type == BusTicketTypeEnum::Month) {
            $monthDt = Carbon::createFromDate($orderContent->year, $orderContent->month);
            $result = self::calculateTicketPriceForMonth($busPath, $monthDt, $user);
            $maxTicketPrice = $result['max_ticket_price'];
        }

        return $maxTicketPrice;
    }

    /**
     * 计算折扣率
     *
     * @param $base
     * @param $delta
     * @param $rules
     * @return array
     */
    private static function getDiscountRate($base, $delta, $rules)
    {
        $defaultRate = 1.0;
        $rateList = [];
        if ($base > array_last($rules)['end']) {
            $rateList[] = [
                'count' => $delta,
                'rate' => $defaultRate
            ];
        } else {
            $baseIdx = -1;
            if ($base == 0) {
                $baseIdx = 0;
            } else {
                foreach ($rules as $idx => $item) {
                    if ($item['begin'] <= $base + 1 && $base + 1 <= $item['end']) {
                        $baseIdx = $idx;
                        break;
                    }
                }
            }

            $baseItem = $rules[$baseIdx];
            if ($base + $delta <= $baseItem['end']) {
                $rateList[] = [
                    'count' => $delta,
                    'rate' => $baseItem['value']
                ];
            } else {
                $rateList[] = [
                    'count' => $baseItem['end'] - $base,
                    'rate' => $baseItem['value']
                ];
                $deltaRemain = $base + $delta - $baseItem['end'];

                foreach ($rules as $idx => $item) {
                    if ($idx <= $baseIdx) continue;

                    $itemRangeLength = $item['end'] - $item['begin'] + 1;
                    if ($deltaRemain > $itemRangeLength) {
                        $rateList[] = [
                            'count' => $itemRangeLength,
                            'rate' => $item['value']
                        ];
                        $deltaRemain -= $itemRangeLength;
                    } else {
                        $rateList[] = [
                            'count' => $deltaRemain,
                            'rate' => $item['value']
                        ];
                        $deltaRemain = 0;
                        break;
                    }
                }

                if ($deltaRemain > 0) {
                    $rateList[] = [
                        'count' => $defaultRate,
                        'rate' => $defaultRate
                    ];
                }
            }
        }

        return $rateList;
    }

}