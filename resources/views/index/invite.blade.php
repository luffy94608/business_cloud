@extends('layouts.default')
@section('title', '机场巴士')
@section('bodyBg', 'bg-white')

{{--内容区域--}}
@section('content')
    <div class="ar-invite-section">
        <div class="ari-header">
            <img class="ari-title " src="/images/share/invite_text.png">
            <img class="ari-sub-title " src="/images/share/invite_sub_text.png">
            <img class="ari-cloud" src="/images/share/cloud.png">
        </div>
        <div class="ari-content">
            <div class="ari-info">
                <p class="cut-ellipsis-2">收到来自{{ $user['wechatUser'] ? $user['wechatUser']['nickname'] : '' }}的邀请</p>
                <p>立即领取<span class="color-red">{{ $price }}元优惠券</span></p>
            </div>
            <div class="ari-input">
                @if(!$status)
                    <input type="number" placeholder="请输入手机号" class="js_ari_mobile js_open_info">
                    <input type="hidden" class="js_ari_open_id"  value="{{ $user['open_id'] }}">
                    <input type="hidden" class="js_ari_uid"  value="{{ $user['uid'] }}">
                    <input type="hidden" class="js_ari_status"  value="{{ $status ? 1 : 0 }}">
                    <button class="text-center btn-primary ari-btn js_open_info" id="js_invite_submit" >立即领取</button>
                    <button class="text-center btn-primary ari-btn gone js_opened_info"   >已领取</button>
                @else
                    <button class="text-center btn-primary ari-btn  js_opened_info"   >已领取</button>
                @endif

            </div>
        </div>
    </div>
@stop
