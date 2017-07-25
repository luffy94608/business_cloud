<?php

namespace App\Http\Controllers\Api;

use App\Helper\Util;
use App\Http\Builders\BusBuilder;
use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ShuttleController extends Controller
{
    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 线路列表
     * @param Request $request
     * @return mixed
     */
    public function getLineList(Request $request)
    {
        $pattern = [
            'timestamp' => 'sometimes',
            'loc'=> 'sometimes',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $timestamp = isset($params['timestamp']) ? $params['timestamp'] : 0;
        $loc = isset($params['loc']) ? $params['loc'] : 0;

        $result = ShuttleApi::LinesList($timestamp , $loc);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];

            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }


    /**
     * 快捷巴士退票
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refund(Request $request)
    {
        $pattern = [
            'ticket_id' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = ShuttleApi::refund($params['ticket_id']);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            $data = [];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }


    /**
     * 快捷巴士验票
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkTicket(Request $request)
    {
        $pattern = [
            'ticket_id' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $now = Carbon::now();
        $ticket = Util::toCheckTicketStructure($params['ticket_id'], $now->timestamp);
        $result = ShuttleApi::checkTicket([$ticket]);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            $data = [
                'time'=>$now->timestamp
            ];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }


    /**
     * 快捷巴士实时位置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function busLinePosition(Request $request)
    {
        $pattern = [
            'line_id' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = ShuttleApi::shuttleLinePosition($params['line_id']);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
//            $item = [
//                'bus_id'=> 'abc',
//                'loc'=> [
//                    'lat'=>40.150714,
//                    'lng'=>116.226654,
//                    'name'=> "沙河高教园地铁A2口北侧路边"
//                ],
//                'direction'=> '0',
//            ];
//            $item2 = [
//                'bus_id'=> 'abce',
//                'loc'=> [
//                    'lat'=>40.120714,
//                    'lng'=>116.286654,
//                    'name'=> "沙河高教园地铁A2口北侧路边"
//                ],
//                'direction'=> '180',
//            ];
//            $res ['bus_reals'][] = $item;
//            $res ['bus_reals'][] = $item2;
            $heart = isset($res['heart']) ? $res['heart'] : [];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $res, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

}
