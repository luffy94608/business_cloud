<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2/23/16
 * Time: 10:12 AM
 */

namespace App\Helper;


use App\Models\User;
use App\Repositories\SettingRepositories;
use Carbon\Carbon;
use EasyWeChat\Support\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class Util
{

    private static $userKey = 'user.info.';
    private static $userTokenKey = 'i_u_token_';
    private static $userUidKey = 'i_u_uid_';

    /**
     * 获取用户缓存key
     * @return string
     */
    public static function userCacheKey()
    {
        return self::$userKey.self::getOpenId();
    }

    /**
     * 获取用户缓存token key
     * @param $uid
     * @return string
     */
    public static function userCacheTokenKey($uid)
    {
        return self::$userTokenKey.$uid;
    }

    /**
     * 获取用户缓存token key
     * @param $openId
     * @return string
     */
    public static function userCacheUidKey($openId)
    {
        return self::$userUidKey.$openId;
    }

    /**
     * 设置缓存的用户信息
     * @param $data
     */
    public static function setCacheUserInfo($data)
    {
        Session::put('account_info', $data);
//        Redis::set(self::userCacheKey(), \GuzzleHttp\json_encode($data));
    }

    /**
     * 设置用户验证码
     * @param $code
     */
    public static function setVerifyCode($code)
    {
        Session::put('user_verify_code', $code);
//        Redis::set(self::userCacheKey(), \GuzzleHttp\json_encode($data));
    }

    /**
     * 获取用户验证码
     * @return mixed
     */
    public static function getVerifyCode()
    {
       return  Session::get('user_verify_code');
//        Redis::set(self::userCacheKey(), \GuzzleHttp\json_encode($data));
    }

    /**
     * 清空缓存的用户信息
     */
    public static function clearCacheUserInfo()
    {
        Session::forget('account_info');
//        Redis::del(self::userCacheKey());
//        $uid = self::getUid();
//        Redis::del(self::userCacheTokenKey($uid));
    }

    /**
     * 获取用户 信息
     * @return mixed
     */
    public static function getUserInfo()
    {
        $account = session('account_info'); // 拿到微信授权用户资料
//        $account = Redis::get(self::userCacheKey()); // 拿到微信授权用户资料
        return $account ? $account : false;
    }

    /**
     * 获取用户token
     * @return string
     */
    public static function getUserToken()
    {
        $res = '';
        $account = self::getUserInfo(); // 拿到微信授权用户资料
        if(!empty($account)) {
            $res = $account['sid'];
        }
        return $res;
    }

    public static function getUserName()
    {
        $res = '';
        $account = self::getUserInfo(); // 拿到微信授权用户资料
        if(!empty($account)) {
            $res = $account['name'];
        }
        return $res;
    }

    public static function getUserMobile()
    {
        $res = '';
        $account = self::getUserInfo(); // 拿到微信授权用户资料
        if(!empty($account)) {
            $res = $account['username'];
        }
        return $res;
    }


    /**
     * 获取用户 uid
     * @return string
     */
    public static function getUid()
    {
        $res = '';
        $account = self::getUserInfo(); // 拿到微信授权用户资料
        if(!empty($account))
        {
            $res = $account['id'];
        }
        return $res;
    }

    /**
     * 关注区域id
     * @return string
     */
    public static function getFollowAreaId()
    {
        $res = '';
        $account = self::getUserInfo(); // 拿到微信授权用户资料
        if(!empty($account))
        {
            $res = explode(',', $account['follow_area']);
        }
        return $res;
    }

    /**
     * 关注行业id
     * @return string
     */
    public static function getFollowIndustryId()
    {
        $res = '';
        $account = self::getUserInfo(); // 拿到微信授权用户资料
        if(!empty($account))
        {
            $res = $account['follow_industry'];
        }
        return $res;
    }

    /**
     * 是否是付费用户
     * @return bool
     */
    public static function isVip()
    {
        $res = false;
        $account = self::getUserInfo(); // 拿到微信授权用户资料
        if(!empty($account))
        {
            $registerTime = strtotime($account['created_at']);
            $now = Carbon::now();
            $feeTime = SettingRepositories::freeVipSecond();

            if (!empty($account['paid']) ||  $registerTime + $feeTime > $now->timestamp) {
                $res = true;
            }
        }
        return $res;
    }


    /**
     * 获取用户 open_id
     * @return string
     */
    public static function getOpenId()
    {
        $res = '';
        $wechatUser = session('wechat.oauth_user'); // 拿到微信授权用户资料
        if($wechatUser)
        {
            $res = $wechatUser->id;
        }
        return $res;
    }

    /**
     * 获取微信用户 open_id
     * @return string
     */
    public static function getWechatUser()
    {
        $res = '';
        $wechatUser = session('wechat.oauth_user'); // 拿到微信授权用户资料
        if($wechatUser)
        {
            $res = $wechatUser;
        }
        return $res;
    }

    /**
     * 获取支付回调url
     * @return string
     */
    public static function getWechatPayNotifyUrl()
    {
        $url = '%s/api/pay/wxpay-notify';
        $host = Config::get('app')['url'];
        $url = sprintf($url, $host);
        return $url;
    }

    /**
     * 构照验票的数据结构
     * @param $ticketId
     * @param $timestamp
     * @return array
     */
    public static function toCheckTicketStructure($ticketId, $timestamp)
    {
        return [
            'ticket_id'=>$ticketId,
            'check_time'=>$timestamp,
        ];
    }

    /**
     * 菜单是否选中
     * @param $type
     * @return string
     */
    public static function headMenuActive($type)
    {
        $res = '';
        $map = [];
        switch ($type) {
            case 1:
                $map = ['bid-call', 'src=publish'];
                break;
            case 2:
                $map = ['bid-winner', 'src=bid'];
                break;
            case 3:
                $map = ['rival', 'src=competitor',  'rival-detail/'];
                break;
        }
        $path = \Request::getUri();
        foreach ($map as $v) {
            if (stripos($path , $v) !== false) {
                $res = 'active';
            }
        }
        return $res;
    }
} 