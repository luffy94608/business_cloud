<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 17/11/2016
 * Time: 16:44
 */

namespace App\Helper;


class IDVerifier
{

    public static function checkIdCard($idCard)
    {
        // 只能是18位
        if (strlen($idCard) != 18) {
            return false;
        }

        // 取出本体码
        $idCardBase = substr($idCard, 0, 17);

        // 取出校验码
        $verifyCode = substr($idCard, 17, 1);

        // 加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

        // 校验码对应值
        $verifyCodeList = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

        // 根据前17位计算校验码
        $total = 0;
        for ($i = 0; $i < 17; $i++) {
            $total += substr($idCardBase, $i, 1) * $factor[$i];
        }

        // 取模
        $mod = $total % 11;

        // 比较校验码
        if ($verifyCode == $verifyCodeList[$mod]) {
            return true;
        }

        return false;
    }

    public static function verify($realName, $idCard)
    {
        $data = [
            'code' => 0,
            'msg' => ''
        ];

        $url = 'http://op.juhe.cn/idcard/query';
        $params = [
            'key' => env('JUHE_KEY'),
            'idcard' => $idCard,
            'realname' => $realName
        ];

        $res = app('http_client')->get($url, $params);
        if ($res['error_code'] == 0) {
            switch ($res['result']['res']) {
                case 1:
                    $data['code'] = 0;
                    $data['msg'] = '认证通过';
                    break;
                case 2:
                    $data['code'] = -2;
                    $data['msg'] = '姓名和身份证不匹配';
                    break;
                default:
                    break;
            }
        } else {
            $data['code'] = -3;
//            $data['msg'] = $res['reason'] ?: '未知错误';
            $data['msg'] = '姓名和身份证不匹配';
        }

        return $data;
    }
}