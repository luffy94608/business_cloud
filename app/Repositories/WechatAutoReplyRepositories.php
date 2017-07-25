<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  




use App\Models\WechatAutoReply;

class WechatAutoReplyRepositories
{
    /**
     * 获取所有的自动回复
     * @return array
     */
    public static function replyMsgMap()
    {
        $msg = WechatAutoReply::where('enable',1)
            ->get();
        $result = $msg->all();
        return $result;
    }

    /**
     * 获取默认回复
     * @param $list
     * @return string
     */
    public static function getDefaultReplyMsg($list)
    {
        $msg =  '';
        return $msg;
    }


}