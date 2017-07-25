<?php

namespace App\Console\Commands;

use App\Helper\Util;
use App\Models\Enum\DriverNotifyEnum;
use App\Models\Enum\EmergencyAlarmEnum;
use App\Models\Enum\NotificationTypeEnum;
use App\Models\Enum\WarningTypeEnum;
use App\Models\MessageTemplate;
use App\Models\Shuttle\ShuttleSchedule;
use App\Repositories\BusPathScheduleRepositories;
use App\Repositories\BusRoomRepositories;
use App\Repositories\DriverPositionRepositories;
use App\Repositories\DriverRepositories;
use App\Repositories\SettingRepositories;
use App\Repositories\ShuttleScheduleRepositories;
use App\Tools\HttpClient\HttpClientService;
use App\Tools\Message\MessageCenter;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScanDriverTaskAlarm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'driver:alarm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '未开司机端报警';

    /**
     * Create a new command instance.
     * ScanDriverTaskAlarm constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        print_r(sprintf('当前执行时间：  %s    %s  ', Carbon::now()->toDateTimeString(), PHP_EOL));
        if (!SettingRepositories::driverClientNotifyStatus()) {
            return false;
        }
        $httpClientService = new HttpClientService();
        $now = Carbon::now();
        if (!SettingRepositories::driverClientNotifyStatus()) {
            print_r(sprintf('所有报警已关闭 %s', PHP_EOL));
            return false;
        }

        if (!SettingRepositories::driverClientOpenAlarmStatus()) {
            print_r(sprintf('未开启司机端报警已经关闭 %s', PHP_EOL));
            return false;
        }

        $minutes = SettingRepositories::driverClientOpenAlarmInMinutes();
        print_r(sprintf('未开司机端报警 时间：%s', $minutes) . PHP_EOL);
        $this->scanBusTask($minutes, $now, $httpClientService);
        $this->scanShuttleTask($minutes, $now, $httpClientService);
    }

    /**
     * 班车未开司机端报警
     * @param $minutes // 提前多长时间未开报警
     * @param Carbon $now
     * @param HttpClientService $httpClientService
     */
    private function scanBusTask($minutes, Carbon $now, HttpClientService $httpClientService)
    {
        $startTime = $now;
        $endTime = $now->copy()->endOfDay();

        $busRooms = BusPathScheduleRepositories::getUnStartBusRoomInPeriod($startTime, $endTime);
        $count = count($busRooms);
        print_r(sprintf('【班车】当前未开始任务共计：  %s  个%s', $count, PHP_EOL));
        if ($count) {
            $total = 0;
            foreach ($busRooms as $busRoom) {
                $deptTime = Carbon::createFromTimestamp($busRoom->dept_at);
                $diffSeconds = $now->diffInSeconds($deptTime, false)/60;
                $diffMinutes = round($diffSeconds, 0);
                if ($diffMinutes>=0 && $diffMinutes<=$minutes && $diffMinutes%5==0) {//报警时间范围
                    $driverId = $busRoom->driver_id;
                    $status = DriverPositionRepositories::driverPositionAfterTimeStatus($busRoom->driver_id, $deptTime);
                    if (!$status) { //无最新坐标点
                        $driver = DriverRepositories::getDriverById($driverId);
                        $line = BusRoomRepositories::getLineByBusRoom($busRoom);
                        $messageTemplate = new MessageTemplate($busRoom, $line, $driver, '', '', '', '', $minutes);
                        $msg = $messageTemplate->getMessageText(EmergencyAlarmEnum::DRIVER_CLIENT_NOT_OPEN);
                        $plate = '';
                        if (!is_null($busRoom->bus)) {
                            $plate = $busRoom->bus->plate;
                        }
                        $info = [
                            'bus_room_id'   =>$busRoom->id,
                            'detail'        =>$msg,
                            'driver_id'     =>$driverId,
                            
                            'line_code'     =>$line->code,
                            'line_name'     =>$line->name,
                            'shift'         =>Carbon::createFromTimestamp($busRoom->dept_at)->format('H:i'),
                            'driver_name'   =>$driver->nickname,
                            'driver_mobile' =>$driver->name,
                            'plate'         =>$plate,
                        ];
                        print_r(sprintf('【班车】未开司机端报警：  %s %s', $msg, PHP_EOL));
                        $httpClientService->SendWarning(WarningTypeEnum::DriverClient, $msg, $info);
                        $ext = [
                            'type'  =>DriverNotifyEnum::Alarm,
                            'title' =>DriverNotifyEnum::transform(DriverNotifyEnum::Alarm),
                        ];
                        Util::sendNotification($driver, NotificationTypeEnum::DriverSys, $msg, null, null, null, $ext);
                        ++$total;
                    }
                }

            }
            print_r(sprintf('【班车】当前未开启司机端报警数：  %s  个 %s', $total, PHP_EOL));
        }
    }

    /**
     * 快捷巴士未开司机端报警
     * @param $minutes
     * @param Carbon $now
     * @param HttpClientService $httpClientService
     */
    private function scanShuttleTask($minutes, Carbon $now, HttpClientService $httpClientService)
    {
        $startTime = $now;
        $endTime = $now->copy()->endOfDay();

        $schedules = ShuttleScheduleRepositories::getUnStartScheduleInPeriod($startTime, $endTime);
        $count = count($schedules);
        print_r(sprintf('【快捷巴士】当前未开始任务共计：  %s  个%s', $count, PHP_EOL));
        if ($count) {
            $total = 0;
            foreach ($schedules as $schedule) {
                $deptTime = Carbon::createFromTimestamp($schedule->dept_at);
                $diffSeconds = $now->diffInSeconds($deptTime, false)/60;
                $diffMinutes = round($diffSeconds, 0);
                if ($diffMinutes>=0 && $diffMinutes<=$minutes && $diffMinutes%5==0) {//报警时间范围
                    $driverId = ShuttleScheduleRepositories::getScheduleDriverId($schedule);
                    $status = DriverPositionRepositories::driverPositionAfterTimeStatus($schedule->driver_id, $deptTime);
                    if (!$status) { //无最新坐标点
                        $driver = DriverRepositories::getDriverById($driverId);
                        $line = $schedule->line;
                        $messageTemplate = new MessageTemplate($schedule, $line, $driver, '', '', '', '', $minutes);
                        $msg = $messageTemplate->getMessageText(EmergencyAlarmEnum::DRIVER_CLIENT_NOT_OPEN);
                        $plate = '';
                        if (!is_null($schedule->bus)) {
                            $plate = $schedule->bus->plate;
                        }
                        $info = [
                            'shuttle_schedule_id'   =>$schedule->id,
                            'detail'                =>$msg,
                            'driver_id'             =>$driverId,

                            'line_code'     =>$line->code,
                            'line_name'     =>$line->name,
                            'shift'         =>Carbon::createFromTimestamp($schedule->dept_at)->format('H:i'),
                            'driver_name'   =>$driver->nickname,
                            'driver_mobile' =>$driver->name,
                            'plate'         =>$plate,
                        ];
                        print_r(sprintf('【班车】未开司机端报警：  %s %s', $msg, PHP_EOL));
                        $httpClientService->SendWarning(WarningTypeEnum::DriverClient, $msg, $info);
                        ++$total;
                    }
                }
            }
            print_r(sprintf('【快捷巴士】当前未开启司机端报警数：  %s  个 %s', $total, PHP_EOL));

        }

    }
}
