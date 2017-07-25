<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/4/20
 * Time: 18:46
 */
                                                                                                                                   
namespace App\Http\Builders;


use Carbon\Carbon;

class ActivityBuilder
{

    /**
     * 已经抽奖
     * @param $type
     * @return string
     */
    public static function toBuildAwardedHtml($type)
    {
        $busActive = '';
        $shuttleActive = "";
        $slideActive = "";
        if($type == 0 )
        {
            $busActive = "active";
            $slideActive = "active";
        }
        else
        {
            $shuttleActive = "active";
        }

        $html ="
            <h4 class=\"title-1 relative\">
                请选择车票类型
                <i class=\"h-icon icon-msg-info fixed-right\"></i>
            </h4>
            <p class=\"title-3\">（只能任选其一进行抽奖一次，不可同时进行，选错类型概不负责）</p>
    
            <div class=\"awd-type disabled\">
                <span class='{$shuttleActive}' data-type='1'>快捷巴士</span>
                <span class='{$busActive}' data-type='0'>班&nbsp;&nbsp;&nbsp;车</span>
                <span class='bg-slide {$slideActive}'></span>
            </div>
            <p class='text-center lottery-end'>已抽奖</p>

        ";
        return $html;
    }

    /**
     * 活动已开始
     * @return string
     */
    public static function toBuildStartHtml()
    {
        $html ="
            <h4 class=\"title-1 relative\">
                请选择车票类型
                <i class=\"h-icon icon-msg-info fixed-right\"></i>
            </h4>
            <p class=\"title-3\">（任选其一进行抽奖，不可同时进行，选错类型概不负责）</p>
            <div class=\"awd-type\">
                <span class=\"active\" data-type='1'>快捷巴士</span>
                <span data-type='0'>班&nbsp;&nbsp;&nbsp;车</span>
                <span class=\"bg-slide\"></span>
            </div>
            <button class=\"awd-submit\"></button>
        ";
        return $html;
    }

    /**
     * 活动未开始
     * @return string
     */
    public static function toBuildEndHtml()
    {
        $html ="
            <h4 class=\"title-1 relative\">
                抽奖活动暂未开启
                <i class=\"h-icon icon-msg-info fixed-right\"></i>
            </h4>
            <p class=\"title-3\">（每周五及服务号文章发布日开启）</p>
        ";
        return $html;
    }

    /**
     * 获取中奖列表
     * @param $list
     * @return string
     */
    public static function toBuildAwardListHtml($list)
    {
        $html = "";
        if(empty($list))
        {
            $html = "
                <tr>
                    <td></td>
                    <td>暂无中奖记录</td>
                    <td></td>
                </tr>";
            return $html;
        }
        foreach ($list as $v)
        {
            $aId = $v['aid'];
            $openId = $v['open_id'];
            $code = $v['code'];
            $type = $v['type'] ==0 ? '班车优惠券' : '快捷巴士优惠券';
            $time = Carbon::createFromTimestamp(strtotime($v['created_at']))->format('Y-m-d');

            $html .="
                <tr>
                    <td>{$time}</td>
                    <td>{$type}</td>
                    <td>{$code}</td>
                </tr>
            ";
        }
        return $html;
    }

    /**
     * $code
     * @param $code
     * @return string
     */
    public static function toGetItModalHtml($code)
    {
        $html ="
            <div class=\"dialog pd-10 \">
                <i class=\"h-icon icon-close \"></i>
                <div class=\"white\">
                    <img src=\"/images/activity/window_bing.png\" width=\"100%\">
                    <p class=\"bing-go\">
                        优惠码：<span id='js_copy_content'>{$code}</span>
                        <span style=\"color: #999;font-size: 1.3rem;float: right;\" class=\"\">（长按复制）</span>
                        <!--<button id='js_copy_btn' data-clipboard-target='js_copy_content'></button>-->
                    </p>
                </div>
            </div>
        ";
        return $html;
    }
    /**
     * 获取未中奖modal
     */
    public static function toNotGetItModalHtml()
    {
        $html ="
            <div class=\"dialog\">
                <i class=\"h-icon icon-close \"></i>
                <img src=\"/images/activity/window_thx.png\" width=\"100%\">
            </div>
        ";
        return $html;
    }
}