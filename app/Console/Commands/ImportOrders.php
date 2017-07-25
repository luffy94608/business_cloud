<?php

namespace App\Console\Commands;

use App\Models\Enum\CashBillTypeEnum;
use App\Models\Enum\HolloOrderStatusEnum;
use App\Models\Enum\OrderContentCLSEnum;
use App\Models\Order\CashBill;
use App\Models\Order\Order;
use App\Models\Order\OrderContent;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportOrders extends Command
{
    protected $start;
    protected $end;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_pay_orders{step}{start}{end}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '1 import bus 2 only check bus 20 import shuttle 21 only check shuttle 30 import travel';

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
        $this->start = intval($this->argument('start'));
        $this->end = intval($this->argument('end'));
        #将车票的支付信息导到content里  use_coupon coupon_id coupon_price use_balance use_3rd_trade trade_channel trade_3rd_amount balance
        $orderTypes = ['bus_contract', 'shuttle_contract', 'tour_contract'];   #1378221 条  23号

        $step = intval($this->argument('step'));
        if ($step >= 1 && $step < 20) {
            # 1 import bus_contract
            if ($step == 1) {
                self::importContract('bus_contract');
                print_r('import bus contract done' . PHP_EOL);
//                $input = $this->output->ask('import bus_contract done. continue ? ', 'Y');
//                if (strtolower($input) != 'y') {
//                    return;
//                }
            }

            # check bus_content
            self::check('bus_contract');
            print_r('check bus contract done' . PHP_EOL);
//            $input = $this->output->ask('check bus_content done. continue ? ', 'Y');
//            if (strtolower($input) != 'y') {
//                return;
//            }
        }

        if ($step <= 20) {
            if ($step < 21) {
                self::importContract('shuttle_contract');
                print_r('import shuttle contract done' . PHP_EOL);
//                $input = $this->output->ask('import shuttle_contract done. continue ? ', 'Y');
//                if (strtolower($input) != 'y') {
//                    return;
//                }
            }
            # 2 import shuttle_contract

            # check shuttle_content
            self::check('shuttle_content');
            print_r('check shuttle contract done' . PHP_EOL);
//            $input = $this->output->ask('check shuttle_content done. continue ? ', 'Y');
//            if (strtolower($input) != 'y') {
//                return;
//            }
        }
        else
        {
            # 旅游不需要导 原有数据都已结算。
//            if ($step == 30)
//            {
//                #3 import tour_contract
//                $input = $this->output->ask('import tour_contract done. continue ? ', 'Y');
//                if (strtolower($input) != 'y')
//                {
//                    return;
//                }
//            }
//            # check tour_content
//            self::check('tour_content');
//            $input = $this->output->ask('check tour_contract done. continue ? ', 'Y');
//            if (strtolower($input) != 'y')
//            {
//                return;
//            }
        }

    }

    public function importContract($type)
    {
        $ids = [];
        $errorIds = [];
        $temp = DB::connection('mongodb')->collection('temp_orders')->where('type', $type)->first();
        if (isset($temp)) {
            $ids = $temp['import_ids'];
        }
        try {
//            $orders = Order::join('order_statuses', function($join) {
//                $join->on('orders.status_id', '=', 'order_statuses.id')
//                    ->whereIn('order_statuses.type', [HolloOrderStatusEnum::Refund, HolloOrderStatusEnum::Checked, HolloOrderStatusEnum::Finished, HolloOrderStatusEnum::Paid]);
//            })
//                ->select('orders.*')
//                ->where('orders.type', $type)->where('orders.created_at', '>', Carbon::createFromTimestamp($this->start))->where('orders.created_at', '<', Carbon::createFromTimestamp($this->end))->orderBy('orders.created_at')->get();

            $orders = Order::where('type', $type)->where('created_at', '>', Carbon::createFromTimestamp($this->start))->where('created_at', '<', Carbon::createFromTimestamp($this->end))->orderBy('created_at')->get();
            $bar = $this->output->createProgressBar($orders->count());
            foreach ($orders as $order) {
                if (in_array($order->id, $ids)) {
                    $bar->advance();
                    continue;
                }
                if (! in_array($order->currentStatus->type , [HolloOrderStatusEnum::Refund, HolloOrderStatusEnum::Checked, HolloOrderStatusEnum::Finished, HolloOrderStatusEnum::Paid]))
                {
                    $bar->advance();
                    continue;
                }
                $content = $order->content;
                if (is_null($content)) {
                    $errorIds[] = $order->id;
                    $bar->advance();
                    continue;
                }

                #coupon: use_coupon coupon_id coupon_price   现有数据已有

                #balance: use_balance balance
                if ($content->use_balance == 1 || $content->use_3rd_trade == 1) {
                    $cashBill = CashBill::where('order_id', $order->id)->where('type', CashBillTypeEnum::Expenses)->first();
                    if (is_null($cashBill))
                    {
                        $errorIds[] = $order->id;
                        $bar->advance();
                        continue;
                    }
                    if ($cashBill->amount == 0)
                    {
                        # 赠票问题
                        $content->balance = $order->amount;
                    }
                    else
                    {
                        $content->balance = $cashBill->amount;
                    }

                }

                #3rd: use_3rd_trade trade_channel trade_3rd_amount balance    balance = balance + 3rd + grants
                if ($content->use_3rd_trade) {
                    $links = DB::connection('mysql')->table('order_links')->where('order_id2', $order->id)->get();
                    foreach ($links as $link) {
                        $cashBill = CashBill::where('order_id', $link->order_id1)->first();
                        if (isset($cashBill)) {
                            if ($cashBill->type == CashBillTypeEnum::AlipayRecharge) {
                                $content->trade_channel = 0;
                            }
                            elseif ($content->os_type == 'wechat') {
                                $content->trade_channel = 2;   # 公众号支付
                            }
                            else {
                                $content->trade_channel = 1;
                            }
                            $content->trade_3rd_amount = doubleval($cashBill->amount);
                            break;
                        }
                    }
                }
                $content->import_done = 1;
                if (! $content->save()) {
//                    self::fail($type, $ids);
//                    return;
                }
                $ids[] = $order->id;
                $bar->advance();
            }
            $bar->finish();
        }
        catch (\Exception $e) {
            self::printError($errorIds, $type);
            self::fail($type, $ids);
            var_dump($e->getTraceAsString());
            throw $e;
        }
        self::printError($errorIds, $type);
    }

    public function printError($ids, $type)
    {
        if (count($ids) > 0) {
            DB::connection('mongodb')->collection('temp_orders')->where('type', 'error_' . $type)
                ->update([
                    '$set' => ['ids' => $ids]
                ], ['upsert' => true]);
        }
    }

    public function check($type)
    {
        $ids = [];
        $error =[];
        $temp = DB::connection('mongodb')->collection('temp_orders')->where('type', 'check_' . $type)->first();
        if (isset($temp)) {
            $ids = $temp['import_ids'];
        }
        try {
            if ($type == 'bus_contract') {
                $cls = OrderContentCLSEnum::Bus;
            }
            else {
                $cls = OrderContentCLSEnum::Shuttle;
            }
            $contents = OrderContent::where('_cls', $cls)->where('import_done', 1)->whereIn('status', ['finished', 'paid', 'refund', 'checked'])->where('created_at', '>', Carbon::createFromTimestamp($this->start))->where('created_at', '<', Carbon::createFromTimestamp($this->end))->get();
            $bar = $this->output->createProgressBar($contents->count());
            foreach ($contents as $content) {
                if (in_array($content->order_id, $ids)) {

                    $bar->advance();
                }
                $couponPrice = is_null($content->coupon_price) ? 0 : $content->coupon_price;
                $balance = is_null($content->balance) ?  0 : $content->balance;
                $order = Order::find($content->order_id);
                if (is_null($order)) {
                    $error[] = $content->order_id;
                    $bar->advance();
                    continue;
                }
                # order amount = content.balance + content.coupon_price
                if (abs($order->amount - $couponPrice - $balance) > 0.01 && $couponPrice < $order->amount) {
                    $error[] = $content->order_id;
                    $bar->advance();
                    continue;
                }
                if ($content->use_3rd_trade || isset($content->trade_channel)) {
                    $links = DB::connection('mysql')->table('order_links')->where('order_id2', $order->id)->get();
                    $match = true;
                    if ($links->count() > 0) {
                        foreach ($links as $link) {
                            $cashBill = CashBill::where('order_id', $link->order_id1)->first();
                            if (isset($cashBill)) {
                                if ($cashBill->type == CashBillTypeEnum::AlipayRecharge) {
                                    $match = $content->trade_channel == 0 && $content->trade_3rd_amount == doubleval($cashBill->amount);
                                }
                                elseif ($content->os_type == 'wechat') {
                                    $match = $content->trade_channel == 2 && $content->trade_3rd_amount == doubleval($cashBill->amount);
                                }
                                else {
                                    $match = $content->trade_channel == 1 && $content->trade_3rd_amount == doubleval($cashBill->amount);
                                }
                                if (! $match) {
                                    break;
                                }
                            }
                        }
                        if (! $match) {
                            $error[] = $order->id;
                            continue;
                        }
                    }
                }


                $ids[] = $content->order_id;
                $bar->advance();
            }

        }
        catch (\Exception $e) {
            self::printError($error, 'check_' . $type);
            self::fail($type, $ids);
            var_dump($e->getTraceAsString());
            throw $e;
        }
        $bar->finish();
        self::printError($error, 'check_' . $type);
    }

    public function fail($type, $ids)
    {
        DB::connection('mongodb')->collection('temp_orders')->where('type', $type)
            ->update([
                '$set' => ['import_ids' => $ids]
            ], ['upsert' => true]);
    }

    public function errorIds($errorIds, $type)
    {

    }
}
