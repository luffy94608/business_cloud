<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  


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
        $model = DataPublisher::skip($offset)->take($length);
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
     * @return array|\Illuminate\Support\Collection
     */
    public static function getBidListTotal()
    {
        $total = DataPublisher::count();
        return $total;
    }

    /**
     * @param int $offset
     * @param int $length
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public static function getWinnerListData($offset=0, $length = 10)
    {
        $list = DataBid::skip($offset)
            ->take($length)
            ->orderBy('created_at', -1)
            ->get();
        return $list;
    }

    /**
     * @return array|\Illuminate\Support\Collection
     */
    public static function getWinnerListTotal()
    {
        $total = DataBid::count();
        return $total;
    }

    /**
     * @param int $offset
     * @param int $length
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public static function getCompetitorListData($offset=0, $length = 10)
    {
        $list = DataCompetitor::skip($offset)
            ->take($length)
            ->orderBy('created_at', -1)
            ->get();
        return $list;
    }

    /**
     * @return array|\Illuminate\Support\Collection
     */
    public static function getCompetitorListTotal()
    {
        $total = DataCompetitor::count();
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
        switch ($src) {
            case 'publish':
                $model = DataPublisher::where('title', 'like', '%'.$keyword.'%');
                break;
            case 'bid':
                $model = DataBid::where('company_name', 'like', '%'.$keyword.'%')
                    ->orWhere('project_name', 'like', '%'.$keyword.'%');

                break;
            case 'competitor':
                $model = DataCompetitor::where('company', 'like', '%'.$keyword.'%');

                break;
        }
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