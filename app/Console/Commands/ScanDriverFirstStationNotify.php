<?php

namespace App\Console\Commands;

use App\Helper\Util;
use App\Models\Enum\EmergencyAlarmEnum;
use App\Models\Enum\NotificationTypeEnum;
use App\Models\MessageTemplate;
use App\Repositories\BusPathRepositories;
use App\Repositories\BusPathScheduleRepositories;
use App\Repositories\BusRoomRepositories;
use App\Repositories\DriverPositionRepositories;
use App\Repositories\DriverRepositories;
use App\Repositories\OrderContentSeatsRepositories;
use App\Repositories\SettingRepositories;
use App\Repositories\UserRepositories;
use App\Tools\HttpClient\HttpClientService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScanDriverFirstStationNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'driver_notify:first_station';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '首站发车通知';

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
     *
     * @return mixed
     */
    public function handle()
    {
        print_r(sprintf('当前执行时间：  %s    %s  ', Carbon::now()->toDateTimeString(), PHP_EOL));
        if (!SettingRepositories::driverClientNotifyStatus()) {
            return false;
        }
        $now = Carbon::now();
        if (!SettingRepositories::driverClientNotifyStatus()) {
            print_r(sprintf('所有报警已关闭 %s', PHP_EOL));
            return false;
        }

        $minutes = 10;//首站通知时间
        $this->scanBusTask($minutes, $now);
        $this->scanShuttleTask($minutes, $now);
        
    }

    /**
     * 班车首站通知
     * @param $minutes // 提前多长时间 发通知
     * @param Carbon $now
     */
    private function scanBusTask($minutes, Carbon $now)
    {
        $startTime = $now;
        $endTime = $now->copy()->endOfDay();

        $busRooms = BusPathScheduleRepositories::getUnStartBusRoomInPeriod($startTime, $endTime);
        $count = count($busRooms);
        if ($count) {
            foreach ($busRooms as $busRoom) {
                $deptTime = Carbon::createFromTimestamp($busRoom->dept_at);
                $diffSeconds = $now->diffInSeconds($deptTime, false)/60;
                $diffMinutes = round($diffSeconds, 0);
                if ($diffMinutes==$minutes) {
                    $driverId = $busRoom->driver_id;
                    $driver = DriverRepositories::getDriverById($driverId);
                    $line = BusRoomRepositories::getLineByBusRoom($busRoom);
                    $stations = BusPathRepositories::getLineStations($line);
                    $firstStationName = $stations[0]['name'];
                    $messageTemplate = new MessageTemplate($busRoom, $line, $driver, '', $firstStationName, '', '', '');
                    $stationId = BusPathRepositories::getStationIdByIndex($line->id , 0);
                    $userIds = OrderContentSeatsRepositories::getUserIdsByRoomAndStationId($busRoom->id, $stationId);
                    if (count($userIds)) {
                        $msg = $messageTemplate->getMessageText(EmergencyAlarmEnum::Notify_Dispatch);
                        print_r(sprintf('【班车首站通知】  %s  %s', $msg, PHP_EOL));
                        $users = UserRepositories::getUsersByIds($userIds);
                        Util::sendNotification($users, NotificationTypeEnum::UserSys,$msg);
                    }
                }

            }
        }
    }

    /**
     * 快捷巴士首站通知
     * @param $minutes
     * @param Carbon $now
     */
    private function scanShuttleTask($minutes, Carbon $now)
    {
       

    }
}
