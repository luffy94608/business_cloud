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

            <div class="row bg-white box-shadow-1">
                <div class="col-sm-8 col-xs-12 ">
                    <div class="row bc-stat-section">
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
                <div class="col-sm-4 pl-20 hidden-xs">
                    @include('templates.side')
                </div>

            </div>

            <div class="row  mt-20  bg-white box-shadow-1">
                <div class="bc-section-title purple">竞争企业<a href="#" class="color-two-title pull-right font-14 v-align-middle">更多</a></div>
                <div class="col-xs-12">
                    <div class="col-sm-6 col-xs-12 mt-10">
                        <div class="col-xs-12 box-shadow-3 bc-list-item">
                            <div class="col-xs-2 bcl-img">
                                <img src="/images/default@2x.png" width="60px">
                            </div>
                            <div class="col-xs-7">
                                <p class="text-cut col-xs-12">网易传媒科技有限公司</p>
                                <p class="col-xs-12 text-cut">中标项目：平顶山市石龙区公安局政法专款装配采购</p>
                                <p class="col-xs-6">中标产品：地产</p>
                            </div>
                            <div class="col-xs-3 bcl-right">
                                <div class="col-xs-6 p-0">
                                    <canvas class="vie-num" width="36" height="36">20</canvas>
                                    <div class="vie-text">20</div>
                                    <p class="mb-0">竞争力</p>
                                </div>
                                <div class="col-xs-6 p-0">
                                    <div class="vitality-img">
                                        <img src="/images/vitality/1.png" >
                                    </div>
                                    <p class="mb-0">活跃度</p>
                                </div>
                                <p>公布时间：2018-11-4</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12 mt-10">
                        <div class="col-xs-12 box-shadow-3 bc-list-item">
                            <div class="col-xs-2 bcl-img">
                                <img src="/images/default@2x.png" width="60px">
                            </div>
                            <div class="col-xs-7">
                                <p class="text-cut col-xs-12">网易传媒科技有限公司</p>
                                <p class="col-xs-12 text-cut">中标项目：平顶山市石龙区公安局政法专款装配采购</p>
                                <p class="col-xs-6">中标产品：地产</p>
                            </div>
                            <div class="col-xs-3 bcl-right">
                                <div class="col-xs-6 p-0">
                                    <canvas class="vie-num" width="36" height="36">20</canvas>
                                    <div class="vie-text">20</div>
                                    <p class="mb-0">竞争力</p>
                                </div>
                                <div class="col-xs-6 p-0">
                                    <div class="vitality-img">
                                        <img src="/images/vitality/1.png" >
                                    </div>
                                    <p class="mb-0">活跃度</p>
                                </div>
                                <p>公布时间：2018-11-4</p>
                            </div>
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


