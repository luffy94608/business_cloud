@extends('layouts.default')
@section('title', '竞争对手')

{{--内容区域--}}
@section('content')
    {{--header--}}
    @include('layouts.header')
    {{--banner--}}
    @include('templates.banner')


    {{--content--}}
    <div class="container-fluid">
        <div class="container">
            @include('templates.select')

            <div class="row bc-body-section">
                <div class="col-sm-8 col-xs-12 bg-white box-shadow-1 bc-stat-section">
                    <div class="row ">
                        <div class="bc-section-title text-left">数据统计</div>
                        <div class="d-table wd-100 ">
                            <div class="d-table-cell v-align-middle">
                                <div class="col-xs-6 bcs-item">
                                    <p class="title">今日新增竞标企业</p>
                                    <p class="total border-right color-green">42</p>
                                    <p class="hint">
                                        <img src="/images/banner/rival_new.png" width="100px" class="mt-15">
                                    </p>
                                </div>
                                <div class="col-xs-6 bcs-item">
                                    <p class="title">行业竞争企业共</p>
                                    <p class="total color-orange">42</p>
                                    <p class="hint">
                                        <img src="/images/banner/rival_total.png" width="100px">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xs-12 bc-side-section">
                    <div class="row bg-white box-shadow-1  ht-100">
                        @include('templates.side')
                    </div>
                </div>

            </div>

            <div class="row  mt-20  bg-white box-shadow-1">
                <div class="bc-section-title purple">竞争企业<a href="#" class="color-two-title pull-right font-14 v-align-middle">更多</a></div>
                <div class="col-sm-6 col-xs-12 mt-10">
                    <div class="col-xs-12 box-shadow-3 bc-list-item">
                        <div class="col-xs-2 bcl-img">
                            <img src="/images/default@2x.png" width="60px">
                        </div>
                        <div class="col-xs-6">
                            <p class="text-cut col-xs-12">网易传媒科技有限公司</p>
                            <p class="col-xs-12 text-cut">中标项目数量：12个</p>
                            <p class="col-xs-12">中标候选人次数：6次</p>
                        </div>
                        <div class="col-xs-4 bcl-right pt-15">
                            <p>竞争力：
                                <span class="b-icon-star active-2"></span>
                                <span class="b-icon-star active-2"></span>
                                <span class="b-icon-star active-2"></span>
                                <span class="b-icon-star"></span>
                                <span class="b-icon-star"></span>
                            </p>
                            <p>活跃度：
                                <span class="b-icon-star active"></span>
                                <span class="b-icon-star active"></span>
                                <span class="b-icon-star active"></span>
                                <span class="b-icon-star"></span>
                                <span class="b-icon-star"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12 mt-10">
                    <div class="col-xs-12 box-shadow-3 bc-list-item">
                        <div class="col-xs-2 bcl-img">
                            <img src="/images/default@2x.png" width="60px">
                        </div>
                        <div class="col-xs-6">
                            <p class="text-cut col-xs-12">网易传媒科技有限公司</p>
                            <p class="col-xs-12 text-cut">中标项目数量：12个</p>
                            <p class="col-xs-12">中标候选人次数：6次</p>
                        </div>
                        <div class="col-xs-4 bcl-right pt-15">
                            <p>竞争力：
                                <span class="b-icon-star active-2"></span>
                                <span class="b-icon-star active-2"></span>
                                <span class="b-icon-star active-2"></span>
                                <span class="b-icon-star"></span>
                                <span class="b-icon-star"></span>
                            </p>
                            <p>活跃度：
                                <span class="b-icon-star active"></span>
                                <span class="b-icon-star active"></span>
                                <span class="b-icon-star active"></span>
                                <span class="b-icon-star"></span>
                                <span class="b-icon-star"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 text-center">
                    <ul class="pagination">
                        <li ><a href="#" aria-label="Previous"><span aria-hidden="true">首页</span></a></li>
                        <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li>
                        <li><a href="#" aria-label="Next"><span aria-hidden="true">末页</span></a></li>
                    </ul>
                </div>
            </div>

        </div>

        @include('templates.ad')

    </div>

    @include('layouts.footer')
@stop


