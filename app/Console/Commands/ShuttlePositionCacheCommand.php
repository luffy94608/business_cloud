<?php

namespace App\Console\Commands;

use App\Helper\DataUtils;
use App\Helper\RedisHelper;
use App\Models\BusGPS;
use App\Models\Enum\ServiceName;
use App\Models\GPS\Position;
use App\Models\Shuttle\ShuttlePath;
use Carbon\Carbon;
use Illuminate\Console\Command;
use SebastianBergmann\CodeCoverage\Report\PHP;

class ShuttlePositionCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:shuttle_position';

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
        try {
            $d = 19;
            while ($d > 0) {
                $d -= 1;
                $this->doWork();
                sleep(5);
            }
        } catch (\Exception $e) {
            app(ServiceName::HttpClient)->SendWarning(env('APP_ENV') == 'product' ? 41 : 45, '获取快捷巴士位置错误', ['detail' => sprintf('\n%s', $e->getTraceAsString())]);
        }

    }
    public function doWork() {
        # 获取所有运营中的线路   前后30分钟
        $shuttlePaths = ShuttlePath::where('enabled', true)->get();

        $deltaInSecond = 60 * 15;
        $now = Carbon::now()->timestamp;

        $runPaths = [];
        foreach ($shuttlePaths as $shuttlePath) {
            if (env('APP_ENV') == 'production') {
                $inBusinessPeriod = false;
            } else {
                $inBusinessPeriod = true;
            }
            foreach ($shuttlePath->schedules as $schedule) {
                $deptAt = DataUtils::getTimestampFromTimeStr($schedule['dept_at_str'], Carbon::today());
                $destAt = DataUtils::getTimestampFromTimeStr($schedule['dest_at_str'], Carbon::today());
                if ($deptAt - $deltaInSecond <= $now && $now <= $destAt + $deltaInSecond) {
                    $inBusinessPeriod = true;
                    break;
                }
            }
            if ($inBusinessPeriod) {
                $runPaths[] = $shuttlePath;
            }
        }

        foreach ($runPaths as $path) {
            $busInfoIds = $path->shuttles()->pluck('bus_info_id')->all();
            $deviceIds = BusGPS::whereIn('car_id', $busInfoIds)->pluck('serial_num')->all();
            if (env('APP_ENV') == 'production') {
                $positions = Position::whereIn('device_id', $deviceIds)->where('at', '>', Carbon::now()->addMinutes(-5))->get();
            } else {
                $positions = Position::whereIn('device_id', $deviceIds)->get();
            }

            $data = [];
            foreach ($positions as $position) {
                $data[] = [
                    'bus_id' => DataUtils::generateId(),
                    'line_id' => $path->id,
                    'loc' => [
                        'name' => $position->addr,
                        'lng' => $position->baidu_coord['lng'],
                        'lat' => $position->baidu_coord['lat']
                    ],
                    'angle' => floatval($position->direction)
                ];
            }
            print_r(sprintf('%s %s', $path->id, json_encode($data)) . PHP_EOL);
            RedisHelper::sendShuttlePosition($path->id, $data);
        }
    }
}
