@extends('layouts.default')
@section('title', '登录')
@section('bodyBg', 'bg-city')

@section('content')
<div>
    {{--<canvas id="canvas" style="position: fixed;top: 0;left: 0;width: 100%;height: 200px;"></canvas>--}}
    <main id="page " class="relative" >
        <div class="wrap">
            <div class="text-center">
                <img src="/images/logo-transparent@2x.png" class="logo-pic">
            </div>
            <div class="login-form pt-5 pb-5">
                <div class="lf-item display-flex">
                    <i class="icon-user"></i>
                    <input type="tel" class="box-flex-1" id="js_ar_mobile"  placeholder="手机号(未注册将自动创建账号)" value="{{ \Illuminate\Support\Facades\Cookie::get('user_mobile') }}">
                </div>
                <div class="lf-item display-flex">
                    <i class="icon-code"></i>
                    <input type="number" id="js_ar_code" class="box-flex-1" placeholder="验证码" value="">
                    <div class="verify-code">获取验证码</div>
                </div>
            </div>
            <p class="agree mt-35 font-13 {{ \App\Models\Enums\SettingEnum::transform(\App\Models\Enums\SettingEnum::User_Policy_Status) }}">
                <input id="read_btn" type="checkbox" checked  class="read-checkbox yellow" value="1">
                <label for="read_btn" class="color-white">我已阅读并同意</label>
                <a href="{{ \App\Models\Enums\SettingEnum::transform(\App\Models\Enums\SettingEnum::User_Policy_URL) }}" class="color-yellow">《用户协议及隐私政策》</a>
            </p>
            <button class="btn btn-stress text-center  full-width font-16 mt-20" id="js_login_btn">登录</button>
            {{--<p class="text-right pt-20 pb-10 color-hint">--}}
                {{--<a href="/auth/psw" class="color-white">密码登录</a>--}}
            {{--</p>--}}

            <p class="text-center lf-fixed-btn">
                <a href="/auth/psw" data-replace="true" class="color-white js_location_url" >使用密码登录</a>
            </p>
        </div>
    </main>
</div>

@stop
