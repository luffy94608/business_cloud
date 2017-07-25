@extends('layouts.default')
@section('title', '重置密码')
@section('bodyBg', 'bg-city')
@section('content')
    {{--<canvas id="canvas" style="position: fixed;top: 0;left: 0;width: 100%;height: 200px;"></canvas>--}}
    <main id="page" class="relative">             
        <div class="wrap">
            <div class="text-center">
                <img src="/images/logo-transparent@2x.png" class="logo-pic">
            </div>
            <div class="login-form pt-5 pb-5">
                <div class="lf-item display-flex">
                    <i class="icon-user"></i>
                    <input type="tel" class="box-flex-1" id="js_ar_mobile"  placeholder="请输入您的手机号" value="{{ \Illuminate\Support\Facades\Cookie::get('user_mobile') }}">
                </div>
                <div class="lf-item display-flex">
                    <i class="icon-code"></i>
                    <input type="number" id="js_ar_code" class="box-flex-1" placeholder="请输入验证码" value="">
                    <div class="verify-code">获取验证码</div>
                </div>
                <div class="lf-item display-flex">
                    <i class="icon-psw"></i>
                    <input type="password" id="js_ar_password" class="box-flex-1"  placeholder="请输入登录密码" value="">
                </div>
            </div>
            {{--<p class="text-right pt-10 pb-10 color-hint">--}}
                {{--<a href="/auth/reset?v=1" class="color-white">忘记密码?</a>--}}
            {{--</p>--}}
            <button class="btn btn-stress text-center  full-width font-16 mt-20" id="js_reset_btn">确定</button>

            <p class="text-center lf-fixed-btn">
                <a href="/auth/psw" class="color-white" >使用密码登录</a>
            </p>
        </div>

    </main>
@stop
