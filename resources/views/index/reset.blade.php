@extends('layouts.default')
@section('title', '忘记密码')

{{--内容区域--}}
@section('content')
    {{--content--}}
    <div class="container-fluid">
        <div class="container ">
            <div class="row  mt-100 pb-100  bg-white box-shadow-1 bc-form-section">
                <div class="bc-section-title color-with-body">重置密码</div>
                <div class="col-xs-12 col-sm-10 col-sm-offset-1 form-horizontal mt-100">
                    {{--帐号相关--}}
                    {{--<div class="bc-form-title-section">--}}
                        {{--<div class="title">帐号密码</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label for="js_input_mobile" class="col-sm-2 col-xs-3 control-label">手机号</label>
                        <div class="col-sm-5 col-xs-7">
                            <input type="email" name="mobile" class="form-control" id="js_input_mobile" placeholder="请输入手机号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_code" class="col-sm-2 col-xs-3 control-label">验证码</label>
                        <div class="col-sm-3 col-xs-4">
                            <input type="text" name="code" class="form-control" id="js_input_code" placeholder="请输入验证码">
                        </div>
                        <div class="col-sm-2 col-xs-2">
                            <button type="button" class="btn btn-default" id="js_get_code_btn">获取验证码</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_psw" class="col-sm-2 col-xs-3 control-label">新密码</label>
                        <div class="col-sm-5 col-xs-7">
                            <input type="password" name="psw" class="form-control" id="js_input_psw" placeholder="请输入新密码">
                        </div>
                    </div>

                    {{--提交按钮--}}
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-4 col-xs-12">
                            <button type="submit" class="btn btn-success form-control" id="js_input_submit">提交</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('layouts.footer', ['style'=>'white'])
@stop


