<?php

namespace App\Console\Commands;

use App\Helper\Util;
use App\Models\Bus\BusPath;
use App\Models\Bus\BusRoom;
use App\Models\Enum\NotificationTypeEnum;
use App\Repositories\BusPathScheduleRepositories;
use App\Repositories\BusRoomRepositories;
use App\Repositories\OrderContentSeatsRepositories;
use App\Repositories\SettingRepositories;
use App\Repositories\ShuttleScheduleRepositories;
use App\Repositories\UserRepositories;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScanOverdueTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '司机端 班车和快捷巴士任务过期脚本';

    /**
     * Create a new command instance.
     * ScanOverdueTask constructor.
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
         $this->scanBusTask();
         $this->scanShuttleTask();
    }

    private function scanBusTask()
    {
        $now = Carbon::now();
        $startTime = $now->copy()->startOfDay();
        $endTime = $now->copy()->endOfDay();
        $overdueMinutes = SettingRepositories::driverTaskOverdueInMinutes();

        $busRooms = BusPathScheduleRepositories::getUnFinishBusRoomInPeriod($startTime, $endTime);
        $count = count($busRooms);
        print_r(sprintf('【班车】当前未结束任务共计：  %s  个%s', $count, PHP_EOL));
        if ($count) {
            $deadTimestamp =  $now->copy()->subMinutes($overdueMinutes)->timestamp;
            $closeTotal = 0;
            foreach ($busRooms as $busRoom) {
                $line = BusRoomRepositories::getLineByBusRoom($busRoom);
                $destTimestamp = $now->copy()->modify($line->dest_at)->getTimestamp();
                if ($deadTimestamp >= $destTimestamp) {
                    BusRoomRepositories::finishTask($busRoom);
                    ++$closeTotal;
                    $scheduleTimeTitle = Carbon::createFromTimestamp($busRoom->dept_at)->format('Y-m-d H:i');
                    print_r(sprintf('【班车】  %s   %s   线路  %s  班次任务超时关闭%s', $line->code, $line->name, $scheduleTimeTitle, PHP_EOL));
                }

                $this->notifyUserToCommentTicket($busRoom, $line);
            }
            print_r(sprintf('【班车】当前结束任务共计：  %s  个 %s', $closeTotal, PHP_EOL));
        }
    }

    private function scanShuttleTask()
    {
        $now = Carbon::now();
        $startTime = $now->copy()->startOfDay();
        $endTime = $now->copy()->endOfDay();
        $overdueMinutes = SettingRepositories::driverTaskOverdueInMinutes();

        $schedules = ShuttleScheduleRepositories::getUnFinishScheduleInPeriod($startTime, $endTime);
        $count = count($schedules);
        print_r(sprintf('【快捷巴士】当前未结束任务共计：  %s  个%s', $count, PHP_EOL));
        if ($count) {
            $deadTimestamp =  $now->copy()->subMinutes($overdueMinutes)->timestamp;
            $closeTotal = 0;
            foreach ($schedules as $schedule) {
                $destTimestamp = ShuttleScheduleRepositories::getScheduleDestTimestamp($schedule);
                if ($deadTimestamp >= $destTimestamp) {
                    ShuttleScheduleRepositories::finishTask($schedule);
                    ++$closeTotal;
                    $line = $schedule ->line;
                    $scheduleTimeTitle = Carbon::createFromTimestamp($schedule->dept_at)->format('Y-m-d H:i');
                    print_r(sprintf('【快捷巴士】  %s   %s   线路  %s  班次任务超时关闭  %s', $line->code, $line->name, $scheduleTimeTitle, PHP_EOL));
                }
            }
            print_r(sprintf('【快捷巴士】当前超时任务共计：  %s  个  %s', $closeTotal, PHP_EOL));
        }
        
    }

    private function notifyUserToCommentTicket(BusRoom $busRoom, BusPath $line, $deltaInMinutes=10)
    {
        $now = Carbon::now();
        $reserveEndTimeTs = $busRoom->busPathSchedule->reserve_end_time;
        $diffSeconds = ($now->timestamp - $reserveEndTimeTs)/60;
        $diffMinutes = round($diffSeconds, 0);
        if ($diffMinutes == $deltaInMinutes) {
            $msg = sprintf('感谢您乘坐%s%s的班车，我们期待您的好评。', $line->code, $line->name);

            $tickets = OrderContentSeatsRepositories::getOrderContentSeatsByRoomId($busRoom->id);
            foreach ($tickets as $ticket) {
                $comment = $ticket->comment;
                if (!empty($comment)) continue;

                $remainTicketCount = $ticket->orderContent->seats()
                    ->where('dept_at', '>', $ticket->dept_at)
                    ->count();
                if ($remainTicketCount > 0) {
                    $actUrl = 'hollogo://bus_ticket_list';
                } else {
                    $actUrl = 'hollogo://ticket_history_list';
                }

                $user = $ticket->user;
                print_r(sprintf('班车结束运营后发送给 %s %s', $user->name, $msg) . PHP_EOL);
                Util::sendNotification($user, NotificationTypeEnum::UserSys, $msg, $actUrl);
            }
        }
    }
}
