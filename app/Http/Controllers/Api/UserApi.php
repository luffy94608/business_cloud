<?php

namespace App\Http\Controllers\Api;

use App\Helper\Util;
use App\Models\Enums\HttpURLEnum;
use App\Models\Enums\VerifyCodeEnum;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class UserApi extends BaseApi
{

    /**
     * 获取手机验证码
     * @param $mobile
     * @return mixed
     */
    public static function getVerifyCode($mobile)
    {
        $url = 'https://dx.ipyy.net/smsJson.aspx';
        $account = 'AC00465';
        $psw = 'AC0046500';
        $psw = strtoupper(md5($psw));
        $code = mt_rand(1000, 9999);
        $content = sprintf('您的验证码：%s【商情云】', $code);
        $params = [
            'account'=>$account,
            'password'=>$psw,
            'mobile'=>intval($mobile),
            'content'=>($content),
            'action'=>'send',
            'userid'=>'',
            'sendTime'=>'',
            'extno'=>'',
        ];
        $result = Curl::to($url)
            ->withData( $params )
            ->asJsonResponse( true )
            ->post();

        Log::info($params);
        Log::info($result);
        $result = $code;
        Util::setVerifyCode($code);
        return $result;
    }

    /**
     * 注册
     * @param $openId
     * @param $mobile
     * @param $psw
     * @param $verify_code
     * @param int $type
     * @return mixed
     */
    public static function register($openId,$mobile, $psw, $verify_code, $type = VerifyCodeEnum::Verify_Code_Register)
    {
        $url = HttpURLEnum::User_Register;
        $params = [
            'open_id'=>$openId,
            'phone'=>$mobile,
            'password'=>$psw,
            'verify_code'=>$verify_code,
            'type'=>(int)$type
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 验证码登录
     * @param $mobile
     * @param $code
     * @param $type  //Int,1,注册码类型0-注册, 1-登录, 2-密码重置, 3-绑定手机
     * @return mixed
     */
    public static function loginCode($mobile, $code, $type = VerifyCodeEnum::Verify_Code_Login)
    {
        $url = HttpURLEnum::User_Login_Code;
        $params = [
            'phone' => $mobile,
            'verify_code' => $code,
            'type' =>(int)$type
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }


    /**
     * 退出
     * @return mixed
     */
    public static function logout()
    {
        $url = HttpURLEnum::User_Logout;
        $params = [
        ];
        $result = self::getRequestData($url, $params);
        return $result;
    }

    /**
     * 密码登录
     * @param $mobile
     * @param $psw
     * @return mixed
     */
    public static function loginPSW($mobile, $psw)
    {
        $url = HttpURLEnum::User_Login_PSW;
        $params = [
            'phone'=>$mobile,
            'password'=>md5($psw),
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 忘记密码
     * @param $phone
     * @param $psw
     * @param $code
     * @return mixed
     */
    public static function reset($phone, $psw, $code)
    {
        $url = HttpURLEnum::User_Reset_PSW;
        $params = [
            'phone'=>($phone),
            'password'=>md5($psw),
            'verify_code'=>($code),
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 修改密码
     * @param $new_psw
     * @param $psw
     * @return mixed
     */
    public static function editPsw($psw, $new_psw)
    {
        $url = HttpURLEnum::User_Edit_PSW;
        $params = [
            'password'=>md5($psw),
            'new_password'=>md5($new_psw)
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 获取用户详细信息
     * @return mixed
     */
    public static function getProfile()
    {
        $url = HttpURLEnum::User_Profile;
        $params = [
        ];
        $result = self::getRequestData($url, $params);
        return $result;
    }

    /**
     * 钱包概览g
     * @return mixed
     */
    public static function getSummary()
    {
        $url = HttpURLEnum::User_Summary;
        $params = [
            'timestamp'=>Carbon::now()->timestamp
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 余额明细
     * @param int $cursorId
     * @param int $timeStamp
     * @param int $past 1:向上翻页，0:向下翻页
     * @return mixed
     */
    public static function getCashBill($cursorId=0,$timeStamp=0,$past=0)
    {
        $url = HttpURLEnum::User_Cash_Bill_List;
        $params = [
            'cursor_id'=>intval($cursorId),
            'is_next'=>intval($past),
            'timestamp'=>intval($timeStamp) ? intval($timeStamp) : 0
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 优惠券
     * @param int $cursorId
     * @param int $timeStamp
     * @param int $past
     * @param string $contractId
     * @return mixed
     */
    public static function getCoupons($cursorId=0, $timeStamp=0, $past=0, $contractId = '')
    {
        $url = HttpURLEnum::User_Coupons_List;
        $params = [
            'cursor_id'=>intval($cursorId),
            'is_next'=>intval($past),
            'timestamp'=>intval($timeStamp) ? intval($timeStamp) : 0
        ];
        if (!empty($contractId)) {
            $params['contract_id'] = $contractId;
        }
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 优惠券兑换
     * @param string $code
     * @return mixed
     */
    public static function exchangeCode($code)
    {
        $url = HttpURLEnum::User_Exchange_Coupon_Code;
        $params = [
            'code'=>$code
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     *  修改个人信息
     * @param $name
     * @return mixed
     */
    public static function updateProfile($name)
    {

        $url = HttpURLEnum::User_Update_Profile;
        $params = [
            'nickname'=>$name,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 用户投诉
     * @param $mobile
     * @param $line
     * @param $deptDate
     * @param $reasonPick
     * @param $reasonContent
     * @return mixed
     */
    public static function userComplaint($mobile, $line, $deptDate, $reasonPick, $reasonContent)
    {

        $url = HttpURLEnum::User_Feedback;
        $params = [
            'line'=> $line,
            'dept_date'=> $deptDate,
            'phone'=> $mobile,
            'reason_pick'=> $reasonPick ?: [],
            'reason_content'=> $reasonContent,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 红包列表
     * @param int $cursorId
     * @param int $timeStamp
     * @param int $past 1:向上翻页，0:向下翻页
     * @return mixed
     */
    public static function getBonusList($cursorId=0,$timeStamp=0,$past=0)
    {
        $url = HttpURLEnum::User_Bonus_List;
        $params = [
            'cursor_id'=>intval($cursorId),
            'is_next'=>intval($past),
            'timestamp'=>intval($timeStamp) ? intval($timeStamp) : 0
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

    /**
     * 红包
     * @param  $bonusId
     * @return mixed
     */
    public static function getBonusDetail($bonusId)
    {
        $url = HttpURLEnum::User_Bonus_Detail;
        $params = [
            'bonus_id'=>$bonusId,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }
}
