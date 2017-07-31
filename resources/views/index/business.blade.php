@extends('layouts.default')
@section('title', '市场数据分析')

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
                <div class="bc-section-title color-with-body">市场数据分析</div>
                <div class="col-xs-12 col-sm-10 col-sm-offset-1 form-horizontal mt-100">

                    <div class="form-group">
                        <label for="js_input_company" class="col-sm-2 col-xs-3 control-label">关注企业名称</label>
                        <div class="col-sm-4 col-xs-7">
                            <input type="email" class="form-control" id="js_input_company" placeholder="请输入企业名称">
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


