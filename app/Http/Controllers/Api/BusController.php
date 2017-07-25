<?php

namespace App\Http\Controllers\Api;

use App\Helper\Util;
use App\Http\Builders\BusBuilder;
use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use App\Models\Enums\TicketTypeEnum;
use App\Repositories\TicketRepositories;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class BusController extends Controller
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
            'cursor_id' => 'required_if:past,1',
            'timestamp' => 'required_if:past,0',
            'past' => 'required|in:0,1',//1:向上翻页，0:向下翻页
            'type' => 'required|in:0,1',//0上班，1:下班
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = BusApi::getBusList($params['type'] ,$params['cursor_id'],$params['timestamp'],$params['past']);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            $list = $res['lines'];
            $cursorId = !empty($list) ? $list[count($list)-1]['cursor_id'] : intval($params['cursor_id']);
            $html = BusBuilder::toBuildLineListHtml($list, $cursorId);
            $firstCursorId = !empty($list) ? $list[0]['cursor_id'] : 0;
            $data = [
                'html'=>$html,
                'length'=>count($list),
                'first_cursor_id'=>$firstCursorId,  
                'cursor_id'=>$cursorId
            ];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 获取日期车票列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTicketListByDate(Request $request)
    {
        $pattern = [
            'timestamp' => 'required',
            'type' => 'sometimes',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $filterTicketType = [TicketTypeEnum::Bus, TicketTypeEnum::Shuttle];
        if (isset($params['type'])) {
            $filterTicketType = [intval($params['type'])];
        }


        $result = BusApi::getTicketListWithDate($params['timestamp']);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            $list = $res['contracts'];
            $list = TicketRepositories::ticketDateListSort($list);
            $html = BusBuilder::toBuildTicketList($list, $filterTicketType);
            $data = [
                'html'=>$html,
            ];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 获取当月车票状况
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ticketMonthMap(Request $request)
    {
        $pattern = [
            'timestamp' => 'required',
            'type' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = BusApi::ticketMonthMap($params['timestamp'], $params['type']);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            $list = $res['have_ticket_days'];
            $days = empty($list) ? [] : $list[0]['days'] ;
            $data = [
                'days'=>$days,
            ];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 班车退票
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function busRefund(Request $request)
    {
        $pattern = [
            'ticket_id' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = BusApi::refund($params['ticket_id']);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            $data['msg'] =  $result['msg'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }


    /**
     * 班车验票
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
        $result = BusApi::checkBusTicket([$ticket]);
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
     * 离线验票
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOffLineTicket(Request $request)
    {
        $pattern = [
            'bus_ticket' => 'sometimes',
            'shuttle_ticket' => 'sometimes',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        if (isset($params['bus_ticket']) && count($params['bus_ticket'])) {
            $result = BusApi::checkBusTicket($params['bus_ticket']);
        }
        if (isset($params['shuttle_ticket']) && count($params['shuttle_ticket'])) {
            $result = ShuttleApi::checkTicket($params['shuttle_ticket']);
        }
        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), [], []))->toJson());

    }

    /**
     * 班车实时位置
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

        $result = BusApi::busLinePosition($params['line_id']);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $res, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 快捷出示电子票
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quickShowTicket(Request $request)
    {
        $pattern = [
            'type' => 'required',//// 0 班车 1 摆渡车
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = BusApi::quickShowTicket($params['type']);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $data = [
                'tickets'=>$res[TicketTypeEnum::transformListKey($params['type'])]
            ];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }


    /**
     * 车票评价
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remark(Request $request)
    {
        $pattern = [
            'ticket_id' => 'required',
            'score' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $params['comment'] = Input::get('comment');

        $result = BusApi::remark($params['ticket_id'], $params['score'], $params['comment']);
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
     * 所有线路
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allLines(Request $request)
    {
        $pattern = [
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $result = BusApi::allLine();
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


}
