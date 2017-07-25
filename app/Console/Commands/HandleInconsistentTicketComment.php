<?php

namespace App\Console\Commands;

use App\Models\Bus\BusRoom;
use App\Models\Enum\HolloOrderStatusEnum;
use App\Models\Order\Order;
use App\Models\Order\OrderContentSeat;
use App\Repositories\BusContractRepositories;
use App\Repositories\BusTicketRepositories;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HandleInconsistentTicketComment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:revise_auto_comment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动评论票';

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
        $startTime = Carbon::createFromDate(2017, 3, 28)->startOfDay();
        $endTime = Carbon::today()->endOfDay();

        $tickets = OrderContentSeat::whereBetween('dept_at', [$startTime->timestamp, $endTime->timestamp])
            ->whereIn('status', [HolloOrderStatusEnum::Paid, HolloOrderStatusEnum::Checked])
            ->orderBy('dept_at')
            ->get();

        $orders = [];
        $ordersNeedToRevise = [];
        foreach ($tickets as $ticket) {
            $order = $ticket->orderContent->order;
            if (!array_key_exists($order->id, $orders) && $order->is_customer_commented == 1) {
                $orders[$order->id] = $order;
            }
        }

        print_r('共有订单' . count($orders) . PHP_EOL);

        foreach ($orders as $orderId => $order) {
            $validTickets = BusContractRepositories::getValidTickets($order);

            foreach ($validTickets as $ticket) {
                $comment = $ticket->comment;
                if (empty($comment)) {
                    if (!array_key_exists($order->id, $ordersNeedToRevise)) {
                        $ordersNeedToRevise[$order->id] = $order;
                    }
                }
                break;
            }
        }

        print_r('共需要恢复订单数为 ' . count($ordersNeedToRevise) . PHP_EOL);

        foreach ($ordersNeedToRevise as $orderId => $order) {
            $order->is_customer_commented = 0;
            $order->save();
        }
        print_r('恢复成功' . PHP_EOL);

    }
}
