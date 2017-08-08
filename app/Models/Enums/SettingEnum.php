<?php

namespace App\Models\Enums;


use Illuminate\Support\Facades\Config;

class SettingEnum
{

    const Prefix                            = 'https://m.hollo.cn/v3';

    /**
     * 不同项目显示配置
     */
    const Ticket_Policy_URL         = 1;
    const Refund_Policy_URL         = 2;
    const User_Policy_URL           = 3;
    const User_Policy_Status        = 4;
    const App_Name                  = 101;
    const App_Name_Simple           = 102;
    const Pay_Success_Msg_Template_id           = 103;

    public static function transform($key)
    {
        $transformMap = [];
        $appName = strtolower(Config::get('app')['name']);
        switch ($appName) {
            case 'hollo':
                $transformMap = array(
                    self::App_Name                                => '哈罗同行',
                    self::App_Name_Simple                         => '哈罗',
                    self::Ticket_Policy_URL                       => self::Prefix.'/activity/monthly_ticket.html',
                    self::Refund_Policy_URL                       => self::Prefix.'/notice/ticket_refund.html',
                    self::User_Policy_URL                         => self::Prefix.'/hgt/compact.html',
                    self::User_Policy_Status                      => 'gone',
                    self::Pay_Success_Msg_Template_id             => 'SlkTsBCelqM3jnnD-q7MQ7OwFfXsTLl1NS6OB-s-UIE',
                );
                break;

            case 'hgt':
                $transformMap = array(
                    self::App_Name                                => '智享出行',
                    self::App_Name_Simple                         => '智享',
                    self::Ticket_Policy_URL                       => self::Prefix.'/activity/hgt_monthly_ticket.html',
                    self::Refund_Policy_URL                       => self::Prefix.'/notice/hgt_ticket_refund.html',
                    self::User_Policy_URL                         => self::Prefix.'/hgt/compact.html',
                    self::User_Policy_Status                      => '',
                    self::Pay_Success_Msg_Template_id             => 'QShQGHhHUm8nLWpZlrKvAFS1FhbYUUrJ3L849OQULho',
                );
                break;
        }

        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
