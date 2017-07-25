<?php

namespace App\Models\Enums;


use EasyWeChat\Message\News;
use Illuminate\Support\Facades\Config;

class WechatClickEnum
{

    /**
     * 文字消息
     */
    const Event_Click_Key_Subscribe                               = 'subscribe_feedback_click';//关注
    const Event_Click_Key_Feedback                                = 'default_reply_click'; //默认回复

    

    /**
     * @param $key
     * @return mixed|string
     */
    public static function transform($key)
    {
        $host = Config::get('app')['url'];

        $transformMap = array(
        );



        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
