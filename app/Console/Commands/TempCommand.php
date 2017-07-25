<?php

namespace App\Console\Commands;

use App\Helper\RedisHelper;
use App\Helper\Util;
use Doctrine\Common\Cache\PredisCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use MongoDB;

class TempCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp';

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
        $token = Util::getUserToken();
        $uid = Util::getUid();
        $token = Redis::get(Util::userCacheTokenKey($uid));
        echo sprintf('uid:: %s',$uid) . PHP_EOL;
        echo sprintf('用户token:: %s',$token) . PHP_EOL;

//        $predis = app('redis')->connection();// connection($name), $name 默认为 `default`
//        $cacheDriver = new PredisCache($predis);
//        $app = app('wechat');
//        $app->driver = $cacheDriver;
//        $payment = $app->payment;
//        $url = $payment->scheme(1);
//        $shortUrl = '';
//        $res = $payment->urlShorten($url);
//        if (strtolower($res['return_code']) == 'success') {
//            $shortUrl = $res['short_url'];
//        }
////        echo $url.PHP_EOL;
//        echo sprintf('支付短链接:: %s',$shortUrl) . PHP_EOL;
    }
}
