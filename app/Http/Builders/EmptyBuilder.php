<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/4/20
 * Time: 18:46
 */
                                                                                                                                   
namespace App\Http\Builders;

use App\Models\Enums\OrderShareTypeEnum;
use Carbon\Carbon;

class EmptyBuilder
{

    /**
     * 车票列表空页面
     * @return string
     */
    public static function toBuildTicketEmptyHtml()
    {
        $html = "
            <div class='empty-list'>
                <div class='eb-content ticket'>暂无可用车票</div>
            </div>
        ";
        return $html;
    }

    /**
     * 明细
     * @return string
     */
    public static function toBuildCashEmptyHtml()
    {
        $html = "
            <div class='empty-list'>
                <div class='eb-content cash'>暂无记录</div>
            </div>
        ";
        return $html;
    }

    /**
     * 优惠券
     * @return string
     */
    public static function toBuildCouponEmptyHtml()
    {
        $html = "
            <div class='empty-list relative'>
                <div class='eb-content coupon'>暂无优惠券</div>
            </div>
        ";
        return $html;
    }


}