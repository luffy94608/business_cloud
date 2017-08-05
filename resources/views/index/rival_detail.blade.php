@extends('layouts.default')
@section('title', '竞争对手')

{{--内容区域--}}
@section('content')
    {{--header--}}
    @include('layouts.header',['bgStyle'=>'bg-transparent bc-header-section'])

    {{--banner--}}
    @include('templates.banner')


    {{--content--}}
    <div class="container-fluid">
        <div class="container">

            <div class="row  mt-20  bg-white box-shadow-1">
                <div class="bc-section-title purple">企业信息</div>
                <div class="col-xs-12  mt-10 ">
                    <div class="col-xs-12 p-0  bc-list-item border-bottom bc-item-hover">
                        <div class="col-xs-2 col-sm-1 bcl-img">
                            <img src="/images/default@2x.png" width="60px">
                        </div>
                        <div class="col-xs-6 col-sm-5">
                            <p class="text-cut col-xs-12">网易传媒科技有限公司</p>
                            <p class="col-xs-12 font-12 text-cut color-sub-title">中标项目数量：12个</p>
                            <p class="col-xs-12 font-12 color-sub-title">中标候选人次数：6次</p>
                        </div>
                        <div class="col-xs-4 text-center pt-15 col-sm-3">
                            <p class="col-sm-12 col-xs-12 col-xs-push-4 col-sm-push-0">
                                <span class="b-icon-star active-2"></span>
                                <span class="b-icon-star active-2"></span>
                                <span class="b-icon-star active-2"></span>
                                <span class="b-icon-star"></span>
                                <span class="b-icon-star"></span>
                            </p>
                            <p class="color-hint col-sm-12  hidden-xs col-xs-pull-8 col-sm-pull-0">竞争力</p>
                        </div>
                        <div class="col-xs-4 text-center pt-15 col-sm-3">
                            <p class="col-sm-12 col-xs-12 col-xs-push-4 col-sm-push-0">
                                <span class="b-icon-star active"></span>
                                <span class="b-icon-star active"></span>
                                <span class="b-icon-star active"></span>
                                <span class="b-icon-star"></span>
                                <span class="b-icon-star"></span>
                            </p>
                            <p class="color-hint col-sm-12 hidden-xs col-xs-pull-8 col-sm-pull-0">活跃度</p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 bc-stat-section pt-40">
                    <div class="d-table wd-100 ">
                        <div class="d-table-cell v-align-middle">
                            <div class="col-sm-4 col-xs-12 bcs-item">
                                <p class="font-18">中标数据统计<span class="color-green font-36">32</span><span class="color-hint">分</span></p>
                                <div id="js_chart_1" style="height:200px;width: 100%"></div>
                            </div>
                            <div class="col-sm-4 col-xs-12 bcs-item">
                                <p class="font-18">竞争活跃度<span class="color-orange font-36">32</span><span class="color-hint">分</span></p>
                                <div id="js_chart_2" style="height:200px;width: 100%"></div>
                            </div>
                            <div class="col-sm-4 col-xs-12 bcs-item">
                                <p class="font-18">企业中标金额<span class="color-red font-36">32</span><span class="color-hint">万</span></p>
                                <div id="js_chart_3" style="height:200px;width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        @include('templates.ad')

    </div>

    @include('layouts.footer')
@stop


