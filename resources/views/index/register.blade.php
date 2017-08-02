@extends('layouts.default')
@section('title', '注册')

{{--内容区域--}}
@section('content')

    <div class="visible-xs">
        @include('layouts.header', ['hideContent'=>true, 'bgStyle'=>'bc-login-screen'])
    </div>
    {{--content--}}
    <div class="container-fluid">
        <div class="container">
            <div class="row  mt-20  bg-white box-shadow-1 bc-form-section">
                <div class="bc-section-title color-with-body hidden-xs">用户注册</div>
                <div class="col-xs-12 col-sm-10 col-sm-offset-1 form-horizontal">
                    {{--帐号相关--}}
                    <div class="bc-form-title-section">
                       <div class="title">帐号密码</div>
                    </div>
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

                    {{--个人信息--}}
                    <div class="bc-form-title-section">
                        <div class="title">个人信息</div>
                        <span class="color-hint">（必填）</span>
                    </div>
                    <div class="form-group">
                        <label for="js_input_name" class="col-sm-2 col-xs-3 control-label">姓名</label>
                        <div class="col-sm-5 col-xs-7">
                            <input type="email" class="form-control" id="js_input_name" placeholder="请输入姓名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_sex" class="col-sm-2 col-xs-3 control-label">性别</label>
                        <div class="col-sm-2 col-xs-7">
                            <select id="js_input_sex" class="form-control">
                                <option>男</option>
                                <option>女</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_job" class="col-sm-2 col-xs-3 control-label">职位</label>
                        <div class="col-sm-5 col-xs-7">
                            <input type="text" name="job" class="form-control" id="js_input_job" placeholder="请输入职位">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_email" class="col-sm-2 col-xs-3 control-label">邮箱</label>
                        <div class="col-sm-5 col-xs-7">
                            <input type="text" name="email" class="form-control" id="js_input_email" placeholder="请输入邮箱">
                        </div>
                    </div>

                    {{--企业信息--}}
                    <div class="bc-form-title-section">
                        <div class="title">企业信息</div>
                        <span class="color-hint">（必填）</span>
                    </div>
                    <div class="form-group">
                        <label for="js_input_company" class="col-sm-2 col-xs-3 control-label">名称</label>
                        <div class="col-sm-5 col-xs-7">
                            <input type="email" class="form-control" name="company" id="js_input_company" placeholder="请输入名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_location" class="col-sm-2 col-xs-3 control-label">所在地</label>
                        <div class="col-sm-2 col-xs-7">
                            <select id="js_input_location" class="form-control">
                                <option>北京市</option>
                                <option>上海市</option>
                                <option>重庆市</option>
                            </select>
                        </div>
                        <div class="col-xs-12 mt-15 visible-xs-block"></div>
                        <label for="js_input_industry" class="col-sm-1 col-xs-3 control-label">行业</label>
                        <div class="col-sm-2 col-xs-7">
                            <select id="js_input_industry" class="form-control">
                                <option>计算机</option>
                                <option>游戏</option>
                                <option>音乐</option>
                            </select>
                        </div>
                    </div>

                    
                    {{--关注信息--}}
                    <div class="bc-form-title-section">
                        <div class="title">关注信息</div>
                        <span class="color-hint">（必填）</span>
                    </div>
                    <div class="form-group">
                        <label for="js_input_area" class="col-sm-2 col-xs-3 control-label">地区</label>
                        <div class="col-sm-4 col-xs-7">
                            <div class="bc-keyword-section">
                                <span class="bck-item">房屋建筑<i class="b-icon-close ml-5"></i></span>
                                <span class="bck-item active">基础建筑</span>
                                <span class="bck-item">水利建筑</span>
                                <span class="bck-item">工程建筑</span>
                                <div class="row">
                                    <div class="col-xs-8">
                                        <select id="js_input_area" class="form-control" >
                                            <option>北京市</option>
                                            <option>上海市</option>
                                            <option>重庆市</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_industry_care" class="col-sm-2 col-xs-3 control-label">行业</label>
                        <div class="col-sm-2 col-xs-7">
                            <select id="js_input_industry_care" class="form-control">
                                <option>计算机</option>
                                <option>游戏</option>
                                <option>音乐</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="js_input_mobile" class="col-sm-2 col-xs-3 control-label">关键词</label>
                        <div class="col-sm-4 col-xs-7">
                            <div class="bc-keyword-section">
                                <span class="bck-item">房屋建筑</span>
                                <span class="bck-item active">基础建筑</span>
                                <span class="bck-item">水利建筑</span>
                                <span class="bck-item">工程建筑</span>

                                <div class="row">
                                    <div class="col-xs-8">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" placeholder="Search for...">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button">+</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    {{--提交按钮--}}
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success">提交</button>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    @include('layouts.footer', ['style'=>'white'])
@stop


