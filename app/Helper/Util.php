<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2/23/16
 * Time: 10:12 AM
 */

namespace App\Helper;


use App\Models\User;
use EasyWeChat\Support\Log;
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
        Redis::set(self::userCacheKey(), \GuzzleHttp\json_encode($data));
    }

    /**
     * 清空缓存的用户信息
     */
    public static function clearCacheUserInfo()
    {
        Redis::del(self::userCacheKey());
        $uid = self::getUid();
        Redis::del(self::userCacheTokenKey($uid));
    }

    /**
     * 获取用户 信息
     * @return mixed
     */
    public static function getUserInfo()
    {
//        $account = session('account_info'); // 拿到微信授权用户资料
        $account = Redis::get(self::userCacheKey()); // 拿到微信授权用户资料
        return $account ? \GuzzleHttp\json_decode($account, true) : false;
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

        if (empty($res)) {
            $uid = self::getUid();
            $res = Redis::get(self::userCacheTokenKey($uid));
        }
        
//        $res = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXBpLmhvbGxvLmNuL2F1dGgvdjIvYWNjZXNzX3Rva2VuL3B3ZCIsImlhdCI6MTQ5OTY1NTAxNiwiZXhwIjoxNTAyMjQ3MDE2LCJuYmYiOjE0OTk2NTUwMTYsImp0aSI6IjNITmhOS1I2QTkzZ3FJcHoiLCJzdWIiOiIxMmVlOTQ1YTA3M2IxMWU1OWZiMTAwMTYzZTAwM2FkYiJ9.S_0j_u-H_gPSrvGbl9SH2RnQQHBG5WUtCQuMqpeEAkc';
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
            $res = $account['uid'];
        }

        if (empty($res)) {
            $openId = self::getOpenId();
            $res = Redis::get(self::userCacheUidKey($openId));
        }

        if (empty($res)) {
            //数据库获取uid
            $openId = self::getOpenId();
            $user = User::where('open_id', $openId)
                ->orderBy('id', -1)
                ->first();
            if (!is_null($user)) {
                $res = $user->uid;
                Redis::set(self::userCacheUidKey($openId), $res);
                Log::info('数据库获取token');
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
} 