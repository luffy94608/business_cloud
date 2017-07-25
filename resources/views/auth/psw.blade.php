@extends('layouts.default')
@section('title', '登录')
@section('bodyBg', 'bg-city')

@section('content')
<div>
    {{--<canvas id="canvas" style="position: fixed;top: 0;left: 0;width: 100%;height: 200px;"></canvas>--}}
    <main id="page" class="relative">
        <div class="wrap">
            <div class="text-center">
                <img src="/images/logo-transparent@2x.png" class="logo-pic">
            </div>
            <div class="login-form pt-5 pb-5 ">
                <div class="lf-item display-flex">
                    <i class="icon-user"></i>
                    <input type="tel" id="js_ar_mobile" class="box-flex-1"  placeholder="请输入您的手机号" value="{{ \Illuminate\Support\Facades\Cookie::get('user_mobile') }}">
                </div>
                <div class="lf-item display-flex">
                    <i class="icon-psw"></i>
                    <input type="password" id="js_ar_password" class="box-flex-1"  placeholder="请输入密码" value="{{ \Illuminate\Support\Facades\Cookie::get('user_psw') }}">
                </div>
            </div>

            <button class="btn btn-stress text-center  full-width font-16 mt-20" id="js_login_btn">登录</button>
            <p class="text-right pt-20 pb-10 color-hint">
                {{--<a href="/auth/login" class="color-white fl" >验证码登录</a>--}}
                <a href="/auth/reset" data-replace="false" class="color-white js_location_url">忘记密码?</a>
            </p>
            
            <p class="text-center lf-fixed-btn">
                <a href="/auth/login" data-replace="true" class="color-white js_location_url"  >验证码登录</a>
            </p>
        </div>
    </main>
</div>

@stop
