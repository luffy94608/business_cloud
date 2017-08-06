<?php

namespace App\Http\Controllers\Api;

use App\Helper\Util;
use App\Http\Builders\PagerBuilder;
use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use App\Repositories\BidRepositories;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 招标列表
     * @param Request $request
     * @return mixed
     */
    public function getBidList(Request $request)
    {
        $pattern = [
            'offset' => 'sometimes',
            'length'=> 'sometimes',
            'limit'=> 'sometimes',
            'type'=> 'sometimes',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $params['offset'] = isset($params['offset']) ? $params['offset'] : 0;
        $params['length'] = isset($params['length']) ? $params['length'] : 0;
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 0;
        $params['type'] = isset($params['type']) ? $params['type'] : 'all';

        $result = BidRepositories::getBidListData($params['offset'], $params['length'], $params['type']);
        $html = PagerBuilder::toBidListHtml($result);
        $total = 0;
        if ($params['offset'] ==0) {
            $total = BidRepositories::getBidListTotal();
        }
        $limit = $params['length']*$params['limit'];
        $data = [
            'html'=>$html,
            'total'=>$total > $limit && $params['limit']>0 ? $limit : $total
        ];
        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, []))->toJson());
    }

    /**
     * 中标列表
     * @param Request $request
     * @return mixed
     */
    public function getBidResultList(Request $request)
    {
        $pattern = [
            'offset' => 'sometimes',
            'length'=> 'sometimes',
            'limit'=> 'sometimes',
            'type'=> 'sometimes',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $params['offset'] = isset($params['offset']) ? $params['offset'] : 0;
        $params['length'] = isset($params['length']) ? $params['length'] : 0;
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 0;

        $result = BidRepositories::getWinnerListData($params['offset'], $params['length']);
        $html = PagerBuilder::toWinnerListHtml($result);
        $total = 0;
        if ($params['offset'] ==0) {
            $total = BidRepositories::getWinnerListTotal();
        }
        $limit = $params['length']*$params['limit'];
        $data = [
            'html'=>$html,
            'total'=>$total > $limit && $params['limit']>0 ? $limit : $total
        ];
        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, []))->toJson());
    }

    /**
     * 企业列表
     * @param Request $request
     * @return mixed
     */
    public function getCompetitorList(Request $request)
    {
        $pattern = [
            'offset' => 'sometimes',
            'length'=> 'sometimes',
            'limit'=> 'sometimes',
            'type'=> 'sometimes',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $params['offset'] = isset($params['offset']) ? $params['offset'] : 0;
        $params['length'] = isset($params['length']) ? $params['length'] : 0;
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 0;

        $result = BidRepositories::getCompetitorListData($params['offset'], $params['length']);
        $html = PagerBuilder::toCompetitorListHtml($result);
        $total = 0;
        if ($params['offset'] ==0) {
            $total = BidRepositories::getCompetitorListTotal();
        }
        $limit = $params['length']*$params['limit'];
        $data = [
            'html'=>$html,
            'total'=>$total > $limit && $params['limit']>0 ? $limit : $total
        ];
        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, []))->toJson());
    }


    /**
     * 企业列表
     * @param Request $request
     * @return mixed
     */
    public function searchList(Request $request)
    {
        $pattern = [
            'offset' => 'sometimes',
            'length'=> 'sometimes',
            'limit'=> 'sometimes',
            'src'=> 'sometimes',
            'keyword'=> 'sometimes',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $params['offset'] = isset($params['offset']) ? $params['offset'] : 0;
        $params['length'] = isset($params['length']) ? $params['length'] : 0;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : '';
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 0;
        $src = isset($params['src']) ? $params['src'] : 0;

        $result = BidRepositories::search($params['offset'], $params['length'], $params['keyword'], $src);
        $html = '';
        $list = $result['list'];
        switch ($src) {
            case 'publish':
                $html = PagerBuilder::toBidListHtml($list, false);
                break;
            case 'bid':
                $html = PagerBuilder::toWinnerListHtml($list, false);
                break;
            case 'competitor':
                $html = PagerBuilder::toCompetitorListHtml($list, false);
                break;
        }
        $total = 0;
        if ($params['offset'] ==0) {
            $total = $result['total'];
        }
        $limit = $params['length']*$params['limit'];
        $data = [
            'html'=>$html,
            'total'=>$total > $limit && $params['limit']>0 ? $limit : $total
        ];
        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, []))->toJson());
    }


}
