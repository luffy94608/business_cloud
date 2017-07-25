<?php

namespace App\Http\Controllers\Api;

use App\Helper\Util;
use App\Http\Builders\OtherBuilder;
use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取手机验证码
     * @param  $request
     * @return mixed
     */
    public function verifyCode(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required|digits:11',
            'type' => 'required|in:0,1,2,3',//注册码类型0-注册, 1-登录, 2-密码重置, 3-绑定手机
        ]);

        $params = $request->only(
            'mobile', 'type'
        );

        $result = UserApi::getVerifyCode($params['mobile'], $params['type']);
        if ($result['code'] === 0) {
            $data = $result['data'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 注册
     * @param Request $request
     * @return mixed
     */
//    public function register(Request $request)
//    {
//        $this->validate($request, [
//            'mobile' => 'required|digits:11',
//            'code' => 'required',
//            'password' => 'required',
//        ]);
//
//        $params = $request->only(
//            'mobile', 'code', 'password'
//        );
//
//        $result = UserApi::register($this->openId,$params['mobile'], $params['password'], $params['code']);
//        if ($result['code'] === 0) {
//            $data = $result['data'];
//            $cookie = Cookie::forever('user_mobile', $params['mobile']);
//            $cookie2 = Cookie::forever('user_psw', isset($params['password']) ? $params['password'] : '');
//            $this->saveLoginData($this->openId, $data);
//
//            $result = UserApi::reset($params['password']);
//            if (isset($result['code']) && $result['code'] === 0) {
//
//                $data = [
//                    'url'=>$this->getReferUrl()
//                ];
//                $heart = isset($data['heart']) ? $data['heart'] : [];
//                return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson())
//                    ->withCookie($cookie)
//                    ->withCookie($cookie2);
//            }
//        } else {
//            $code = isset($result['code']) ? $result['code'] : -1;
//            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
//            return response()->json((new ApiResult($code, $desc, $result))->toJson());
//        }
//    }


    /**
     * 忘记密码
     * @param Request $request
     * @return mixed
     */
    public function reset(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required|digits:11',
            'code' => 'required',
            'password' => 'required',
        ]);

        $params = $request->only(
            'mobile', 'code', 'password'
        );
        $result = UserApi::reset($params['mobile'], $params['password'], $params['code']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            $cookie = Cookie::forever('user_mobile', $params['mobile']);
            $cookie2 = Cookie::forever('user_psw', isset($params['password']) ? $params['password'] : '');
            $this->saveLoginData($this->openId, $data);
            $data = [
                'url'=>$this->getReferUrl()
            ];
            $heart = isset($data['heart']) ? $data['heart'] : [];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson())
                ->withCookie($cookie)
                ->withCookie($cookie2);
        }
        $code = isset($result['code']) ? $result['code'] : -1;
        $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
        return response()->json((new ApiResult($code, $desc, $result))->toJson());
    }


    /**
     * 登录
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $pattern = [
            'mobile'    => 'required|digits:11',
            'type'      => 'required|in:1,2',
            'code'      => 'required_if:type,1',
            'password'  => 'required_if:type,2',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        if ($params['type'] == 1) {
            $result = UserApi::loginCode($params['mobile'], $params['code']);
        } else{
            $result = UserApi::loginPSW($params['mobile'], $params['password']);
        }
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            $cookie = Cookie::forever('user_mobile', $params['mobile']);
            $cookie2 = Cookie::forever('user_psw', isset($params['password']) ? $params['password'] : '');
            $this->saveLoginData($this->openId, $data);
            $data = [
                'url'=>$this->getReferUrl()
            ];
            $heart = isset($data['heart']) ? $data['heart'] : [];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson())
                ->withCookie($cookie)
                ->withCookie($cookie2);
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }


    /**
     * 保存登录成功的数据
     * @param $openId
     * @param $data
     */
    private function saveLoginData($openId, $data)
    {
//        Session::put('account_info',$data);
        Util::setCacheUserInfo($data);
        Log::info('opend_id'.$openId);
        if($openId)
        {
            Cookie::forever('user_info', $data['account']['user']);
            User::updateUserInfo($this->openId, $data['uid'], $data['sid']);
            $this->refreshInitInfo();
        }
    }


    /**
     * 修改姓名
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $pattern = [
            'name' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = UserApi::updateProfile($params['name']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            $heart = isset($data['heart']) ? $data['heart'] : [];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        }

        $code = isset($result['code']) ? $result['code'] : -1;
        $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
        return response()->json((new ApiResult($code, $desc, $result))->toJson());
    }

    /**
     * 二维码兑换
     * @param Request $request
     * @return mixed
     */
    public function exchangeCode(Request $request)
    {
        $pattern = [
            'code' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = UserApi::exchangeCode($params['code']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            $heart = isset($data['heart']) ? $data['heart'] : [];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        }

        $code = isset($result['code']) ? $result['code'] : -1;
        $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
        return response()->json((new ApiResult($code, $desc, $result))->toJson());
    }


    /**
     * 获取上一个url default /
     * @return string
     */
    public function getReferUrl()
    {
        $redirectUrl = '';
        $redirectObj  = redirect()->intended();
        if ($redirectObj->getStatusCode() == 200) {
            $redirectUrl = $redirectObj->getTargetUrl();
        }
        $redirectUrl = $redirectUrl ? $redirectUrl : '/';
        return $redirectUrl.'?v=1';
    }


    /**
     * 获取优惠券列表
     * @param Request $request
     * @return mixed
     */
    public function getCouponList(Request $request)
    {
        $pattern = [
            'cursor_id' => 'required_if:past,1',
            'timestamp' => 'required_if:past,0',
            'past' => 'required|in:0,1',//1:向上翻页，0:向下翻页
            'show_checked' => 'sometimes',//是否显示选择框
            'contract_id' => 'sometimes',//是否显示选择框
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $contractId = isset($params['contract_id']) ? $params['contract_id'] : '';
        $result = UserApi::getCoupons($params['cursor_id'],$params['timestamp'],$params['past'] = 0, $contractId);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            $list = isset($res['coupons']) ? $res['coupons'] : [];
            $checked = isset($params['show_checked']) ? true : false;
//            $cursorId = !empty($list) ? $list[count($list)-1]['cursor_id'] : intval($params['cursor_id']);
            $html = OtherBuilder::toBuildCouponList($list , $checked);
//            $firstCursorId = !empty($list) ? $list[0]['cursor_id'] : 0;
            $data = [
                'html'=>$html,
                'first_cursor_id'=>0,
                'cursor_id'=>0
            ];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /**
     * 获取余额明细
     * @param Request $request
     * @return mixed
     */
    public function getCashBillList(Request $request)
    {
        $pattern = [
            'cursor_id' => 'required_if:past,1',
            'timestamp' => 'required_if:past,0',
            'past' => 'required|in:0,1',//1:向上翻页，0:向下翻页
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = UserApi::getCashBill($params['cursor_id'],$params['timestamp'],$params['past']);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            $list = $res['bills'];
            $cursorId = !empty($list) ? $list[count($list)-1]['cursor_id'] : intval($params['cursor_id']);
            $html = OtherBuilder::toBuildBillList($list, $cursorId);
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
     * 获取红包列表
     * @param Request $request
     * @return mixed
     */
    public function getBonusList(Request $request)
    {
        $pattern = [
            'cursor_id' => 'required_if:past,1',
            'timestamp' => 'required_if:past,0',
            'past' => 'required|in:0,1',//1:向上翻页，0:向下翻页
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = UserApi::getBonusList($params['cursor_id'],$params['timestamp'],$params['past']);
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            $list = $res['bonus_packages'];
            $cursorId = !empty($list) ? $list[count($list)-1]['cursor_id'] : intval($params['cursor_id']);
            $html = OtherBuilder::toBonusList($list, $cursorId);
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
     * 获取红包详情
     * @param Request $request
     * @return mixed
     */
    public function getBonusDetail(Request $request)
    {
        $pattern = [
            'id' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $result = UserApi::getBonusDetail($params['id']);
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            $heart = isset($data['heart']) ? $data['heart'] : [];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        }

        $code = isset($result['code']) ? $result['code'] : -1;
        $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
        return response()->json((new ApiResult($code, $desc, $result))->toJson());
    }


    /**
     * 投诉建议
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function feedback(Request $request)
    {
        $pattern = [
            'phone'             => 'required',
            'line'              => 'required',
            'dept_date'         => 'required',
            'reason_pick'       => 'sometimes',
            'reason_content'    => 'sometimes',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $result = UserApi::userComplaint($params['phone'], $params['line'], $params['dept_date'], $params['reason_pick'], $params['reason_content']);
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
     * 获取profile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(Request $request)
    {
        $result = UserApi::getProfile();
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data']['profile'];
            $heart = isset($res['heart']) ? $res['heart'] : [];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

}
