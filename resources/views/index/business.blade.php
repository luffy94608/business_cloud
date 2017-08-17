@extends('layouts.default')
@section('title', '市场数据分析')

{{--内容区域--}}
@section('content')
    {{--header--}}
    @include('layouts.header',['bgStyle'=>'bg-transparent bc-header-section'])

    {{--banner--}}
    @include('templates.banner')

    {{--content--}}
    <div class="container-fluid">
        <div class="container ">
            <div class="row  mt-20 pb-100  bg-white box-shadow-1 bc-form-section">
                <div class="bc-section-title color-green">企业数据分析</div>
                <div class="col-xs-12 col-sm-10 col-sm-offset-1 form-horizontal mt-100">

                    <div class="form-group">
                        <label for="js_input_company_name" class="col-sm-2 col-xs-4 control-label">关注企业名称</label>
                        <div class="col-sm-5 col-xs-8">
                            <input type="text" class="form-control" name="company_name" id="js_input_company" placeholder="请输入企业名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="js_input_time" class="col-sm-2 col-xs-3 control-label">关注时间</label>
                        <div class="col-sm-4 col-xs-7">
                            <select id="js_input_time" class="form-control">
                                <option value="1">最近1个月</option>
                                <option value="3">最近3个月</option>
                                <option value="6">最近6个月</option>
                            </select>
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
        @include('templates.ad')
    </div>

    @include('layouts.footer', ['style'=>''])
@stop


