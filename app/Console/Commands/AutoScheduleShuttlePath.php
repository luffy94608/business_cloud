<?php

namespace App\Console\Commands;

use App\Exceptions\MessageException;
use App\Helper\ScheduleUtils;
use App\Repositories\ShuttlePathRepositories;
use App\Repositories\SettingRepositories;
use App\Repositories\WorkDayRepositories;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoScheduleShuttlePath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:shuttle_path_daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '快捷巴士按日自动调度';

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
        $today = Carbon::today();

        $workDayOfCurrMonth = null;

        $dayType = 'hollo_days';

        $monthDt = $today->copy();
        $workDayOfCurrMonth = WorkDayRepositories::getWorkDay($monthDt->year, $monthDt->month);
        if (is_null($workDayOfCurrMonth)) {
            throw new MessageException(sprintf('快捷巴士按日调度时出错，没找到%s的work_days', $monthDt->format('Y年m月')), -1);
        }
        $workDaysInDay = $workDayOfCurrMonth->{$dayType} ?: [];

        if (empty($workDaysInDay)) {
            throw new MessageException(sprintf('快捷巴士按日调度时出错，%s的work_days数据有误', $monthDt->format('Y年m月')), -1);
        }

        if (!in_array($today->day, $workDaysInDay)) return;

        $shuttlePaths = ShuttlePathRepositories::getShuttlePaths();

        foreach ($shuttlePaths as $shuttlePath) {
            ScheduleUtils::createShuttlePathSchedule($shuttlePath, $today);
        }
    }


}
