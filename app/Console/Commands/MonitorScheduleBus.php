<?php

namespace App\Console\Commands;

use App\Models\Enum\ServiceName;
use App\Repositories\SettingRepositories;
use App\Tools\Message\MessageCenter;
use Illuminate\Console\Command;

class MonitorScheduleBus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:schedule_bus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $fileNames = ['./auto_schedule_bus_path_9.log', './auto_schedule_bus_path_21.log'];

        $mobiles = SettingRepositories::getServerNotifyMobiles();
        foreach ($fileNames as $fileName) {
            if (file_exists($fileName)) {
                $content = file_get_contents($fileName);
                if (strpos($content, 'Error') !== false ||
                    strpos($content, 'error') !== false ||
                    strpos($content, 'Exception') !== false) {
                    $mess = '班车调度失败';
                    app(ServiceName::HttpClient)->SendWarning(40, $mess);
                    MessageCenter::sendSMSByCenter($mobiles, sprintf('(%s)| %s', env('APP_ENV'), $mess));
                }
            } else {
                $mess = '班车调度出现问题，没有找到log文件';
                app(ServiceName::HttpClient)->SendWarning(40, $mess);
                MessageCenter::sendSMSByCenter($mobiles, sprintf('(%s)| %s', env('APP_ENV'), $mess));
            }
        }
    }
}
