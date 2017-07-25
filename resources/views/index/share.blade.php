@extends('layouts.default')
@section('title', '机场巴士')
@section('bodyBg', 'share')

{{--内容区域--}}
@section('content')
    <div>
        @include('layouts.header',['title'=>'拉新有奖','leftBtnType'=>1])
        <main id="pages" class="page-wrap">
            <div class="page ">
                <div class="ar-share">
                    <img class="arh-title " src="/images/share/share_text.png">
                    <img class="arh-sub-title " src="/images/share/share_sub_text.png">
                    <img class="arh-bonus" src="/images/share/bonus.png">
                    <div class="arh-intro" data-title="活动简介">
                        <p>送给好友1元乘车优惠，您即可立即<span class="color-red">领取5元优惠券</span>的奖励（此奖励只能领取一次）。</p>
                        <p>好友通过分享页面，注册成功并且进入应用后，您将再获得<span class="color-red">1元优惠券</span>奖励。</p>
                        <p>好友首次下单并完成支付后，您还可再获得<span class="color-red">10元优惠券</span>奖励。</p>
                        <p>邀请越多好友，奖励越多。</p>
                    </div>
                    <button class="text-center btn-primary arh-btn" id="js_share_btn">马上参与</button>
                </div>
                <img class="arh-cloud" src="/images/share/cloud.png">
                <input type="hidden" class="js_open_id" value="{{ $openId }}">
                <input type="hidden" class="js_user_id" value="{{ $uid }}">
            </div>
        </main>
    </div>
@stop

