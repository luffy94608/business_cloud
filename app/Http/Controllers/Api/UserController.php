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
    public function register(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required|digits:11',
            'code' => 'required',
            'password' => 'required',
        ]);

        $params = $request->only(
            'mobile', 'code', 'password'
        );

        $result = UserApi::register($this->openId,$params['mobile'], $params['password'], $params['code']);
        if ($result['code'] === 0) {
            $data = $result['data'];
            $cookie = Cookie::forever('user_mobile', $params['mobile']);
            $cookie2 = Cookie::forever('user_psw', isset($params['password']) ? $params['password'] : '');
            $this->saveLoginData($this->openId, $data);

            $result = UserApi::reset($params['password']);
            if (isset($result['code']) && $result['code'] === 0) {

                $data = [
                    'url'=>$this->getReferUrl()
                ];
                $heart = isset($data['heart']) ? $data['heart'] : [];
                return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data, $heart))->toJson())
                    ->withCookie($cookie)
                    ->withCookie($cookie2);
            }
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }


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
            $this->saveLoginData($data);
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
     * 退出
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        $pattern = [
        ];
        $this->validate($request, $pattern);
        Util::clearCacheUserInfo();
        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), [], []))->toJson());
    }


    /**
     * 保存登录成功的数据
     * @param $data
     */
    private function saveLoginData($data)
    {
        Util::setCacheUserInfo($data);
        $this->refreshInitInfo();
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
