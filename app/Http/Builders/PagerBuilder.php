<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/4/20
 * Time: 18:46
 */

namespace App\Http\Builders;

use Carbon\Carbon;

class PagerBuilder
{

    /**
     * 招标列表
     * @param $list
     * @param $halfStatus
     * @return string
     */
    public static function toBidListHtml($list, $halfStatus = true)
    {
        $html = '';
        if($list->isEmpty()){
            return EmptyBuilder::toEmptyHtml();
        }
        $grid = $halfStatus ? 'col-sm-6' : 'col-sm-12';
        foreach($list as $v){
            $url = $v->url;
            $title = $v->title;
            $userName = isset($v->publisher) ? $v->publisher : '未知';
            $deadline = Carbon::createFromTimestamp($v->deadline)->toDateString();
            $levelHtml = OtherBuilder::toLevelHtml($v->power);

            $html.="
                    <div class=\"{$grid} col-xs-12 cursor-pointer mt-10 js_location_url\" data-target='_blank' data-url='{$url}'>
                        <div class=\"col-xs-12 bc-item-hover border bc-list-item\">
                            <div class=\"col-xs-2 bcl-img gone\">
                                <img src=\"/images/default@2x.png\" width=\"60px\">
                            </div>
                            <div class=\"col-xs-8 text-left\">
                                <p class=\"text-cut col-xs-12\"><span class=\"b-icon-tip mr-10 \"></span>{$title}</p>
                                <p class=\"col-xs-12 text-cut font-12 color-sub-title\">招标人：{$userName}</p>
                                <p class=\"col-xs-12 font-12 color-sub-title\">开始日期：{$deadline}</p>
                            </div>
                            <div class=\"col-xs-4 bcl-right pt-15 \">
                                <p class=\"text-center\">
                                   {$levelHtml} 
                                </p>
                                <p class=\"text-center\">竞争力</p>
                            </div>
                        </div>
                    </div>
                ";
        }

        return $html;
    }

    /**
     * 中标列表
     * @param $list
     * @param $halfStatus
     * @return string
     */
    public static function toWinnerListHtml($list, $halfStatus = true)
    {
        $html = '';
        if($list->isEmpty()){
            return EmptyBuilder::toEmptyHtml();
        }
        $grid = $halfStatus ? 'col-sm-6' : 'col-sm-12';
        foreach($list as $v){
            $url = isset($v->url) ? $v->url : '';
            $projectName = $v->title;
            $company = $v->bid_company ?: '暂未公开';
            $time = Carbon::createFromTimestamp($v->bid_time)->toDateString();
            $price = $v->bid_price;
            if ($price < 10000) {
                $price = sprintf('%.1f', $price/10000);
            } else {
                $price = sprintf('%d', $price/10000);
            }
            $price = $price == 0?'N/A' : $price;
            $priceTitle = $price == 0?'' : '万';
            $html.="
                    <div class=\"{$grid} col-xs-12 cursor-pointer mt-10\">
                        <div class=\"col-xs-12 bc-item-hover border js_location_url bc-list-item\" data-target='_blank' data-url='{$url}'>
                            <div class=\"col-xs-8 col-sm-9 text-left\">
                                <p class=\"text-cut\"><span class=\"b-icon-tip mr-10 \"></span>{$projectName}</p>
                                <p class=\"col-xs-12 font-12 text-cut color-sub-title\">中标企业：{$company}</p>
                                <p class=\"col-xs-12 font-12 color-sub-title\">中标时间：{$time}</p>
                            </div>
                            <div class=\"col-xs-4 col-sm-3 bcl-right\">
                                <p class=\"font-16 mt-35\"><span class='hidden-xs'></span><span class=\"color-orange\"><span class=\"ml-5 mr-5 font-24\">{$price}</span> {$priceTitle}</span></p>
                            </div>
                        </div>
                    </div>
                ";
        }

        return $html;
    }

    /**
     * 企业列表
     * @param $list
     * @param $halfStatus
     * @return string
     */
    public static function toCompetitorListHtml($list, $halfStatus = true)
    {
        $html = '';
        if($list->isEmpty()){
            return EmptyBuilder::toEmptyHtml();
        }
        $grid = $halfStatus ? 'col-sm-6' : 'col-sm-12';
        foreach($list as $v){
            $id = $v->id;
            $logo = isset($v->logo) ? $v->logo : '/images/default@2x.png';
            $company = $v->company;
            $bidTotal = $v->bid_total;
            $candidateTotal = $v->candidate_total;
            $levelHtml = OtherBuilder::toLevelHtml($v->power);
            $liveHtml = OtherBuilder::toLevelHtml($v->liveness, true);
            $url = OtherBuilder::toSearchUrl($company);
            $html.="
                 <div class=\"{$grid} col-xs-12 mt-10 cursor-pointer js_list_item\" data-id='{$id}'>
                    <div class=\"col-xs-12 bc-item-hover border bc-list-item\">
                        <div class=\"col-xs-2 bcl-img gone\">
                            <img src=\"{$logo}\" width=\"60px\">
                        </div>
                        <div class=\"col-xs-8 text-left\">
                            <p class=\"text-cut col-xs-12 js_location_url\" data-url='{$url}' data-target='_blank'><span class=\"b-icon-tip mr-10 \"></span>{$company}</p>
                            <p class=\"col-xs-12 font-12 color-sub-title text-cut\">中标项目数量：{$bidTotal}个</p>
                            <p class=\"col-xs-12 font-12 color-sub-title\">中标候选人次数：{$candidateTotal}次</p>
                        </div>
                        <div class=\"col-xs-4 bcl-right pt-15\">
                            <p>
                                <span class='hidden-xs'>竞争力：</span>
                                {$levelHtml}
                            </p>
                            <p>
                                <span class='hidden-xs'>活跃度：</span>
                                {$liveHtml}
                            </p>
                        </div>
                    </div>
                </div>
                ";
        }

        return $html;
    }

}