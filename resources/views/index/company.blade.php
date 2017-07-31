@extends('layouts.default')
@section('title', '企业数据分析')

{{--内容区域--}}
@section('content')
    {{--header--}}
    @include('layouts.header')
    {{--banner--}}
    @include('templates.banner')
    
    {{--content--}}
    <div class="container-fluid">
        <div class="container ">
            <div class="row  mt-100 pb-100  bg-white box-shadow-1 bc-form-section">
                <div class="bc-section-title color-with-body">企业数据分析</div>
                <div class="col-xs-12 col-sm-10 col-sm-offset-1 form-horizontal mt-100">

                    <div class="form-group">
                        <label for="js_input_area" class="col-sm-2 col-xs-3 control-label">关注地区</label>
                        <div class="col-sm-4 col-xs-7">
                            <select id="js_input_area" class="form-control">
                                <option>北京市</option>
                                <option>上海市</option>
                                <option>重庆市</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="js_input_industry_care" class="col-sm-2 col-xs-3 control-label">关注行业</label>
                        <div class="col-sm-4 col-xs-7">
                            <select id="js_input_industry_care" class="form-control">
                                <option>计算机</option>
                                <option>游戏</option>
                                <option>音乐</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="js_input_industry_care" class="col-sm-2 col-xs-3 control-label">关注时间</label>
                        <div class="col-sm-4 col-xs-7">
                            <select id="js_input_industry_care" class="form-control">
                                <option>最近一月</option>
                                <option>最近两月</option>
                                <option>最近三月</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="js_input_mobile" class="col-sm-2 col-xs-3 control-label">关注关键词</label>
                        <div class="col-sm-4 col-xs-7">
                            <div class="bc-keyword-section">
                                <span class="bck-item">房屋建筑</span>
                                <span class="bck-item active">基础建筑</span>
                                <span class="bck-item">水利建筑</span>
                                <span class="bck-item">工程建筑</span>
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
        @include('templates.ad')
    </div>

    @include('layouts.footer', ['style'=>''])
@stop


