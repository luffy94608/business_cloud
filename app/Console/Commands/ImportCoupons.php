<?php

namespace App\Console\Commands;

use App\Helper\RuleEngine;
use App\Models\Coupon\Coupon;
use App\Models\Enum\CouponSourceEnum;
use App\Models\Enum\CouponTypeEnum;
use App\Models\Enum\ServiceName;
use App\Models\WorkDay;
use App\Tools\Pay\PayService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB;

class ImportCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_coupons{start}{key}';

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
            $appId = '583533eacbd426495d448881';  //测试
        } else {
            $appId = '58d0fcd5cbd42668750521e0';  //正式
        }
        $input = $this->output->ask(sprintf('current APP_ENV is %s, continue ?', $plat), 'Y');
        if (strtolower($input) != 'y') {
            return;
        }
        
        $importKey = $this->argument('key');
        $key = 1000;
        if (true)
        {
            try
            {

                $url = 'http://pay-center.hollo.cn:82/config/import/import_coupon';
                $num = 1000;
                # 2017-03-22导入老数据
                $coupons = DB::connection('mysql')->table('coupons')->where('expired_at', '>', Carbon::createFromTimestamp(intval($this->argument('start'))))->orderBy('user_id')->get();
                $bar = $this->output->createProgressBar($coupons->count());

                $send = [];
                $bulks = [];

                foreach ($coupons as $coupon)
                {

                    if (DB::connection('mongodb')->collection('temp_coupons')->where('user_id', $coupon->user_id)->where($coupon->type . '_list', 'all', [$coupon->id])->first())
                    {
                        $bar->advance();
//                $this->output->comment(sprintf('%s is import', $coupon->id));
                        continue;
                    }

                    $expireTime = Carbon::createFromFormat('Y-m-d H:i:s', $coupon->expired_at)->timestamp;
                    $couponType = 0;
                    $inc = ['expire_time_all' => $expireTime];
                    $push = [];
                    switch ($coupon->type)
                    {
                        case 'shuttle_ticket_coupon':
                            $couponType = CouponTypeEnum::Shuttle_Ticket;
                            $inc['shuttle_ticket_coupon'] = 1;
                            $push = ['shuttle_ticket_coupon_list' => $coupon->id];
                            break;
                        case 'shuttle_cash_coupon':
                            $couponType = CouponTypeEnum::Shuttle_Cash;
                            $inc['shuttle_cash_coupon'] = 1;
                            $inc['shuttle_cash_value_all'] = doubleval($coupon->amount);
                            $push = ['shuttle_cash_coupon_list' => $coupon->id];
                            break;
                        case 'cash_coupon':
                            $couponType = CouponTypeEnum::Bus_Cash;
                            $inc['cash_coupon'] = 1;
                            $inc['bus_cash_value_all'] = doubleval($coupon->amount);
                            $push = ['cash_coupon_list' => $coupon->id];
                            break;
                        case 'ticket_coupon':
                            $couponType = CouponTypeEnum::Bus_Ticket;
                            $inc['ticket_coupon'] = 1;
                            $push = ['ticket_coupon_list' => $coupon->id];
                            break;
                        case 'tour_coupon':
                            $couponType = CouponTypeEnum::Travel_Cash;
                            $inc['tour_coupon'] = 1;
                            $inc['tour_value_all'] = doubleval($coupon->amount);
                            $push = ['tour_coupon_list' => $coupon->id];
                            break;

                    }
                    $content = Coupon::getContentByType($couponType, doubleval($coupon->amount ? ($coupon->amount == 0 ? 1 : $coupon->amount) : 1));
                    $content['import'] = $importKey;
                    $content['import_id'] = $coupon->id;
                    $content['key'] = $key;
                    $couponSource = DB::connection('mysql')->table('coupons_source')->where('id', $coupon->id)->first();
                    $remark = '其它';
                    if (isset($couponSource))
                    {
                        $remark = CouponSourceEnum::transform($couponSource->type);
                        $content['source_id'] = $couponSource->source_id;
                        $content['source_type'] = $couponSource->type;
                    }
                    if (!is_null($coupon->order_id))
                    {
                        $content['business_id'] = $coupon->order_id;
                    }

                    $result = app(ServiceName::PayService)->sendCoupon([$coupon->user_id], [], $couponType, $expireTime,
                        $content, $remark, $appId);
//                    $data = [
//                        'user_id' => $coupon->user_id,
//                        'coupon_type' => $couponType,
//                        'coupon_content' => $content,
//                        'remark' => $remark,
//                        'expire_time' => $expireTime
//                    ];
//                    if (!is_null($coupon->order_id))
//                    {
//                        $data['business_id'] = $coupon->order_id;
//                    }
//                    $send[] = $data;
                    if (true)
                    {

//                        $bulk = new MongoDB\Driver\BulkWrite(['ordered' => false]);
//                        $bulk->update(['user_id' => $coupon->user_id], [
//                            '$inc' => $inc,
//                            '$push' => $push
//                        ], ['multi' => false, 'upsert' => true]);
//                        $bulks[] = $bulk;
                    DB::connection('mongodb')->collection('temp_coupons')->where('user_id', $coupon->user_id)
                        ->update([
                            '$inc' => $inc,
                            '$push' => $push,
                            '$setOnInsert' => ['done' => false, 'user_id' => $coupon->user_id]
                        ], ['upsert' => true]);
                    }
                    else{
                        throw new \Exception(sprintf('%s send fail', $coupon->id));
                    }

                    $bar->advance();
                    if (count($send) == $num)
                    {
//                        app(ServiceName::HttpClient)->postJson($url, ['coupons' => $send]);
                        $send = [];
//                        self::doBulk($bulks);
                        $bulks = [];
                        $key += 1;
                    }
                }
                if (count($send) > 0)
                {
//                    app(ServiceName::HttpClient)->postJson($url, ['coupons' => $send]);
                    $send = [];
//                    self::doBulk($bulks);
                }
                $bar->finish();
            }
            catch (\Exception $e)
            {
                var_dump('last key is ' . $key);
                return;
            }
        }



        $input = $this->output->ask('import done. continue ? ', 'Y');
        if (strtolower($input) != 'y')
        {
            return;
        }

        # print record
        $this->output->comment('checking');
        $record = DB::connection('mongodb')->collection('temp_coupons')->where('done', false)->get();
        $bar = $this->output->createProgressBar($record->count());
        $errors = [];
        foreach ($record as $item)
        {
            $coupons = app(ServiceName::PayService)->getCouponsList($item['user_id'], 2,
                CouponTypeEnum::all(), 0, 0, 2000, 0 ,
                $appId);
            $shuttleCouponCount = 0;
            $busCouponCount = 0;
            $shuttleCashCouponCount = 0;
            $busCashCouponCount = 0;
            $shuttleCashAllValue = 0;
            $busCashAllValue = 0;
            $expireTime = 0;

            $travelCount = 0;
            $travelValue = 0;
            foreach ($coupons as $coupon)
            {
                if (array_key_exists('import', $coupon->content) && $coupon->content['import'] == $importKey)
                {
                    $expireTime += $coupon->expiredTime;
                    switch ($coupon->type)
                    {
                        case CouponTypeEnum::Bus_Ticket:
                            $busCouponCount += 1;
                            break;
                        case CouponTypeEnum::Bus_Cash:
                            $busCashCouponCount += 1;
                            $busCashAllValue += $coupon->value;
                            break;
                        case CouponTypeEnum::Shuttle_Cash:
                            $shuttleCashCouponCount += 1;
                            $shuttleCashAllValue += $coupon->value;
                            break;
                        case CouponTypeEnum::Shuttle_Ticket:
                            $shuttleCouponCount += 1;
                            break;
                        case CouponTypeEnum::Travel_Cash:
                            $travelCount += 1;
                            $travelValue += $coupon->value;
                            break;
                    }
                }
                elseif (array_key_exists('import_id', $coupon->content))
                {
                    var_dump($coupon->content['import_id']);
                }
            }
            if (! ($shuttleCouponCount == (array_key_exists('shuttle_ticket_coupon', $item) ? $item['shuttle_ticket_coupon'] : 0)
                    && $busCouponCount == (array_key_exists('ticket_coupon', $item) ? $item['ticket_coupon'] : 0)
                    && $shuttleCashCouponCount == (array_key_exists('shuttle_cash_coupon', $item) ? $item['shuttle_cash_coupon'] : 0)
                    && $busCashCouponCount == (array_key_exists('cash_coupon', $item) ? $item['cash_coupon'] : 0)
                    && $shuttleCashAllValue == (array_key_exists('shuttle_cash_value_all', $item) ? $item['shuttle_cash_value_all'] : 0)
                    && $busCashAllValue == (array_key_exists('bus_cash_value_all', $item) ? $item['bus_cash_value_all'] : 0)
                    && $expireTime == (array_key_exists('expire_time_all', $item) ? $item['expire_time_all'] : 0)
                    && $travelCount == (array_key_exists('tour_coupon', $item) ? $item['tour_coupon'] : 0)
                    && $travelValue == (array_key_exists('tour_value_all', $item) ? $item['tour_value_all'] : 0)))
            {
                $errors[] = [
                    'user_id' => $item['user_id'],
                    'shuttle_ticket_coupon' => [$shuttleCouponCount , (array_key_exists('shuttle_ticket_coupon', $item) ? $item['shuttle_ticket_coupon'] : 0)],
                    'ticket_coupon' => [$busCouponCount , (array_key_exists('ticket_coupon', $item) ? $item['ticket_coupon'] : 0)],
                    'shuttle_cash_coupon' => [$shuttleCashCouponCount , (array_key_exists('shuttle_cash_coupon', $item) ? $item['shuttle_cash_coupon'] : 0)],
                    'cash_coupon' => [$busCashCouponCount , (array_key_exists('cash_coupon', $item) ? $item['cash_coupon'] : 0)],
                    'shuttle_cash_value_all' => [$shuttleCashAllValue , (array_key_exists('shuttle_cash_value_all', $item) ? $item['shuttle_cash_value_all'] : 0)],
                    'bus_cash_value_all' => [$busCashAllValue , (array_key_exists('bus_cash_value_all', $item) ? $item['bus_cash_value_all'] : 0)],
                    'expire_time_all' => [$expireTime , (array_key_exists('expire_time_all', $item) ? $item['expire_time_all'] : 0)],
                    'tour_coupon' => [$travelCount , (array_key_exists('tour_coupon', $item) ? $item['tour_coupon'] : 0)],
                    'tour_value_all' => [$travelValue , (array_key_exists('tour_value_all', $item) ? $item['tour_value_all'] : 0)]

                ];
                var_dump(json_encode($errors));
                $input = $this->output->ask('continue ? ', 'Y');
                if (strtolower($input) != 'y')
                {
                    return;
                }
            }
            DB::connection('mongodb')->collection('temp_coupons')->where('user_id', $item['user_id'])->update(['$set' => ['done' => true]]);
            $bar->advance();
        }

        $bar->finish();
        $this->output->comment('');
    }

    public function doBulk($bulks)
    {
        foreach ($bulks as $bulk) {
            try {
                $connectStr = sprintf('mongodb://%s:%s', env('MG_HOST'), env('MG_PORT'));
                $databaseName = env('MG_DATABASE');
                $client = new MongoDB\Client($connectStr);
                $db = $client->selectDatabase($databaseName);
                $namespace = sprintf('%s.%s', $databaseName, 'temp_coupons');
                $manager = $db->getManager();
                $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
                $result = $manager->executeBulkWrite($namespace, $bulk, $writeConcern);
            } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
                $result = $e->getWriteResult();

                // Check if the write concern could not be fulfilled
                if ($writeConcernError = $result->getWriteConcernError()) {
                    printf("%s (%d): %s\n",
                        $writeConcernError->getMessage(),
                        $writeConcernError->getCode(),
                        var_export($writeConcernError->getInfo(), true)
                    );
                }

                // Check if any write operations did not complete at all
                foreach ($result->getWriteErrors() as $writeError) {
                    printf("Operation#%d: %s (%d)\n",
                        $writeError->getIndex(),
                        $writeError->getMessage(),
                        $writeError->getCode()
                    );
                }
            } catch (MongoDB\Driver\Exception\Exception $e) {
                printf("Other error: %s\n", $e->getMessage());
                exit;
            }

        }
    }
}
