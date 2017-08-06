<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/4/20
 * Time: 18:46
 */
                                                                                                                                   
namespace App\Http\Builders;

use Carbon\Carbon;

class EmptyBuilder
{

    /**
     * 空页面
     * @return string
     */
    public static function toEmptyHtml()
    {
        $html = "
            <div class=\"col-xs-12 text-center color-hint pt-50 pb-50\">
                        暂无发布信息
            </div>
        ";
        return $html;
    }

}