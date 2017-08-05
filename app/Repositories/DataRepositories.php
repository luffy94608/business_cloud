<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  


use App\Models\DataStatistic;
use App\Models\DataStatisticDetail;
use App\Models\DicArea;
use App\Models\DicIndustry;
use App\Models\DicRegion;

class DataRepositories
{
    /**
     * 获取地区列表
     * @param int $parentId
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getRegionListData($parentId = -1)
    {
        if ($parentId === -1) {
            $regions = DicRegion::all();
        } else {
            $regions = DicRegion::where('parent_id', $parentId)
                ->get();
        }
        $regions = $regions->toArray();
        return $regions;
    }

    /**
     * 获取行业列表
     * @param int $parentId
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getIndustryListData($parentId = -1)
    {
        if ($parentId === -1) {
            $industry = DicIndustry::all();
        } else {
            $industry = DicIndustry::where('parent_id', $parentId)
                ->get();
        }
        $industry = $industry->toArray();
        return $industry;
    }

    /**
     * 获取行业列表
     * @param $ids
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getRegionListByIds($ids)
    {
        $industry = DicRegion::whereIn('id', $ids)
            ->get();
        $industry = $industry->toArray();
        return $industry;
    }

    /**
     * 获取统计总览
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getSummaryData($uid)
    {
        $res = DataStatistic::where('user_id', $uid)
            ->orderBy('id', -1)
            ->first();
        return $res;
    }
}