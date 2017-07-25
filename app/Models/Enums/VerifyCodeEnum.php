<?php

namespace App\Models\Enums;


class VerifyCodeEnum
{

    const Verify_Code_Register          = 0;
    const Verify_Code_Login             = 1;
    const Verify_Code_Reset             = 2;
    const Verify_Code_Bind              = 3;



    public static function transform($key)
    {
        $transformMap = array(
            self::Verify_Code_Register              => "注册",
            self::Verify_Code_Login                 => "登录",
            self::Verify_Code_Reset                 => "密码重置",
            self::Verify_Code_Bind                  => "绑定手机",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
