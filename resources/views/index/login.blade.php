@extends('layouts.default')
@section('title', '登录')
@section('bodyBg', 'bg-login')

{{--内容区域--}}
@section('content')
    {{--header--}}
    @include('layouts.header', ['hideContent'=>true])
    {{--content--}}
    <div class="container-fluid">
        <div class="container ">
            <div class="row  mt-100 pb-100  bc-form-section">
                <div class="col-xs-12 col-sm-6 col-sm-offset-3 form-horizontal mt-100 bg-white box-shadow-1 ">
                    
                    <div class="form-group">
                        <label for="js_input_mobile" class="col-sm-2 col-xs-3 control-label">手机号</label>
                        <div class="col-sm-5 col-xs-7">
                            <input type="email" name="mobile" class="form-control" id="js_input_mobile" placeholder="请输入手机号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_psw" class="col-sm-2 col-xs-3 control-label">密码</label>
                        <div class="col-sm-5 col-xs-7">
                            <input type="password" name="psw" class="form-control" id="js_input_psw" placeholder="请输入密码">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_code" class="col-sm-2 col-xs-3 control-label">验证码</label>
                        <div class="col-sm-3 col-xs-4">
                            <input type="text" name="code" class="form-control" id="js_input_code" placeholder="请输入验证码">
                        </div>
                        <div class="col-sm-2 col-xs-2">
                            <button type="button" class="btn btn-default">获取验证码</button>
                        </div>
                    </div>


                    {{--提交按钮--}}
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success">确定</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('layouts.footer', ['style'=>'white'])
@stop


