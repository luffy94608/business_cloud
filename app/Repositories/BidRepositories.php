<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  


use App\Helper\Util;
use App\Models\DataBid;
use App\Models\DataCompetitor;
use App\Models\DataPublisher;
use App\Models\DataStatistic;
use App\Models\DataStatisticDetail;
use App\Models\DicArea;

class BidRepositories
{
    /**
     * @param int $offset
     * @param int $length
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public static function getBidListData($offset=0, $length = 10, $type = 'all')
    {
        $uid = Util::getUid();
        $areaId = Util::getFollowAreaId();
        $industryId = Util::getFollowIndustryId();
        $model = DataPublisher::where('user_id', $uid)
            ->where('area_id', $areaId)
            ->where('industry_id', $industryId)
            ->skip($offset)->take($length);
        switch ($type) {
            case 'new' :
                $model = $model->orderBy('created_at', -1);
                break;
            case 'all' :
                $model = $model->orderBy('id', -1);
                break;
            case 'hot' :

                break;
        }
        $list = $model->get();
        return $list;
    }

    /**
     * @param $startTimeStr
     * @param $endTimeStr
     * @return int
     */
    public static function getBidListTotal($startTimeStr = '', $endTimeStr = '')
    {
        $uid = Util::getUid();
        $areaId = Util::getFollowAreaId();
        $industryId = Util::getFollowIndustryId();
        $model = DataPublisher::where('user_id', $uid)
            ->where('area_id', $areaId)
            ->where('industry_id', $industryId);
        if (!empty($startTimeStr)) {
            $model->where('created_at', '>', $startTimeStr);
        }
        if (!empty($endTimeStr)) {
            $model->where('created_at', '<=', $endTimeStr);
        }
        $total = $model ->count();
        return $total;
    }

    /**
     * @param int $offset
     * @param int $length
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public static function getWinnerListData($offset=0, $length = 10)
    {
        $uid = Util::getUid();
        $areaId = Util::getFollowAreaId();
        $industryId = Util::getFollowIndustryId();
        $list = DataBid::where('user_id', $uid)
            ->where('area_id', $areaId)
            ->where('industry_id', $industryId)
            ->skip($offset)
            ->take($length)
            ->orderBy('created_at', -1)
            ->get();
        return $list;
    }

    /**
     * @param $startTimeStr
     * @param $endTimeStr
     * @return int
     */
    public static function getWinnerListTotal($startTimeStr = '', $endTimeStr = '')
    {
        $uid = Util::getUid();
        $areaId = Util::getFollowAreaId();
        $industryId = Util::getFollowIndustryId();
        $model = DataBid::where('user_id', $uid)
            ->where('area_id', $areaId)
            ->where('industry_id', $industryId);
        if (!empty($startTimeStr)) {
            $model->where('created_at', '>', $startTimeStr);
        }
        if (!empty($endTimeStr)) {
            $model->where('created_at', '<=', $endTimeStr);
        }
        $total = $model ->count();
        return $total;
    }

    /**
     * @param int $offset
     * @param int $length
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public static function getCompetitorListData($offset=0, $length = 10)
    {
        $uid = Util::getUid();
        $list = DataCompetitor::where('user_id', $uid)
            ->skip($offset)
            ->take($length)
            ->orderBy('created_at', -1)
            ->get();
        return $list;
    }

    /**
     * @param $startTimeStr
     * @param $endTimeStr
     * @return int
     */
    public static function getCompetitorListTotal($startTimeStr = '', $endTimeStr = '')
    {
        $uid = Util::getUid();
        $model = DataCompetitor::where('user_id', $uid);
        if (!empty($startTimeStr)) {
            $model->where('created_at', '>', $startTimeStr);
        }
        if (!empty($endTimeStr)) {
            $model->where('created_at', '<=', $endTimeStr);
        }
        $total = $model ->count();
        return $total;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public static function getCompetitorDetail($id)
    {
        $res = DataCompetitor::find($id);
        return $res;
    }


    public static function search($offset=0, $length = 10, $keyword = '', $src)
    {
        $model = '';
        $uid = Util::getUid();
        $areaId = Util::getFollowAreaId();
        $industryId = Util::getFollowIndustryId();
        
        switch ($src) {
            case 'publish':
                $model = DataPublisher::where('title', 'like', '%'.$keyword.'%')
                    ->orWhere('publisher', 'like', '%'.$keyword.'%');
                $model = $model->where('area_id', $areaId);
                $model = $model->where('industry_id', $industryId);
                break;
            case 'bid':
                $model = DataBid::where('title', 'like', '%'.$keyword.'%')
                    ->orWhere('publisher', 'like', '%'.$keyword.'%')
                    ->orWhere('bid_company', 'like', '%'.$keyword.'%');
                $model = $model->where('area_id', $areaId);
                $model = $model->where('industry_id', $industryId);
                break;
            case 'competitor':
                $model = DataCompetitor::where('company', 'like', '%'.$keyword.'%');

                break;
        }
        $model = $model->where('user_id', $uid);
        $total = $model->count();
        $list = $model->skip($offset)
            ->take($length)
            ->orderBy('id', -1)
            ->get();
        $result = [
            'list'=>$list,
            'total'=>$total,
        ];
        return $result;
    }

}