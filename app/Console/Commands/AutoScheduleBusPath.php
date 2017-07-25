<?php

namespace App\Console\Commands;

use App\Exceptions\MessageException;
use App\Helper\RuleEngine;
use App\Helper\ScheduleUtils;
use App\Repositories\BusPathRepositories;
use App\Repositories\SettingRepositories;
use App\Repositories\WorkDayRepositories;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoScheduleBusPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:bus_path_daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '班车按日自动调度';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     * @throws MessageException
     */
    public function handle()
    {
        $now = Carbon::now();
        if ($now->hour < 12) {
            $busPaths = BusPathRepositories::getBusPathsToBeScheduledInTheMorning();
        } else {
            $busPaths = BusPathRepositories::getBusPathsToBeScheduledInTheEvening();
        }

        $maxScheduleCount = SettingRepositories::maxScheduleCountForBusPath();
        $workDaysInDt = [];
        $hxWorkDayInDt = [];

        $workDayOfCurrMonth = null;
        $workDayOfNextMonth = null;

        $scheduleDay = $now->copy();

        $dayType = 'hollo_days';
        $hxUnScheduleDays = 'hx_un_days';
        // 需要调度的maxScheduleCount天的日期
        while (count($workDaysInDt) < $maxScheduleCount) {
            $monthDt = null;
            $workDayOfMonth = null;
            if (is_null($workDayOfCurrMonth)) {
                $monthDt = $now->copy();
                $workDayOfCurrMonth = WorkDayRepositories::getWorkDay($now->year, $now->month);
                if (is_null($workDayOfCurrMonth)) {
                    throw new MessageException(sprintf('班车按日调度时出错，没找到%s的work_days', $monthDt->format('Y年m月')), -1);
                }
                $workDayOfMonth = $workDayOfCurrMonth;
                $workDaysInDay = $workDayOfCurrMonth->$dayType ?: [];
            } elseif (is_null($workDayOfNextMonth)) {
                $nextMonth = $now->copy()->addMonthNoOverflow();
                $monthDt = $nextMonth->copy();
                $workDayOfNextMonth = WorkDayRepositories::getWorkDay($nextMonth->year, $nextMonth->month);
                if (is_null($workDayOfNextMonth)) {
                    throw new MessageException(sprintf('班车按日调度时出错，没找到%s的work_days', $monthDt->format('Y年m月')), -1);
                }
                $workDayOfMonth = $workDayOfNextMonth;
                $workDaysInDay = $workDayOfNextMonth->$dayType ?: [];
            } else {
                print_r('Unexpected here');
                break;
            }

            while ($monthDt->month == $scheduleDay->month) {
                if (in_array($scheduleDay->day, $workDaysInDay)) {
                    $workDaysInDt[] = Carbon::createFromDate($monthDt->year, $monthDt->month, $scheduleDay->day);
                }

                if (isset($workDayOfMonth->{$hxUnScheduleDays}) && in_array($scheduleDay->day, $workDayOfMonth->{$hxUnScheduleDays})) continue;

                if (in_array($scheduleDay->dayOfWeek, [6, 0]) || in_array($scheduleDay->day, $workDaysInDay)) {
                    $hxWorkDayInDt[] = Carbon::createFromDate($monthDt->year, $monthDt->month, $scheduleDay->day);
                }

                $scheduleDay->addDay();

                if (count($workDaysInDt) == $maxScheduleCount) break;
            }
        }

        foreach ($busPaths as $busPath) {
            if (in_array($busPath->code, SettingRepositories::getHXLineCodes())) {
                foreach ($hxWorkDayInDt as $dayDt) {
                    ScheduleUtils::createBusPathSchedule($busPath, $dayDt);
                }
            } else {
                foreach ($workDaysInDt as $dayDt) {
                    ScheduleUtils::createBusPathSchedule($busPath, $dayDt);
                }
            }
        }
    }


}
