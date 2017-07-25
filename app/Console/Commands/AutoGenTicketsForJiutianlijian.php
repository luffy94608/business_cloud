<?php

namespace App\Console\Commands;

use App\Helper\DataUtils;
use App\Models\Bus\BusPath;
use App\Models\Enum\BusRoomSeatStateEnum;
use App\Models\Enum\BusTicketTypeEnum;
use App\Models\Enum\OrderContentCLSEnum;
use App\Models\Enum\OrderStatusEnum;
use App\Models\Enum\OrderTypeEnum;
use App\Models\Enum\ServiceName;
use App\Models\Order\Order;
use App\Models\Order\OrderContent;
use App\Models\User;
use App\Repositories\BusContentSeatRepositories;
use App\Repositories\BusContractRepositories;
use App\Repositories\BusPathRepositories;
use App\Repositories\BusRoomSeatRepositories;
use App\Repositories\HolloOrderRepositories;
use App\Repositories\SettingRepositories;
use App\Tools\Message\MessageCenter;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoGenTicketsForJiutianlijian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:jiutianlijian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '为九天利建自动生成票';

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
        $lineCode = SettingRepositories::getJiutianlijianLineCode();
        $line = BusPathRepositories::getBusPathByCode($lineCode);
        $scheduleDay = Carbon::tomorrow();
        while ($scheduleDay->isWeekend()) {
            $scheduleDay->addDay();
        }

        $validMobiles = SettingRepositories::getJiutianlijianMobiles();

        $this->generateTicketsForMobiles($line, $scheduleDay, $validMobiles);

    }

    public function generateTicketsForMobiles(BusPath $busPath, Carbon $scheduleDay, array $mobiles)
    {
        $dayStart = $scheduleDay->copy()->startOfDay();
        $dayEnd = $scheduleDay->copy()->endOfDay();

        $schedule = $busPath->schedules()
            ->whereBetween('dept_at', [$dayStart->timestamp, $dayEnd->timestamp])
            ->first();
        if (empty($schedule)) return;

        $room = $schedule->busRooms()->first();

        $sentTicketMobiles = [];
        $users = User::whereIn('name', $mobiles)->get();

        $seatLockValidInSeconds = SettingRepositories::getSeatLockValidInSeconds();

        $notifyMobiles = SettingRepositories::getServerNotifyMobiles();

        try {
            foreach ($users as $user) {
                $seat = $room->seats()
                    ->where('user_id', $user->id)
                    ->where('state', BusRoomSeatStateEnum::Confirmed)
                    ->first();
                if (!empty($seat)) continue;


                $seat = $room->seats()
                    ->where('state', BusRoomSeatStateEnum::Unlocked)
                    ->orderBy('seat_number', 'desc')
                    ->first();

                if (empty($seat)) {
                    print_r(sprintf('余票不够，用户(%s)发票失败', $user->name) . PHP_EOL);
                    break;
                }

                $now = Carbon::now();
                $order = new Order();
                $order->id = DataUtils::generateId();
                $order->order_no = DataUtils::generateOrderNo();
                $order->version_id = uniqid();
                $order->user()->associate($user);
                $order->type = OrderTypeEnum::BusContract;
                $order->amount = 0;
                $order->grants = 0;
                $order->is_customer_commented = 0;
                $order->created_at = $now;
                $order->reserve_end_time = Carbon::createFromTimestamp($schedule->reserve_end_time);
                $order->save();

                $departure = $busPath->dept[0];
                $destination = $busPath->dest[0];

                $oc = new OrderContent();
                $oc->_cls = OrderContentCLSEnum::Bus;
                $oc->order()->associate($order);
                $oc->busPath()->associate($busPath);    //TODO: model保存时是用String id，不是ObjectId
                $oc->user()->associate($user);
                $oc->ticket_type = BusTicketTypeEnum::Days;
                $oc->price = 0;
                $oc->seat_number = $seat->seat_number;
                $oc->departure = $departure;
                $oc->destination = $destination;
                $oc->dept_time = $departure['arrived_at_str'];
                $oc->status = OrderStatusEnum::transform(OrderStatusEnum::UNPAID);
                $oc->reserve_end_time = $schedule->reserve_end_time;
                $oc->save();

                $order->content()->associate($oc);
                $order->save();

                BusRoomSeatRepositories::reserveUserSeatInRoom($user, $oc, $room, $departure['_id'],
                    $destination['_id'], $seat->seat_number, $seatLockValidInSeconds);
                BusContentSeatRepositories::reserveUserSeatsInRoom($busPath, $user, $oc, $room, $seat->seat_number);

                BusContractRepositories::payWithCouponAndBalance($user, $order, null, $user->cashAccount, false, false);

                $sentTicketMobiles[] = $user->name;
            }

            if (count($sentTicketMobiles) != count($mobiles)) {
                $noTicketMobiles = array_diff($mobiles, $sentTicketMobiles);
                $mess = sprintf('自动发票无票人员：%s', implode(',', $noTicketMobiles));
                print_r($mess . PHP_EOL);
                app(ServiceName::HttpClient)->SendWarning(40, $mess);
                MessageCenter::sendSMSByCenter($mobiles, sprintf('(%s)| %s', env('APP_ENV'), $mess));
            } else {
                print_r('自动发票成功，共发出票数 ' . count($mobiles) . PHP_EOL);
            }
        } catch (\Exception $e) {
            $mess = sprintf('自动发票脚本错误：%s', $e->getMessage());
            print_r($mess . PHP_EOL);
            app(ServiceName::HttpClient)->SendWarning(40, $mess);
            MessageCenter::sendSMSByCenter($mobiles, sprintf('(%s)| %s', env('APP_ENV'), $mess));
        }
    }
}
