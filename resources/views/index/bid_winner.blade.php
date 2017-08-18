@extends('layouts.default')
@section('title', '中标信息')

{{--内容区域--}}
@section('content')
    {{--header--}}
    @include('layouts.header',['bgStyle'=>'bg-transparent bc-header-section'])

    {{--banner--}}
    @include('templates.banner')


    {{--content--}}
    <div class="container-fluid">
        <div class="container">
            {{--@include('templates.select')--}}

            <div class="row bc-body-section">
                <div class="col-sm-8 col-xs-12 bg-white box-shadow-1 bc-stat-section">
                    <div class="row ">
                        <div class="bc-section-title text-left">数据统计</div>
                        <div class="d-table wd-100 ">
                            <div class="d-table-cell v-align-middle">
                                <div class="col-xs-4 bcs-item">
                                    <p class="title">今日中标总数</p>
                                    <p class="total border-right color-green">{{ $data['today'] }}</p>
                                    {{--<p class="hint">今日更新<span class="color-green">12</span>条信息</p>--}}
                                    <p class="hint"><span class="bcs-block"></span></p>
                                </div>
                                <div class="col-xs-4 bcs-item">
                                    <p class="title">本周中标总数</p>
                                    <p class="total border-right color-orange">{{ $data['week'] }}</p>
                                    {{--<p class="hint">今日更新<span class="color-orange">12</span>条信息</p>--}}
                                    <p class="hint"><span class="bcs-block bg-orange"></span></p>
                                </div>
                                <div class="col-xs-4 bcs-item">
                                    <p class="title">本月中标总数</p>
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
                <div class="bc-section-title purple">中标信息
                    <a href="/search-list?src=bid" class="gone color-two-title pull-right font-14 v-align-middle">更多</a>
                </div>
                <div class="col-xs-12" id="list">
                    <div class="col-xs-12 text-center color-hint pt-50 pb-50">
                        暂无发布信息
                    </div>
                    {{--<div class="col-sm-6 col-xs-12 mt-10">--}}
                        {{--<div class="col-xs-12 box-shadow-3 bc-list-item">--}}
                            {{--<div class="col-xs-9">--}}
                                {{--<p class="text-cut"><span class="b-icon-tip mr-10 "></span>平顶山市石龙公安局政法专款设备招标公告</p>--}}
                                {{--<p class="col-xs-12">中标企业：网易传媒科技有限公司</p>--}}
                                {{--<p class="col-xs-12">中标时间：2018-11-4</p>--}}
                            {{--</div>--}}
                            {{--<div class="col-xs-3 bcl-right">--}}
                                {{--<p class="font-16 mt-35">价格<span class="color-orange"><span class="ml-5 mr-5 font-30">3.0</span> 万</span></p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div id='list_count' class="gone"></div>
                {!! \App\Http\Builders\OtherBuilder::createPageIndicator() !!}
                
            </div>

        </div>

        @include('templates.ad')

    </div>

    @include('layouts.footer')
@stop


