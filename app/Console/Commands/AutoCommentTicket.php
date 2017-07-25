<?php

namespace App\Console\Commands;

use App\Models\Bus\BusRoom;
use App\Models\Enum\HolloOrderStatusEnum;
use App\Repositories\BusContractRepositories;
use App\Repositories\BusTicketRepositories;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCommentTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:auto_comment';

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
        $endTime = Carbon::now()->addDay(-1);
        $startTime = $endTime->copy()->addMinutes(-5);
        $rooms = BusRoom::whereBetween('dept_at', [$startTime->timestamp, $endTime->timestamp])
            ->orderBy('dept_at')
            ->get();

        foreach ($rooms as $room) {
            $validTickets = $room->orderContentSeats()
                ->whereIn('status', [HolloOrderStatusEnum::Paid, HolloOrderStatusEnum::Checked])
                ->get();
            foreach ($validTickets as $ticket) {
                $comment = $ticket->comment;
                if (!empty($comment)) continue;

                $order = $ticket->orderContent->order;
                $user = $order->user;
                BusTicketRepositories::commentTicket($order, $user, $ticket, 4, 4, 4, '', true);

                print_r(sprintf('评价用户%s的票%s', $user->id, $ticket->id) . PHP_EOL);
                $tickets = BusContractRepositories::getValidTickets($order);
                foreach ($tickets as $ticket) {
                    $comment = $ticket->comment;
                    if (!empty($comment)) {
                        $order->is_customer_commented = 1;
                        $order->save();
                        print_r('最后一张票，评价结束' . PHP_EOL);
                    }
                    break;
                }
            }
        }

    }
}
