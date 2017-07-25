<?php

namespace App\Http\Controllers\Api;

use App\Http\Builders\OtherBuilder;
use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class OtherController extends Controller
{
    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 领取优惠券
     * @param Request $request
     * @return mixed
     */
    public function getShareCoupon(Request $request)
    {
        $pattern = [
            'uid' => 'required',
            'mobile' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = OtherApi::getShareCoupon($params['uid'],$params['mobile']);
        if (isset($result['code']) && $result['code'] === 0) {
            session(['get_share_coupon_status'=>true]);
            $data = $result['data'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }



    /**
     * 订单评价
     * @param Request $request
     * @return mixed
     */
    public function remark(Request $request)
    {
        $pattern = [
            'order_id' => 'required',
            'score' => 'required',
            'tag_ids' => 'required',//1:向上翻页，0:向下翻页
            'note' => 'sometimes',//是否显示选择框
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = OrderApi::remark($params['order_id'],$params['score'],$params['tag_ids'],$params['note']);
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
     * 订单评价
     * @param Request $request
     * @return mixed
     */
    public function share(Request $request)
    {
        $pattern = [
            'uid' => 'required',
            'type' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = OtherApi::share($params['uid'],$params['type']);
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
     * 错误捕获
     * @param Request $request
     * @return mixed
     */
    public function trackError(Request $request)
    {
        $pattern = [
            'msg' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $path = storage_path('logs/client-error.log');
        $handle = new RotatingFileHandler($path);
        $handle->setFormatter(new LineFormatter(null, null, true));
        $logger = new Logger('client_error');
        $logger->pushHandler($handle);
        $logger->error($params['msg'], []);

        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), []))->toJson());

    }
}
