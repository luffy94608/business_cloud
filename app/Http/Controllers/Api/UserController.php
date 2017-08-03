<?php

namespace App\Http\Controllers\Api;

use App\Helper\Util;
use App\Http\Builders\OtherBuilder;
use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use App\Models\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepositories;
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
//            'type' => 'required|in:0,1,2,3',//注册码类型0-注册, 1-登录, 2-密码重置, 3-绑定手机
        ]);

        $params = $request->only(
            'mobile'
        );

        $result = UserApi::getVerifyCode($params['mobile']);
        if (!empty($result)) {
            $data = $result;
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
        $pattern = [
            'mobile' => 'required|digits:11',
            'psw' => 'required',
            'code' => 'required',

            'name' => 'required',
            'gender' => 'required',
            'job' => 'required',
            'email' => 'required',
            'company_name' => 'required',
            'company_area' => 'required',
            'company_industry' => 'required',
            'follow_area' => 'required',
            'follow_industry' => 'required',
            'follow_keyword' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        if ($params['code'] != Util::getVerifyCode()) {
            $desc = '验证码不正确';
            return response()->json((new ApiResult(-1, $desc, []))->toJson());
        }
        if (UserRepositories::mobileIsExist($params['mobile'])) {
            $desc = '手机号已存在';
            return response()->json((new ApiResult(-1, $desc, []))->toJson());
        }
        

        $user = [
            'username' => $params['mobile'],
            'password' => md5($params['psw']),
            'pwd' => $params['psw'],
            'verified' => 1,
            'paid' => 1,
        ];
        $userId = UserRepositories::insertUser($user);
        $profile = [
            'user_id' => $userId,
            'name' => $params['name'],
            'gender' => $params['gender'],
            'position' => $params['job'],
            'mail' => $params['email'],
            'company_name' => $params['company_name'],
            'company_area' => $params['company_area'],
            'company_industry' => $params['company_industry'],
            'follow_area' => $params['follow_area'],
            'follow_industry' => $params['follow_industry'],
            'follow_keyword' => $params['follow_keyword'],
        ];
        $result = UserRepositories::insertProfile($profile);

        if ($result) {
            $user['id'] = $userId;
//            $account = array_merge($user, $profile);
            $cookie = Cookie::forever('user_mobile', $params['mobile']);
            $cookie2 = Cookie::forever('user_psw', isset($params['psw']) ? $params['psw'] : '');
//            $this->saveLoginData($account);
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), [], []))->toJson())
                ->withCookie($cookie)
                ->withCookie($cookie2);
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
        $pattern = [
            'mobile' => 'required|digits:11',
            'psw' => 'required',
            'code' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        if ($params['code'] != Util::getVerifyCode()) {
            $desc = '验证码不正确';
            return response()->json((new ApiResult(-1, $desc, []))->toJson());
        }
        if (!UserRepositories::mobileIsExist($params['mobile'])) {
            $desc = '手机号不存在';
            return response()->json((new ApiResult(-1, $desc, []))->toJson());
        }

        $user = UserRepositories::getUserByMobile($params['mobile']);

        $data = [
            'username' => $params['mobile'],
            'password' => md5($params['psw']),
            'pwd' => $params['psw'],
        ];
        $result = UserRepositories::updateUser($user, $data);

        if ($result) {
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), [], []))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
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
            'psw'  => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        
        if (!UserRepositories::mobileIsExist($params['mobile'])) {
            $desc = '帐号不存在';
            return response()->json((new ApiResult(-1, $desc, []))->toJson());
        }
        $user = UserRepositories::login($params['mobile'], $params['psw']);
        if (is_null($user)) {
            $desc = '帐号或密码错误';
            return response()->json((new ApiResult(-1, $desc, []))->toJson());
        }
        $profile = $user->profile;

        $account = array_merge($user->toArray(), $profile->toArray());
        $cookie = Cookie::forever('user_mobile', $params['mobile']);
        $cookie2 = Cookie::forever('user_psw', isset($params['psw']) ? $params['psw'] : '');
        $this->saveLoginData($account);
        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), [], []))->toJson())
            ->withCookie($cookie)
            ->withCookie($cookie2);
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
            'company_name' => 'required',
            'company_area' => 'required',
            'company_industry' => 'required',
            'follow_area' => 'required',
            'follow_industry' => 'required',
            'follow_keyword' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));

        $user = UserRepositories::getProfile($this->uid, true);

        $data = [
            'company_name' => $params['company_name'],
            'company_area' => $params['company_area'],
            'company_industry' => $params['company_industry'],
            'follow_area' => $params['follow_area'],
            'follow_industry' => $params['follow_industry'],
            'follow_keyword' => $params['follow_keyword'],
        ];
        $result = UserRepositories::updateProfile($user, $data);

        if ($result) {
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), [], []))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
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
