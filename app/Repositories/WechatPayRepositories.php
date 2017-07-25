<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  

use App\Models\ProductInfo;
use Carbon\Carbon;

class WechatPayRepositories
{

    /**
     * 生成out_trade_no
     * @return string
     */
    public static function getMchBillNumber()
    {
        $now = Carbon::now();
        $mchId = env('WECHAT_PAYMENT_MERCHANT_ID');
        $timeStr = $now->format('Ymd');
        $idStr = strval($now->timestamp);
        $len = strlen($idStr);
        if($len > 10)
        {
            $tailStr = substr($idStr,$len - 11,$len);
        }
        else
        {
            $needLen = 10 - $len;
            $fmt = sprintf('%%0%dd',$needLen);
            $tailStr = sprintf($fmt,$idStr);
        }
        return $mchId.$timeStr.$tailStr;
    }


}