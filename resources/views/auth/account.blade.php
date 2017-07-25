@extends('layouts.default')
@section('title', '个人中心')
@section('content')
<main id="page" >
    <div class="account-header">
        <div class="ac-body ">
            <img class="js_profile_img" src="{{ !empty($profile['user']['avatar']) ? Config::get('app')['upyun_host'].$profile['user']['avatar']  : '/images/avatar.png' }}">
            <div class="name ">
                <input type="text" class="edit-name gone" id="js_input_name" disabled data-src="{{ isset($profile['user']['name']) ? $profile['user']['name']  : '未知' }}" value="{{ isset($profile['user']['name']) ? $profile['user']['name']  : '未知' }}">
                <span class="name-title"><span class="js_profile_name">{{ isset($profile['user']['name']) ? $profile['user']['name']  : '未知' }}</span></span>
                <i class="icon-edit"></i>
                <span class="save-name" id="js_submit">保存</span>
            </div>
            <span class="count  ">本月乘车次数：<span class="js_profile_ticket_count">{{ isset($profile['contract_count']) ? $profile['contract_count']: '0' }}</span></span>
        </div>
    </div>
    <div class="line-list line-list--indent line-list--after-v mt-10">
        <a class="line-item color-title animated pulse ant-delay-1" href="/auth/cash">
            {{ \App\Models\Enums\SettingEnum::transform(\App\Models\Enums\SettingEnum::App_Name_Simple) }}币
            <span class="fr color-orange"><span class="js_profile_cash">{{ isset($profile['balance']) ? $profile['balance']: '0' }}</span>元</span>
        </a>
        <a class="line-item color-title animated pulse ant-delay-2" href="/auth/coupons">
            优惠券
            <span class="fr color-orange"><span class="js_profile_coupon">{{ isset($profile['coupon_count']) ? $profile['coupon_count']: '0' }}</span>张</span>
        </a>
        <a class="line-item color-title animated pulse ant-delay-3" href="/auth/bonus">
            红包
            {{--<span class="fr color-orange">{{ isset($profile['coupon_count']) ? $profile['coupon_count']: '0' }}张</span>--}}
        </a>
    </div>

    <div class="line-list line-list--indent animated pulse ant-delay-4">
        <a class="line-item color-title" href="javascript:void(0);" id="js_logout">退出当前帐号</a>
    </div>
</main>

@stop
