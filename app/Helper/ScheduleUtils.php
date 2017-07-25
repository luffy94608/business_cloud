<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 13/03/2017
 * Time: 10:15
 */

namespace App\Helper;


use App\Models\Bus\BusPath;
use App\Models\Bus\BusPathSchedule;
use App\Models\Bus\BusRoom;
use App\Models\Bus\BusRoomSeat;
use App\Models\Enum\BusRoomSeatStateEnum;
use App\Models\Shuttle\ShuttlePath;
use App\Models\Shuttle\ShuttleSchedule;
use App\Repositories\BusRepositories;
use Carbon\Carbon;

class ScheduleUtils
{
    /**
     * 创建班车调度
     *
     * @param BusPath $busPath
     * @param Carbon $day
     */
    public static function createBusPathSchedule(BusPath $busPath, Carbon $day)
    {
        $now = Carbon::now();
        $buses = BusRepositories::getBusesAssociateWithBusPath($busPath);
        if (count($buses) > 0) {
            $deptAt = $day->copy()->modify(RuleEngine::busPathTimeStrTransform($busPath, $busPath->dept_at, $day));
            $destAt = $day->copy()->modify(RuleEngine::busPathTimeStrTransform($busPath, $busPath->dest_at, $day));
            $schedule = $busPath->schedules()
                ->where('dept_at', $deptAt->timestamp)
                ->first();
            if (is_null($schedule)) {
                $schedule = new BusPathSchedule();
                $schedule->line()->associate($busPath);
                $schedule->path_name = $busPath->name;
                $schedule->dept_at = $deptAt->timestamp;
                $schedule->dept_at_str = RuleEngine::busPathTimeStrTransform($busPath, $busPath->dept_at, $day);
                $schedule->reserve_start_time = $day->copy()->addDays(-1)->startOfDay()->timestamp;
                $schedule->reserve_end_time = $destAt->timestamp;
                $schedule->created_at = $now->timestamp;
                $schedule->save();
            }

            foreach ($buses as $bus) {
                print_r(sprintf('release bus: %s for path: %s on %s',
                        $bus->name,
                        $busPath->name,
                        $day->toDateTimeString()
                    ) . PHP_EOL);
                $driver = $bus->driver;
                if (is_null($driver)) {
                    print_r('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' . PHP_EOL);
                    print_r(sprintf('Warning: no driver for bus %s', $bus->id) . PHP_EOL);
                    print_r('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>' . PHP_EOL);
                    continue;
                }
                $busRoom = $schedule->busRooms()
                    ->where('bus_id', $bus->id)
                    ->first();

                if (is_null($busRoom)) {
                    $busRoom = new BusRoom();
                    $busRoom->dept_at = $deptAt->timestamp;
                    $busRoom->busPathSchedule()->associate($schedule);
                    $busRoom->driver()->associate($driver);
                    $busRoom->save();
                }

                $lockSeat = $busPath->availableSeat;
                // range是左闭右闭
                foreach (range(0, intval($bus->total_seat)-1) as $seatNum) {
                    $busRoomSeat = $busRoom->seats()
                        ->where('seat_number', $seatNum)
                        ->first();
                    if ($lockSeat && !in_array($seatNum+1, $lockSeat->unlock_seats)) {
                        if (is_null($busRoomSeat)) {
                            $busRoomSeat = new BusRoomSeat();
                            $busRoomSeat->seat_number = $seatNum;
                            $busRoomSeat->user_id = '026c5544e28e11e49b7700163e02029f';
                            $busRoomSeat->state = BusRoomSeatStateEnum::Confirmed;
                            $busRoomSeat->locked_at = $now->timestamp;
                            $busRoomSeat->update_time = $now->timestamp;
                            $busRoomSeat->busRoom()->associate($busRoom);
                            $busRoomSeat->save();
                        }
                    } else {
                        if (is_null($busRoomSeat)) {
                            $busRoomSeat = new BusRoomSeat();
                            $busRoomSeat->seat_number = $seatNum;
                            $busRoomSeat->user_id = '';
                            $busRoomSeat->state = BusRoomSeatStateEnum::Unlocked;
                            $busRoomSeat->locked_at = 0;
                            $busRoomSeat->update_time = $now->timestamp;
                            $busRoomSeat->busRoom()->associate($busRoom);
                            $busRoomSeat->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * 创建快捷巴士调度
     *
     * @param ShuttlePath $shuttlePath
     * @param Carbon $day
     */
    public static function createShuttlePathSchedule(ShuttlePath $shuttlePath, Carbon $day)
    {
        $now = Carbon::now();
        $shuttles = $shuttlePath->shuttles;

        if (count($shuttles) > 0) {
            $periodInMins = $shuttlePath->period_in_minutes;
            $periods = [
                'dept_at_str' => $shuttlePath['dept_at_str'],
                'dest_at_str' => $shuttlePath['dest_at_str']
            ];

            if (isset($shuttlePath->schedules)) {
                $periods = $shuttlePath->schedules;
            }

            foreach ($periods as $period) {
                $deptAtTs = $day->copy()->startOfDay()->modify($period['dept_at_str'])->timestamp;

                $delta = 0;
                foreach ($shuttles as $shuttle) {
                    $driver = $shuttle->driver;
                    if (is_null($driver)) continue;

                    print_r(sprintf('release shuttle: %s for path: %s-%s with driver: %s-%s on time %s-%s',
                        $shuttle->name,
                        $shuttlePath->code,
                        $shuttlePath->name,
                        $driver->nickname,
                        $shuttle->plate,
                        $period['dept_at_str'],
                        $period['dest_at_str']
                        ) . PHP_EOL);

                    $schedule = $shuttlePath->shuttleSchedules()
                        ->where('dept_at', $deptAtTs + $delta)
                        ->first();
                    if (is_null($schedule)) {
                        $schedule = new ShuttleSchedule();
                        $schedule->line()->associate($shuttlePath);
                        $schedule->bus()->associate($shuttle);  // TODO: 秀飞 schedule上对应的不是shuttle2，现在是bus
                        $schedule->dept_at = $deptAtTs + $delta;
                        $schedule->expired = false;
                        $schedule->release_time = $now->timestamp;
                        $schedule->save();
                    }

                    $delta += $periodInMins * 60;
                }
            }
        }
    }

}
