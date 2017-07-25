<?php

namespace App\Console\Commands;

use App\Models\Enum\HolloOrderStatusEnum;
use App\Models\Enum\OrderContentCLSEnum;
use App\Models\Enum\OrderTypeEnum;
use App\Models\Order\Order;
use App\Models\Order\OrderContent;
use App\Repositories\BusRoomSeatRepositories;
use App\Repositories\HolloOrderRepositories;
use App\Repositories\TravelRepositories;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HandleInconsistentOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:content_deleted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将过期未支付的OrderContent状态设置为deleted';

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
        $now = Carbon::now();

        $contents = OrderContent::where('status', HolloOrderStatusEnum::Unpaid)
            ->where('_cls', OrderContentCLSEnum::Bus)
            ->where('created_at', '<', $now->copy()->addHour(-10))
            ->get();

        foreach ($contents as $content) {
            print_r(sprintf('handling %s ...', $content->id) . PHP_EOL);

            $order = $content->order;
            if (!empty($order)) {
                HolloOrderRepositories::toDeleted($order);
            } else {
                $content->status = HolloOrderStatusEnum::Deleted;
                $content->save();
            }
            BusRoomSeatRepositories::recycleUserSeats($content);
        }
    }
}
