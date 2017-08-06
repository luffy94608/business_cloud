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

    /**
     * 获取统计总览
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getSummaryPageData($uid)
    {
        $res = DataStatisticDetail::where('user_id', $uid)
            ->orderBy('id', -1)
            ->first();
        return $res;
    }

    public static function getCompanySummaryChartData()
    {
       $data = [
          ['name'=>'大型企业', 'y'=>20],
          ['name'=>'中型企业', 'y'=>30],
          ['name'=>'小型企业', 'y'=>60],
          ['name'=>'民营企业', 'y'=>40],
          ['name'=>'外资企业', 'y'=>80],
          ['name'=>'国有企业', 'y'=>50],
          ['name'=>'股份企业', 'y'=>10],
          ['name'=>'私营企业', 'y'=>700],
       ];
       return $data;
    }
    
    public static function getCategoryChartData()
    {
        $data = [
            ['name'=>'品牌', 'y'=>40],
            ['name'=>'资源', 'y'=>80],
            ['name'=>'技能', 'y'=>50],
            ['name'=>'注册资本', 'y'=>70],
        ];
        return $data;
    }

    public static function bidStatData()
    {
        $data = [
            ['name'=>'7月1日', 'y'=>40],
            ['name'=>'7月2日', 'y'=>80],
            ['name'=>'7月3日', 'y'=>50],
            ['name'=>'7月4日', 'y'=>70],
            ['name'=>'7月5日', 'y'=>40],
            ['name'=>'7月6日', 'y'=>70],
        ];
        return $data;
    }
    public static function powerStatData()
    {
        $data = [
            ['name'=>'7月1日', 'y'=>40],
            ['name'=>'7月2日', 'y'=>80],
            ['name'=>'7月3日', 'y'=>50],
            ['name'=>'7月4日', 'y'=>70],
            ['name'=>'7月5日', 'y'=>40],
            ['name'=>'7月6日', 'y'=>70],
        ];
        return $data;
    }
    public static function moneyStatData()
    {
        $data = [
            ['name'=>'7月1日', 'y'=>40],
            ['name'=>'7月2日', 'y'=>80],
            ['name'=>'7月3日', 'y'=>50],
            ['name'=>'7月4日', 'y'=>70],
            ['name'=>'7月5日', 'y'=>40],
            ['name'=>'7月6日', 'y'=>70],
        ];
        return $data;
    }
}