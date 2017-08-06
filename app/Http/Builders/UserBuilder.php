<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/4/20
 * Time: 18:46
 */
                                                                                                                                   
namespace App\Http\Builders;


use App\Repositories\DataRepositories;

class UserBuilder
{

    /**
     * 用户选择的关键词
     * @param $profile
     * @return string
     */
    public static function toUserFollowKeywordHtml($options)
    {
        $html = '';
//        $options = $profile['follow_keyword'];
        if (!empty($options)) {
            $options = explode(',', $options);
            foreach ($options as $option) {
                $key = $option;
                $html.= sprintf("<span class='bck-item active' data-id='%s'>%s<i class='b-icon-close ml-5\'></i></span>", $key, $key);
            }
        }
        return $html;
    }

    /**
     * 用户关注地区
     * @param $profile
     * @return string
     */
    public static function toUserFollowAreaHtml($ids)
    {
        $html = '';
//        $ids = $profile['follow_area'];
        if (!empty($ids)) {
            $ids = explode(',', $ids);
            $options = DataRepositories::getRegionListByIds($ids);
            foreach ($options as $option) {
                $id = $option['id'];
                $name = $option['region_name'];
                $html.= sprintf("<span class='bck-item active' data-id='%s'>%s<i class='b-icon-close ml-5\'></i></span>", $id, $name);
            }
        }
        return $html;
    }

    
}