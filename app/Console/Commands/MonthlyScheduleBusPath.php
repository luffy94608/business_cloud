<?php

namespace App\Console\Commands;

use App\Exceptions\MessageException;
use App\Helper\ScheduleUtils;
use App\Repositories\BusPathRepositories;
use App\Repositories\WorkDayRepositories;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthlyScheduleBusPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:bus_path_monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '班车月票调度';

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
        $workDaysInDt = [];

        $workDayOfNextMonth = null;

        $dayType = 'hollo_days';

        $monthDt = $now->copy()->addMonthNoOverflow(2);

        $result = $this->output->ask(sprintf('月票调度%s年%s月,是否继续', $monthDt->year, $monthDt->month), 'y');
        if (strtolower($result) != 'y') {
            return;
        }

        $workDayOfNextMonth = WorkDayRepositories::getWorkDay($monthDt->year, $monthDt->month);
        if (is_null($workDayOfNextMonth)) {
            throw new MessageException(sprintf('班车按月调度时出错，没找到%s的work_days', $monthDt->format('Y年m月')), -1);
        }
        $workDaysInDay = $workDayOfNextMonth->{$dayType} ?: [];
        if (empty($workDaysInDay)) {
            throw new MessageException(sprintf('班车按月调度时出错，%s的work_days数据有误', $monthDt->format('Y年m月')), -1);
        }

        foreach ($workDaysInDay as $day) {
            $workDaysInDt[] = Carbon::createFromDate($monthDt->year, $monthDt->month, $day);
        }

        $busPaths = BusPathRepositories::getMonthlySupportedBusPaths();

        foreach ($busPaths as $busPath) {
            foreach ($workDaysInDt as $dayDt) {
                ScheduleUtils::createBusPathSchedule($busPath, $dayDt);
            }
        }
    }
}
