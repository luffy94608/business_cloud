<?php

namespace App\Console\Commands;

use App\Models\Enum\OrderTypeEnum;
use App\Models\Enum\ServiceName;
use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp;

class ImportRechargeOrder extends Command
{
    protected $appId = null;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_recharge_order{start}';

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
        $plat = env('APP_ENV', 'local');
        if ($plat != 'production') {
            $this->appId = '583533eacbd426495d448881';  //测试
        } else {
            $this->appId = '58d0fcd5cbd42668750521e0';  //正式
        }
        $input = $this->output->ask(sprintf('current APP_ENV is %s, continue ?', $plat), 'Y');
        if (strtolower($input) != 'y') {
            return;
        }

        $start = intval($this->argument('start'));
        $input = $this->output->ask(sprintf('import from %s . continue ? ', $start), 'Y');
        if (strtolower($input) != 'y') {
            return;
        }
        $rechargeOrders = Order::whereIn('type', [OrderTypeEnum::HolloAlipayRecharge, OrderTypeEnum::HolloWechatRecharge])
            ->where('created_at', '>', Carbon::createFromTimestamp($start))->get();
        $send = [];
        $max = 1000;
        $count = 0;
        $info = ['哈罗账户冲值', '哈罗快捷巴士车票', '哈罗班车车票'];
        $bar = $this->output->createProgressBar($rechargeOrders->count());
        foreach ($rechargeOrders as $order)
        {
            try {
                if ($order->currentStatus->type == 'paid')
                {
                    $mainId = $order->id;
                    $payId = $order->id;
                    $orderInfo = $info[0];
                    $payType = -1;
                    if ($order->type == 'alipay_recharge') {
                        $payType = 0;
                    }
                    $link = DB::connection('mysql')->table('order_links')->where('order_id1', $order->id)->first();
                    if ($link)
                    {
                        $mainId = $link->order_id2;
                        $mainOrder = Order::find($mainId);
                        if ($mainOrder->type == 'bus_contract')
                        {
                            $orderInfo = $info[2];
                        }
                        else{
                            $orderInfo = $info[1];
                        }
                        if ($payType == -1) {
                            $content = $mainOrder->content;
                            if ($content->os_type == 'wechat') {
                                $payType = 2;
                            } else {
                                $payType = 1;
                            }
                        }
                    }
                    $data = [
                        'business_id' => $mainId,
                        'payment_id' => $payId,
                        'payment_type' => $payType,
                        'total_fee' => doubleval($order->amount),
                        'order_info' => $orderInfo
                    ];
                    $send[] = $data;

                    if (count($send) >= $max)
                    {
                        self::doSend($send);
                        $send = [];
                        $count += $max;
                    }
                }
                $bar->advance();
            } catch (\Exception $e) {
                var_dump($e);
                var_dump($e->getTraceAsString());
                var_dump($order->id);
                return;
            }

        }
        if (count($send) > 0)
        {
            $count += count($send);
            self::doSend($send);
            $send = [];
        }
        $bar->finish();
        var_dump($count);
    }

    public function doSend($send)
    {
        $httpClient = new GuzzleHttp\Client(['timeout' => 100]);
        $url = 'http://pay-center.hollo.cn:82/config/import/import_payment_order';

        $r = json_decode($httpClient->post($url, ['json' => [
            'account_id' => $this->appId,
            'import_orders' => $send
        ]])->getBody()->__toString(), true);
        if ($r['code'] != 0)
        {
            throw new \Exception('send error');
        }
    }
}
