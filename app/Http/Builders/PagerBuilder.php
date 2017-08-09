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
            $deadline = Carbon::createFromTimestamp($v->timestamp)->toDateString();
            $levelHtml = OtherBuilder::toLevelHtml($v->power);

            $html.="
                    <div class=\"{$grid} col-xs-12 cursor-pointer mt-10 js_location_url\" data-target='_blank' data-url='{$url}'>
                        <div class=\"col-xs-12 bc-item-hover border bc-list-item\">
                            <div class=\"col-xs-2 bcl-img\">
                                <img src=\"/images/default@2x.png\" width=\"60px\">
                            </div>
                            <div class=\"col-xs-6 text-left\">
                                <p class=\"text-cut col-xs-12\">{$title}</p>
                                <p class=\"col-xs-12\">招标人：{$userName}</p>
                                <p class=\"col-xs-12\">截止时间：{$deadline}</p>
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
            $company = $v->bid_company;
            $time = Carbon::createFromTimestamp($v->bid_time)->toDateString();
            $price = sprintf('%.2f', $v->bid_price/10000);
            $html.="
                    <div class=\"{$grid} col-xs-12 cursor-pointer mt-10\">
                        <div class=\"col-xs-12 bc-item-hover border js_location_url bc-list-item\" data-target='_blank' data-url='{$url}'>
                            <div class=\"col-xs-9 text-left\">
                                <p class=\"text-cut\"><span class=\"b-icon-tip mr-10 \"></span>{$projectName}</p>
                                <p class=\"col-xs-12\">中标企业：{$company}</p>
                                <p class=\"col-xs-12\">中标时间：{$time}</p>
                            </div>
                            <div class=\"col-xs-3 bcl-right\">
                                <p class=\"font-16 mt-35\">价格<span class=\"color-orange\"><span class=\"ml-5 mr-5 font-30\">{$price}</span> 万</span></p>
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
                        <div class=\"col-xs-2 bcl-img\">
                            <img src=\"{$logo}\" width=\"60px\">
                        </div>
                        <div class=\"col-xs-6 text-left\">
                            <p class=\"text-cut col-xs-12 js_location_url\" data-url='{$url}' data-target='_blank'>{$company}</p>
                            <p class=\"col-xs-12 text-cut\">中标项目数量：{$bidTotal}个</p>
                            <p class=\"col-xs-12\">中标候选人次数：{$candidateTotal}次</p>
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