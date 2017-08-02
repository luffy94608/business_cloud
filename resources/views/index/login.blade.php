@extends('layouts.default')
@section('title', '登录')
@section('bodyBg', 'bg-login')

{{--内容区域--}}
@section('content')
    {{--header--}}
    @include('layouts.header', ['hideContent'=>true, 'bgStyle'=>'bg-transparent bc-login-screen'])
    {{--content--}}
    <div class="bc-login-wrap">
        <div class="container-fluid">
            <div class="container ">
                <div class="row">
                    <div class="col-xs-12 bcl-title">
                        <span>数据连接市场</span><span>挖掘数据价值</span>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 form-horizontal bc-login-section">
                        <div class="bc-login-mask"></div>
                        <div class="bc-login-content">
                            <div class="bcl-item">
                                <span class="b-icon-user"></span>
                                <input type="text" id="js_input_name"  placeholder="请输入用户名" >
                            </div>
                            <div class="bcl-item">
                                <span class="b-icon-psw" ></span>
                                <input type="text" id="js_input_psw"  placeholder="请输入密码" >
                            </div>
                            <button type="submit" class="btn btn-success form-control">确定</button>

                            <div class="bcl-footer">
                                <a href="/register">立即注册</a>|
                                <a href="/reset">忘记密码</a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        @include('layouts.footer', ['style'=>'fixed-bottom bc-login-screen'])
    </div>
@stop


