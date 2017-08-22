<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  


use App\Helper\Util;
use App\Models\DataCategoryStat;
use App\Models\DataCompetitor;
use App\Models\DataCompetitorDetailStat;
use App\Models\DataPublisher;
use App\Models\DataStatistic;
use App\Models\DataStatisticDetail;
use App\Models\DicArea;
use App\Models\DicIndustry;
use App\Models\DicRegion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
     * 获取首页统计总览
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getSummaryData($uid)
    {
//        $res = DataStatistic::where('user_id', $uid)
//            ->orderBy('id', -1)
//            ->first();
        $now = Carbon::now();
        $todayAtStr = $now->startOfDay()->toDateTimeString();
        $todayEndStr = $now->endOfDay()->toDateTimeString();
        $res = [
           'tender' =>BidRepositories::getBidListTotal(),
           'bid' =>BidRepositories::getWinnerListTotal(),
           'competitor' =>BidRepositories::getCompetitorListTotal(),
           'tender_today' =>BidRepositories::getBidListTotal($todayAtStr, $todayEndStr),
           'bid_today' =>BidRepositories::getWinnerListTotal($todayAtStr, $todayEndStr),
           'competitor_today' =>BidRepositories::getCompetitorListTotal($todayAtStr, $todayEndStr),
        ];
        return $res;
    }

    /**
     * 获取招标统计总览
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getSummaryPageData($uid)
    {
//        $res = DataStatisticDetail::where('user_id', $uid)
//            ->orderBy('id', -1)
//            ->first();
        $now = Carbon::now();
        $todayAtStr = $now->startOfDay()->toDateTimeString();
        $todayEndStr = $now->endOfDay()->toDateTimeString();
        $weekAtStr = $now->startOfWeek()->toDateTimeString();
        $weekEndStr = $now->endOfWeek()->toDateTimeString();
        $monthAtStr = $now->startOfMonth()->toDateTimeString();
        $monthEndStr = $now->endOfMonth()->toDateTimeString();
        $res = [
            'tender_today_total' =>BidRepositories::getBidListTotal($todayAtStr, $todayEndStr),
            'tender_week_total' =>BidRepositories::getBidListTotal($weekAtStr, $weekEndStr),
            'tender_month_total' =>BidRepositories::getBidListTotal($monthAtStr, $monthEndStr),
            'bid_today_total' =>BidRepositories::getWinnerListTotal($todayAtStr, $todayEndStr),
            'bid_week_total' =>BidRepositories::getWinnerListTotal($weekAtStr, $weekEndStr),
            'bid_month_total' =>BidRepositories::getWinnerListTotal($monthAtStr, $monthEndStr),
            'competitor_today_total' =>BidRepositories::getCompetitorListTotal($todayAtStr, $todayEndStr),
            'competitor_total' =>BidRepositories::getCompetitorListTotal(),
        ];
        return $res;
    }

    public static function getCompanySummaryChartData()
    {
        $result = DataCompetitor::select(DB::raw(' type name,count(*) y '))
            ->groupBy('type')
            ->get();
        $listMap = [];
        $map = ['大型企业', '中型企业', '小型企业', '民营企业', '外资企业', '国有企业', '股份企业', '私营企业',];
        if ($result->isNotEmpty()) {
            $listMap = BaseRepositories::arrayToDictionary($result, 'name');
        }
        $data = [];
        foreach ($map as $v) {
            $item = [
                'name'=>$v,
                'y'=>array_key_exists($v, $listMap) ? $listMap[$v]['y'] : 0
            ];
            $data[] = $item;
        }
//        $data = [
//          ['name'=>'大型企业', 'y'=>20],
//          ['name'=>'中型企业', 'y'=>30],
//          ['name'=>'小型企业', 'y'=>60],
//          ['name'=>'民营企业', 'y'=>40],
//          ['name'=>'外资企业', 'y'=>80],
//          ['name'=>'国有企业', 'y'=>50],
//          ['name'=>'股份企业', 'y'=>10],
//          ['name'=>'私营企业', 'y'=>700],
//       ];
       return $data;
    }
    
    public static function getCategoryChartData()
    {
        $result = DataCategoryStat::all();
        $data = [];
        foreach ($result as $v) {
            $item = [
                'name'=>$v->name,
                'y'=>$v->value
            ];
            $data[] = $item;
        }
//        $data = [
//            ['name'=>'品牌', 'y'=>40],
//            ['name'=>'资源', 'y'=>80],
//            ['name'=>'技能', 'y'=>50],
//            ['name'=>'注册资本', 'y'=>70],
//        ];
        return $data;
    }

    /**
     *竞争公司统计图表数据
     * @param $name
     * @return array
     */
    public static function getCompetitorDetailStat($name)
    {
        $data = [
            'bid'=> [],
            'power'=>[],
            'money'=>[],
        ];
        $result = DataCompetitorDetailStat::where('company_name', $name)
            ->orderBy('id', -1)
            ->first();
        $map = ['bid', 'power', 'money'];
        for ($i=1;$i<=5;$i++) {
            foreach ($map as $v) {
                $key = sprintf('%s_total_%s', $v, $i);
                $value = is_null($result) ? 0 :$result->{$key};
                $item = [
                    'name'=>$i,
                    'y'=>$value
                ];
                $data[$v][] = $item;
            }
        }
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