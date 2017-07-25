@extends('layouts.default')
@section('title', '注册')
@section('bodyBg', 'bg-primary')
@section('content')
    <main id="page">
        <div class="wrap">
            
            <div class="login-form pt-5 pb-5 mt-100">
                <div class="lf-item display-flex">
                    <svg class="icon-svg icon-phone">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/images/icons.svg#icon-phone"></use>
                    </svg>
                    <input type="tel" class="box-flex-1" id="js_ar_mobile"  placeholder="请输入您的手机号" value="">
                </div>
                <div class="lf-item display-flex">
                    {{--<i class="a-icon-edit"></i>--}}
                    <svg class="icon-svg icon-edit">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/images/icons.svg#icon-edit"></use>
                    </svg>
                    <input type="number" id="js_ar_code" class="box-flex-1" placeholder="请输入验证码" value="">
                    <div class="verify-code">获取验证码</div>
                </div>
                <div class="lf-item display-flex">
                    <svg class="icon-svg icon-lock">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/images/icons.svg#icon-lock"></use>
                    </svg>
                    <input type="password" id="js_ar_password" class="box-flex-1"  placeholder="请输入登录密码" value="">
                </div>
            </div>
            <p class="text-right pt-10 pb-10 color-hint">
                <a href="/auth/reset?v=1" class="color-white">忘记密码?</a>
            </p>
            <button class="btn-stress text-center  full-width " id="js_register_btn">注册</button>
        </div>

    </main>

@stop
