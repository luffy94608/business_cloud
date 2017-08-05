<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/4/20
 * Time: 18:46
 */

namespace App\Http\Builders;

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
        $keywords = ['工程', '医疗', '市政', '物流', '软件', '设计'];
        if (!empty($keywords)) {
            $html = '热门关键字：';
            foreach ($keywords as $keyword) {
                $html.= "<a>{$keyword}</a>";
            }
        }
        return $html;
    }

   

}