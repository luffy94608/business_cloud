<?php

namespace App\Console\Commands;

use App\Models\Enum\OrderTypeEnum;
use App\Models\Order\Order;
use App\Repositories\BusRoomSeatRepositories;
use App\Repositories\HolloOrderRepositories;
use App\Repositories\TravelRepositories;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetOverduedOrderToDeleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将过期未支付的订单状态设置为deleted';

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
//        DB::connection('mysql')->enableQueryLog();

        $now = Carbon::now();

        $res = DB::connection('mysql')
            ->table('orders as o')
            ->select('o.id')
            ->join('order_statuses as s', 's.id', '=', 'o.status_id')
            ->where('o.created_at', '>', $now->copy()->addMinutes(-6))
            ->where('o.reserve_end_time', '<', $now)
            ->where('s.type', 'unpaid')
            ->whereIn('o.type', ['bus_contract', 'shuttle_contract', 'tour_contract'])
            ->orderBy('o.reserve_end_time', 'desc')
            ->get();

        $orderIds = [];
        foreach ($res as $item) {
            $orderIds[] = $item->id;
        }

        $orders = Order::whereIn('id', $orderIds)->get();
        foreach ($orders as $order) {
            print_r(sprintf('handling %s ...', $order->id) . PHP_EOL);
            HolloOrderRepositories::toDeleted($order);
            if ($order->type == OrderTypeEnum::BusContract) {
                $content = $order->content;
                BusRoomSeatRepositories::recycleUserSeats($content);
            } elseif ($order->type == OrderTypeEnum::TourContract) {
                $content = $order->content;
                $tourSeat = $content->tourSeat;
                if ($tourSeat) {
                    TravelRepositories::recycleSeat($tourSeat);
                }
            }
        }

//        $queries = DB::connection('mysql')->getQueryLog();
//        print_r($queries);
    }
}
