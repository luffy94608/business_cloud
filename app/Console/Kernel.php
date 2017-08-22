<?php

namespace App\Console;

use App\Console\Commands\AutoCommentTicket;
use App\Console\Commands\AutoGenTicketsForJiutianlijian;
use App\Console\Commands\AutoScheduleBusPath;
use App\Console\Commands\AutoScheduleShuttlePath;
use App\Console\Commands\CarbonTimestampMigrate;
use App\Console\Commands\CheckTimeMigrate;
use App\Console\Commands\DecodeJWTToken;
use App\Console\Commands\HandleInconsistentOrder;
use App\Console\Commands\HandleInconsistentTicketComment;
use App\Console\Commands\ImportCoupons;
use App\Console\Commands\ImportOrders;
use App\Console\Commands\ImportRechargeOrder;
use App\Console\Commands\ImportUserInterest;
use App\Console\Commands\ImportWechatUserData;
use App\Console\Commands\LaraEloquentMigrate;
use App\Console\Commands\MoloquentMigrate;
use App\Console\Commands\MonitorScheduleBus;
use App\Console\Commands\MonitorScheduleShuttle;
use App\Console\Commands\MonthlyScheduleBusPath;
use App\Console\Commands\ObjectIdMigration;
use App\Console\Commands\ScanCompanyInfo;
use App\Console\Commands\ScanDriverFirstStationNotify;
use App\Console\Commands\ScanDriverTaskAlarm;
use App\Console\Commands\ScanOverdueTask;
use App\Console\Commands\SendCoupons;
use App\Console\Commands\SetOverduedOrderToDeleted;
use App\Console\Commands\ShuttlePositionCacheCommand;
use App\Console\Commands\TempCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        TempCommand::class,
        ImportWechatUserData::class,
        ImportUserInterest::class,
        ScanCompanyInfo::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        /**
//         * 班车早9点自动调度
//         */
//         $schedule->command('schedule:bus_path_daily')
//             ->dailyAt("9:00")
//             ->timezone('Asia/Shanghai')
//             ->appendOutputTo('./auto_schedule_bus_path_9.log');
//
//        /**
//         * 班车晚9点自动调度
//         */
//        $schedule->command('schedule:bus_path_daily')
//            ->dailyAt("21:00")
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./auto_schedule_bus_path_21.log');
//
//        /**
//         * 快捷巴士自动调度
//         */
//        $schedule->command('schedule:shuttle_path_daily')
//            ->dailyAt("00:00")
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./auto_schedule_shuttle_path.log');
//
//        /**
//         * 自动评价票
//         */
//        $schedule->command('ticket:auto_comment')
//            ->everyFiveMinutes()
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./auto_comment_ticket.log');
//
//        /**
//         * 自动置超时订单为已删除状态
//         */
//        $schedule->command('order:overdue')
//            ->everyMinute()
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./order_overdue.log');
//
//        /**
//         * 九天利建自动发票
//         */
//        $schedule->command('ticket:jiutianlijian')
//            ->dailyAt('00:10')
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./jiutianlijian_ticket.log');
//
//        /**
//         * 扫描超时司机端任务（包含结束任务后给用户发送评价通知）
//         */
//        $schedule->command('task:overdue')
//            ->everyMinute()
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./scan_overdue_task.log');
//        /**
//         * 未开司机端报警
//         */
//        $schedule->command('driver:alarm')
//            ->everyMinute()
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./scan_driver_task_alarm.log');
//        /**
//         * 司机端首站通知
//         */
//        $schedule->command('driver_notify:first_station')
//            ->everyMinute()
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./scan_driver_first_station_notify.log');
//
//        /**
//         * 班车调度错误监控
//         */
//        $schedule->command('monitor:schedule_bus')
//            ->dailyAt("9:05")
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./monitor_schedule_bus.log');
//
//        $schedule->command('monitor:schedule_bus')
//            ->dailyAt("21:05")
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./monitor_schedule_bus.log');
//
//        /**
//         * 快捷巴士错误监控
//         */
//        $schedule->command('monitor:schedule_shuttle')
//            ->dailyAt("00:05")
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./monitor_schedule_shuttle.log');
//
//        /**
//         * 快捷巴士位置
//         */
//        $schedule->command('cache:shuttle_position')
//            ->everyMinute()
//            ->timezone('Asia/Shanghai')
//            ->appendOutputTo('./shuttle_position.log');

        $schedule->command('import_user_interest')
            ->everyMinute()
            ->timezone('Asia/Shanghai')
            ->appendOutputTo('./import_user_interest.log');

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
