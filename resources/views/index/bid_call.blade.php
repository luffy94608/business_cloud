@extends('layouts.default')
@section('title', '招标信息')

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
                        <div class="d-table wd-100 mt-20">
                            <div class="d-table-cell v-align-middle">
                                <div class="col-xs-4 bcs-item">
                                    <p class="title">今日招标总数</p>
                                    <p class="total border-right color-green">42</p>
                                    {{--<p class="hint">今日更新<span class="color-green">12</span>条信息</p>--}}
                                    <p class="hint"><span class="bcs-block"></span></p>
                                </div>
                                <div class="col-xs-4 bcs-item">
                                    <p class="title">本周招标总数</p>
                                    <p class="total border-right color-orange">42</p>
                                    {{--<p class="hint">今日更新<span class="color-orange">12</span>条信息</p>--}}
                                    <p class="hint"><span class="bcs-block bg-orange"></span></p>
                                </div>
                                <div class="col-xs-4 bcs-item">
                                    <p class="title">本月招标总数</p>
                                    <p class="total color-purple">42</p>
                                    {{--<p class="hint">今日更新<span class="color-purple">12</span>条信息</p>--}}
                                    <p class="hint"><span class="bcs-block bg-purple"></span></p>
                                </div>
                                <div class="col-md-10 col-xs-12 col-md-offset-1 bcs-progress-section ">
                                    <div class="bcs-progress-bar" style="width: 30%"></div>
                                    <div class="bcs-progress-bar bg-orange" style="width: 60%"></div>
                                    <div class="bcs-progress-bar bg-purple" style="width: 90%"></div>
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
                <div class="bc-section-title purple">招标信息<a href="#" class="color-two-title pull-right font-14 v-align-middle">更多</a></div>
                <div class="col-xs-12">
                    <ul class="nav nav-pills">
                        <li role="presentation" class="active"><a href="#">全部信息</a></li>
                        <li role="presentation"><a href="#">最新发布</a></li>
                        <li role="presentation"><a href="#">最热</a></li>
                    </ul>
                </div>
                <div class="col-xs-12">
                    <div class="col-sm-6 col-xs-12 mt-10">
                        <div class="col-xs-12 box-shadow-3 bc-list-item">
                            <div class="col-xs-9">
                                <p class="text-cut"><span class="b-icon-tip mr-10 "></span>平顶山市石龙公安局政法专款设备招标公告</p>
                                <p class="col-xs-6">招标方式：公开</p>
                                <p class="col-xs-6">截止时间：2018-11-4</p>
                                <p class="col-xs-6">招标产品：地产</p>
                                <p class="col-xs-6">招标地点：北京</p>
                            </div>
                            <div class="col-xs-3 bcl-right">
                                <canvas class="vie-num" width="36" height="36">70</canvas>
                                <div class="vie-text">70</div>
                                <p class="mb-0">竞争力</p>
                                <p>2017-7-10</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12 mt-10">
                        <div class="col-xs-12 box-shadow-3 bc-list-item">
                            <div class="col-xs-9">
                                <p class="text-cut"><span class="b-icon-tip mr-10 "></span>平顶山市石龙公安局政法专款设备招标公告</p>
                                <p class="col-xs-6">招标方式：公开</p>
                                <p class="col-xs-6">截止时间：2018-11-4</p>
                                <p class="col-xs-6">招标产品：地产</p>
                                <p class="col-xs-6">招标地点：北京</p>
                            </div>
                            <div class="col-xs-3 bcl-right">
                                <canvas class="vie-num" width="36" height="36">20</canvas>
                                <div class="vie-text">20</div>
                                <p class="mb-0">竞争力</p>
                                <p>2017-7-10</p>
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


