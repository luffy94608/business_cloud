@extends('layouts.default')
@section('title', '个人信息')

{{--内容区域--}}
@section('content')
    {{--content--}}
    @include('layouts.header',['bgStyle'=>'bg-xs-green bc-header-section clear-position'])

    <div class="container-fluid">
        <div class="container">
            <div class="row  mt-20  bg-white box-shadow-1 bc-form-section">
                <div class="bc-section-title color-with-body hidden-xs">个人信息</div>
                <div class="col-xs-12 col-sm-10 col-sm-offset-1 form-horizontal">
                    {{--帐号相关--}}
                    <div class="bc-form-title-section">
                        <div class="title">帐号</div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_mobile" class="col-sm-2 col-xs-3 control-label">手机号</label>
                        <div class="col-sm-5 col-xs-7">
                            <p class="form-control-static">{{ $user['username'] }}</p>
                        </div>
                    </div>

                    {{--个人信息--}}
                    <div class="bc-form-title-section">
                        <div class="title">个人信息</div>
                        {{--<span class="color-hint">（必填）</span>--}}
                    </div>
                    <div class="form-group">
                        <label for="js_input_name" class="col-sm-2 col-xs-3 control-label">姓名</label>
                        <div class="col-sm-5 col-xs-7">
                            <p class="form-control-static">{{ $user['profile']['name'] }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_sex" class="col-sm-2 col-xs-3 control-label">性别</label>
                        <div class="col-sm-2 col-xs-7">
                            {{--<select id="js_input_sex" name="gender" class="form-control">--}}
                                {{--<option value="0">请选择</option>--}}
                                {{--<option value="1">男</option>--}}
                                {{--<option value="2">女</option>--}}
                            {{--</select>--}}
                            <p class="form-control-static">{{ $user['profile']['gender'] == 1 ? '男' : '女' }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_job" class="col-sm-2 col-xs-3 control-label">职位</label>
                        <div class="col-sm-5 col-xs-7">
                            <p class="form-control-static">{{ $user['profile']['position'] }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_email" class="col-sm-2 col-xs-3 control-label">邮箱</label>
                        <div class="col-sm-5 col-xs-7">
                            <p class="form-control-static">{{ $user['profile']['mail'] }}</p>
                        </div>
                    </div>

                    {{--企业信息--}}
                    <div class="bc-form-title-section">
                        <div class="title">企业信息</div>
                        {{--<span class="color-hint">（必填）</span>--}}
                    </div>
                    <div class="form-group">
                        <label for="js_input_company_name" class="col-sm-2 col-xs-3 control-label">名称</label>
                        <div class="col-sm-5 col-xs-7">
                            <input type="text" class="form-control" name="company_name" id="js_input_company" value="{{ $user['profile']['company_name'] }}" placeholder="请输入名称">
{{--                            <p class="form-control-static">{{ $user['profile']['company_name'] }}</p>--}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_company_area" class="col-sm-2 col-xs-3 control-label">所在地</label>
                        <div class="col-sm-2 col-xs-7">
                            <select id="js_input_company_area" class="form-control" >
                                {!!  \App\Http\Builders\DataBuilder::toRegionLevelOneOptionHtml($user['profile']['company_area'])  !!}
                            </select>
                        </div>
                        <div class="col-xs-12 mt-15 visible-xs-block"></div>
                        <label for="js_input_company_industry" class="col-sm-1 col-xs-3 control-label">行业</label>
                        <div class="col-sm-2 col-xs-7">
                            <select id="js_input_company_industry"  class="form-control">
                                {!!  \App\Http\Builders\DataBuilder::toIndustryLevelOneOptionHtml($user['profile']['company_industry'])  !!}
                            </select>
                        </div>
                    </div>


                    {{--关注信息--}}
                    <div class="bc-form-title-section">
                        <div class="title">关注信息</div>
                        {{--<span class="color-hint">（必填）</span>--}}
                    </div>
                    <div class="form-group">
                        <label for="js_input_area" class="col-sm-2 col-xs-3 control-label">地区</label>
                        <div class="col-sm-4 col-xs-7">
                            <div class="bc-keyword-section">
                                <div id="js_follow_area_list">
                                    {!! \App\Http\Builders\UserBuilder::toUserFollowAreaHtml($user['profile']['follow_area']) !!}
                                </div>
                                <div class="row">
                                    <div class="col-xs-8">
                                        <select id="js_follow_area_add_btn" class="form-control" >
                                            <option value="-1">请选择</option>
                                            {!!  \App\Http\Builders\DataBuilder::toRegionLevelOneOptionHtml()  !!}
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_follow_industry" class="col-sm-2 col-xs-3 control-label">行业</label>
                        <div class="col-sm-2 col-xs-7">
                            <select id="js_input_follow_industry" class="form-control">
                                {!!  \App\Http\Builders\DataBuilder::toIndustryLevelOneOptionHtml($user['profile']['follow_industry'])  !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="js_input_mobile" class="col-sm-2 col-xs-3 control-label">关键词</label>
                        <div class="col-sm-4 col-xs-7">
                            <div class="bc-keyword-section">
                                <div id="js_follow_keyword_list">
                                    {!! \App\Http\Builders\UserBuilder::toUserFollowKeywordHtml($user['profile']['follow_keyword']) !!}
                                </div>

                                <div class="row">
                                    <div class="col-xs-8">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="js_keyword_input" placeholder="请输入关键字">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default color-sub-title" id="js_keyword_add_btn" type="button">+</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
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


