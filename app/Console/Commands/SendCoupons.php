<?php

namespace App\Console\Commands;

use App\Helper\RuleEngine;
use App\Models\Coupon\Coupon;
use App\Models\Enum\CouponTypeEnum;
use App\Models\Enum\ServiceName;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_coupons{mobile}{coupon_type}{coupon_value}{count}';

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
        $mobile = $this->argument('mobile');
        $user = User::where('name', $mobile)->first();
        $userId = $user->id;
        $couponType = intval($this->argument('coupon_type'));
        $couponValue = floatval($this->argument('coupon_value'));
        $count = intval($this->argument('count'));

        $content = Coupon::getContentByType($couponType, $couponValue);

        $users = [];
        while ($count > 0) {
            $count -= 1;
            $users[] = $userId;
        }
        if (count($users) > 0) {
            $result = app(ServiceName::PayService)->sendCoupon($users, [], $couponType, Carbon::now()->timestamp + 86400 * 30,
                $content, '自主赠送', RuleEngine::getPayId('', null));
            if ($result) {
                var_dump('发送成功');
            }
            else{
                var_dump('发送失败');
            }
        }

    }
}
