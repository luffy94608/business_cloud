<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/4/20
 * Time: 18:46
 */

namespace App\Http\Builders;

use App\Repositories\WebsiteRepositories;
use Carbon\Carbon;

class OtherBuilder
{

    /**
     * 关键词推荐
     * @return string
     */
    public static function toBuildBannerKeywordHtml()
    {
        $html = '';
//        $keywords = ['工程', '医疗', '市政', '物流', '软件', '设计'];
        $keywords = WebsiteRepositories::hotKeywords();
        if ($keywords->isNotEmpty()) {
            $html = '热门关键字：';
            foreach ($keywords as $keyword) {
                $html.= "<a>{$keyword->name}</a>";
            }
        }
        return $html;
    }

    /**
     * 分页区域
     * @return string
     */
    public static function createPageIndicator()
    {
        $html = "
                <div class=\"col-xs-12 text-center\">
                    <ul class=\"pagination\" id='pager_indicator'>
                    </ul>
                </div>";
        return $html;
    }

    /**
     * 竞争力活跃度
     * @param $level
     * @param bool $switch  true 活跃度
     * @return string
     */
    public static function toLevelHtml($level, $switch = false)
    {
        $html = "";
        $activeStyle = $switch ? 'active' : 'active-2';
        for ($i=1; $i<=5; $i++) {
            $active  = $i<=$level ? $activeStyle : '';
            $html .= "<span class='b-icon-star {$active}'></span>";
        }
        return $html;
    }

    public static function toSearchUrl($name)
    {
        return sprintf('https://www.tianyancha.com/search?key=%s&checkFrom=searchBox', $name);
    }

}