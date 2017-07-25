<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use App\Models\Enums\PayTypeEnum;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{
    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 创建订单
     * @param Request $request
     * @return mixed
     */
    public function createOrder(Request $request)
    {
        $pattern = [
            'line_id'               => 'required',
            'dept_station_id'       => 'required',
            'dest_station_id'       => 'required',
            'seat'                  => 'required',
            'type'                  => 'required|in:day,month',//1 日票 2月票
            
            'year'                  => 'required_if:type,month',
            'month'                 => 'required_if:type,month',
            'frequency'             => 'required_if:type,month',
            'schedule_ids'          => 'required_if:type,day',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $result = PayApi::createContractMulti($params['line_id'], $params['dept_station_id'], $params['dest_station_id'], $params['schedule_ids'], $params['year'], $params['month'], $params['frequency'], $params['seat']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }


    /**
     * 支付
     * @param Request $request
     * @return mixed
     */
    public function pay(Request $request)
    {
        $pattern = [
            'id' => 'required',
            'use_balance' => 'required',
            'use_3rd_trade' => 'required',
            'use_coupon' => 'required',
            'coupon_id'  => 'sometimes',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $params['coupon_id'] = isset( $params['coupon_id']) ?  $params['coupon_id'] : '';

        $result = PayApi::payContract(PayTypeEnum::Bus, $this->openId, $params['id'], $params['use_coupon'], $params['coupon_id'], $params['use_balance'], $params['use_3rd_trade']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 获取支付的班车车票
     * @param Request $request
     * @return mixed
     */
    public function paidBusTicket(Request $request)
    {
        $pattern = [
            'contract_id' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = PayApi::paidBusTicket($params['contract_id']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 获取支付的快捷巴士车票
     * @param Request $request
     * @return mixed
     */
    public function paidShuttleTicket(Request $request)
    {
        $pattern = [
            'contract_id' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = PayApi::paidShuttleTicket($params['contract_id']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 支付
     * @param Request $request
     * @return mixed
     */
    public function payShuttle(Request $request)
    {
        $pattern = [
            'id' => 'required',
            'count' => 'required',
            'use_balance' => 'required',
            'use_3rd_trade' => 'required',
            'use_coupon' => 'required',
            'coupon_id'  => 'sometimes',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $params['coupon_id'] = isset( $params['coupon_id']) ?  $params['coupon_id'] : '';

        $result = PayApi::payShuttle( $params['count'], $this->openId, $params['id'], $params['use_coupon'], $params['coupon_id'], $params['use_balance'], $params['use_3rd_trade']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 通知支付结果
     * @param Request $request
     * @return mixed
     */
    public function payNotify(Request $request)
    {
        $pattern = [
            'trade_no' => 'required',
            'status' => 'required',//0失败 1成功
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = PayApi::payNotify($params['trade_no'], $params['status']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 取消订单
     * @param Request $request
     * @return mixed
     */
    public function cancelOrder(Request $request)
    {
        $pattern = [
            'id' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = PayApi::cancelOrder($params['id']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 日票座位信息
     * @param Request $request
     * @return mixed
     */
    public function busSeatsStatusByDay(Request $request)
    {
        $pattern = [
            'schedules' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = BusApi::seatsStatusByDay($params['schedules']);
        return $this->inputResult($result);
    }

    /**
     * 日票锁座或者解锁
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function busLockOrUnlockSeatsByDay(Request $request)
    {
        $pattern = [
            'bus_schedule_ids'  => 'required',
            'seat'              => 'required',
            'lock_type'         => 'required',//0锁座1解锁
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        if ($params['lock_type'] == 1) {//解锁
            $result = BusApi::unLockSeatsByDay($params['bus_schedule_ids'], $params['seat']);
        } else {
            $result = BusApi::lockSeatsByDay($params['bus_schedule_ids'], $params['seat']);
        }

        return $this->inputResult($result);
    }

    /**
     * 日票座位信息
     * @param Request $request
     * @return mixed
     */
    public function busSeatsStatusByMonth(Request $request)
    {
        $pattern = [
            'line_id'   => 'required',
            'year'      => 'required',
            'month'     => 'required',
            'frequency' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = BusApi::seatsStatusByMonth($params['line_id'], $params['year'], $params['month'], $params['frequency']);
        return $this->inputResult($result);
    }

    /**
     * 日票锁座或者解锁
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function busLockOrUnlockSeatsByMonth(Request $request)
    {
        $pattern = [
            'line_id'   => 'required',
            'year'      => 'required',
            'month'     => 'required',
            'frequency' => 'required',
            'seat'      => 'required',
            'lock_type' => 'required',//0锁座1解锁
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        if ($params['lock_type'] == 1) {//解锁
            $result = BusApi::unLockSeatsByMonth($params['line_id'], $params['year'], $params['month'], $params['frequency'], $params['seat']);
        } else {
            $result = BusApi::lockSeatsByMonth($params['line_id'], $params['year'], $params['month'], $params['frequency'], $params['seat']);
        }

        return $this->inputResult($result);
    }
}
