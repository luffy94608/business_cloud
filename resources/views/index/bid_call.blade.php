@extends('layouts.default')
@section('title', '招标信息')

{{--内容区域--}}
@section('content')
    {{--header--}}
    @include('layouts.header',['bgStyle'=>'bg-transparent bc-header-section'])

    {{--banner--}}
    @include('templates.banner')


    {{--content--}}
    <div class="container-fluid">
        <div class="container">
{{--            @include('templates.select')--}}

            <div class="row bc-body-section">
                <div class="col-sm-8 col-xs-12 bg-white box-shadow-1 bc-stat-section">
                    <div class="row ">
                        <div class="bc-section-title text-left">数据统计</div>
                        <div class="d-table wd-100 ">
                            <div class="d-table-cell v-align-middle">
                                <div class="col-xs-4 bcs-item">
                                    <p class="title">今日招标总数</p>
                                    <p class="total border-right color-green">{{ $data['today'] }}</p>
                                    {{--<p class="hint">今日更新<span class="color-green">12</span>条信息</p>--}}
                                    <p class="hint"><span class="bcs-block"></span></p>
                                </div>
                                <div class="col-xs-4 bcs-item">
                                    <p class="title">本周招标总数</p>
                                    <p class="total border-right color-orange">{{ $data['week'] }}</p>
                                    {{--<p class="hint">今日更新<span class="color-orange">12</span>条信息</p>--}}
                                    <p class="hint"><span class="bcs-block bg-orange"></span></p>
                                </div>
                                <div class="col-xs-4 bcs-item">
                                    <p class="title">本月招标总数</p>
                                    <p class="total color-purple">{{ $data['month'] }}</p>
                                    {{--<p class="hint">今日更新<span class="color-purple">12</span>条信息</p>--}}
                                    <p class="hint"><span class="bcs-block bg-purple"></span></p>
                                </div>
                                <div class="col-md-10 col-xs-12 col-md-offset-1 bcs-progress-section ">
                                    <div class="bcs-progress-bar" style="width: {{ $data['today_percent'] }}%"></div>
                                    <div class="bcs-progress-bar bg-orange" style="width: {{ $data['week_percent'] }}%"></div>
                                    <div class="bcs-progress-bar bg-purple" style="width: {{ $data['month_percent'] }}%"></div>
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

            <div class="row  mt-20  bg-white box-shadow-1" id="wrapperPageList">
                <div class="bc-section-title purple">招标信息<a href="/search-list" class="color-two-title pull-right font-14 v-align-middle">更多</a></div>
                <div class="col-xs-12">
                    <ul class="nav nav-pills">
                        <li role="presentation" data-type="all" class="active js_search_type"><a href="javascript:void(0);">全部信息</a></li>
                        <li role="presentation" data-type="new" class="js_search_type"><a href="javascript:void(0);">最新发布</a></li>
                        {{--<li role="presentation"><a href="#">最热</a></li>--}}
                    </ul>
                </div>
                <div class="col-xs-12" id="list">
                    {{--<div class="col-sm-6 col-xs-12 mt-10">--}}
                        {{--<div class="col-xs-12 box-shadow-3 bc-list-item">--}}
                            {{--<div class="col-xs-9">--}}
                                {{--<p class="text-cut"><span class="b-icon-tip mr-10 "></span>平顶山市石龙公安局政法专款设备招标公告</p>--}}
                                {{--<p class="col-xs-6">招标方式：公开</p>--}}
                                {{--<p class="col-xs-6">截止时间：2018-11-4</p>--}}
                                {{--<p class="col-xs-6">招标产品：地产</p>--}}
                                {{--<p class="col-xs-6">招标地点：北京</p>--}}
                            {{--</div>--}}
                            {{--<div class="col-xs-3 bcl-right">--}}
                                {{--<canvas class="vie-num" width="36" height="36">70</canvas>--}}
                                {{--<div class="vie-text">70</div>--}}
                                {{--<p class="mb-0">竞争力</p>--}}
                                {{--<p>2017-7-10</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-6 col-xs-12 mt-10">--}}
                        {{--<div class="col-xs-12 box-shadow-3 bc-list-item">--}}
                            {{--<div class="col-xs-2 bcl-img">--}}
                                {{--<img src="/images/default@2x.png" width="60px">--}}
                            {{--</div>--}}
                            {{--<div class="col-xs-6">                   \--}}
                                {{--<p class="text-cut col-xs-12">网易传媒科技有限公司</p>--}}
                                {{--<p class="col-xs-12">招标人：常先生</p>--}}
                                {{--<p class="col-xs-12">截止时间：2018-11-4</p>--}}
                            {{--</div>--}}
                            {{--<div class="col-xs-4 bcl-right pt-15 ">--}}
                                {{--<p class="text-center">--}}
                                    {{--<span class="b-icon-star active-2"></span>--}}
                                    {{--<span class="b-icon-star active-2"></span>--}}
                                    {{--<span class="b-icon-star active-2"></span>--}}
                                    {{--<span class="b-icon-star"></span>--}}
                                    {{--<span class="b-icon-star"></span>--}}
                                {{--</p>--}}
                                {{--<p class="text-center">竞争力</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="col-xs-12 text-center color-hint pt-50 pb-50">
                        暂无发布信息
                    </div>

                </div>
                <div id='list_count' class="gone"></div>
                {!! \App\Http\Builders\OtherBuilder::createPageIndicator() !!}
            </div>

        </div>

        @include('templates.ad')

    </div>

    @include('layouts.footer')
@stop


