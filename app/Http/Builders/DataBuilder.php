<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/4/20
 * Time: 18:46
 */
                                                                                                                                   
namespace App\Http\Builders;


use App\Repositories\DataRepositories;

class DataBuilder
{

    /**
     * 一级省市菜单
     * @param int $checkedId
     * @return string
     */
    public static function toRegionLevelOneOptionHtml($checkedId = -1)
    {
        $regions = DataRepositories::getRegionListData(1);
        $html = '';

        foreach ($regions as $region) {
            $id = $region['id'];
            $checked = $id == $checkedId ? 'selected' : '';
            $html.= "<option {$checked} value='{$region['id']}'>{$region['region_name']}</option>";
        }
        return $html;
    }

    /**
     * 一级行业
     * @param int $checkedId
     * @return string
     */
    public static function toIndustryLevelOneOptionHtml($checkedId = -1)
    {
        $regions = DataRepositories::getIndustryListData(0);
        $html = '';

        foreach ($regions as $region) {
            $id = $region['id'];
            $checked = $id == $checkedId ? 'selected' : '';
            $html.= "<option {$checked} value='{$id}'>{$region['name']}</option>";
        }
        return $html;
    }

    
}