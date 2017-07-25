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

class OtherBuilder
{

    /**
     * 优惠券列表
     * @param $list
     * @param bool $checked
     * @return string
     */
    public static function toBuildCouponList($list, $checked = false)
    {
        $html = EmptyBuilder::toBuildCouponEmptyHtml();


        if (count($list)) {
            $html = "";
            $i = 1;
            foreach ($list as $v) {
                $couponId = $v['coupon_id'];
                $type = $v['type'];
                $title = $v['title'];
                $typeDesc = $v['type_desc'];
                $desc = $v['description'];
                $value = $v['value'];
                $isAvailable = $v['is_available'];
                $expiredTime = $v['expired_time'];
                $isExpired = $v['expired_time'] < time();
                $typeClass = $isExpired || !$isAvailable ? 'expired' : '';
                $suffixTitle = $type ==0 ? '次' : '￥';
                $expiredTitle = Carbon::createFromTimestamp($expiredTime)->toDateString();

                $checkedHtml = "";
                if($checked && $isAvailable && !$isExpired) {
                    $info = json_encode($v);
                    $checkedHtml = "
                        <input class='coupon-radio' data-info='{$info}' id='{$couponId}'  type='radio' name='coupon_radio_item'>
                    ";
                }

                $html .= "
                    <label for='{$couponId}' class='animated fadeInUp ant-delay-{$i}'>
                       <li class='hl-coupon {$typeClass}'  >
                           <div class='hlc-title'>{$typeDesc}</div>
                           <div class=\"hlc-content\">
                               <div class=\"hlcc-left\">
                                    <span>
                                       {$value}
                                    </span>
                                    <sub>{$suffixTitle}</sub>
                               </div>
                               <div class='hlcc-right'>
                                   <p class='title'>{$title}</p>
                                   <p class='sub-title'>{$desc}</p>
                                   <p class='sub-title'>有效期至：{$expiredTitle}</p>
                               </div>
                           </div>
                           {$checkedHtml}
                       </li>
                   </label>
                ";
                $i++;
            }
        }
        return $html;
    }

    /**
     * 余额明细
     * @param $list
     * @param $cursorId
     * @return string
     */
    public static function toBuildBillList($list, $cursorId)
    {
        $html = EmptyBuilder::toBuildCashEmptyHtml();

        if($cursorId>0){
            $html = "";
        }

        if (count($list)) {
            $html = "";
            $i = 1;
            foreach ($list as $v) {
                $title = $v['text'];
                $status = $v['amount']>0;
                $amount = sprintf('%s%.2f', $status?'+':'', $v['amount']);
                $classStr = $status ? 'color-green' : 'color-orange';
                $timeTitle = Carbon::createFromTimestamp($v['created_at'])->toDateString();
                $html .= "
                    <div class='media-item animated fadeInUp ant-delay-{$i}'>
                        <div class='item-bd'>
                            <h4 class='bd-tt'>{$title}</h4>
                            <div class='bd-txt'>{$timeTitle}</div>
                        </div>
                        <div class='item-right'>
                            <span class='{$classStr}'>{$amount}</span>
                        </div>
                    </div>
                ";
                $i++;
            }
        }
        return $html;
    }

    /**
     * 红包列表
     * @param $list
     * @param $cursorId
     * @return string
     */
    public static function toBonusList($list, $cursorId)
    {
        $html = EmptyBuilder::toBuildCashEmptyHtml();

        if($cursorId>0){
            $html = "";
        }
        $i = 1;
        if (count($list)) {
            $html = "";
            foreach ($list as $v) {
                $id = $v['id'];
                $title = isset($v['title'] )? $v['title'] : '购票红包';
                $expireTitle = Carbon::createFromTimestamp($v['expired_at'])->format('m月d日 H:i');
                $timeTitle = Carbon::createFromTimestamp($v['created_at'])->format('m月d日');
                $html .= "
                    <div class=\"shuttle-item animated fadeInUp ant-delay-{$i}\" data-id=\"{$id}\">
                        <div class=\"shuttle-header clearfix\">
                            <span class=\"code font-14\">{$expireTitle}前分享有效</span>
                        </div>
                        <div class=\"shuttle-body\">
                            <div class=\"item-bd\">
                                <h4 class=\"bd-tt font-18\" style=\"height: 30px;\">{$timeTitle} {$title}</h4>
                            </div>
                            <div class=\"item-right\">
    
                                <button class=\"btn btn-primary bg-orange full-width btn-s js_buy_btn\">发红包</button>
    
                            </div>
                        </div>
                    </div>
                ";
                $i++;
            }
        }
        return $html;
    }

}