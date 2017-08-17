@extends('layouts.default')
@section('title', '商情云')

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
                        <div class="d-table wd-100">
                            <div class="d-table-cell v-align-middle pl-15 pr-15">
                                <div class="col-xs-4 bcs-item bc-item-hover">
                                    <p class="title"><a class="color-title" href="/bid-call" >招标信息</a></p>
                                    <p class="total js_border_item border-right"><a class="color-green" href="/bid-call" >{{ $data['tender'] }}</a></p>
                                    <div class="pl-15 pr-15 pt-20">
                                        <p class="hint text-left">今日更新<span class="color-green">{{ $data['tender_today'] }}</span>条信息</p>
                                        <div class="progress bid-progress mt-20">
                                            <div class="progress-bar bg-green" role="progressbar" data-percent="{{$data['tender_percent']}}%" aria-valuemin="0" aria-valuemax="100" ></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4 bcs-item bc-item-hover">
                                    <p class="title"><a class="color-title" href="/bid-winner" >中标信息</a></p>
                                    <p class="total js_border_item border-right color-orange"><a class="color-orange" href="/bid-winner" >{{ $data['bid'] }}</a></p>
                                    <div class="pl-15 pr-15 pt-20">
                                        <p class="hint text-left">今日更新<span class="color-orange">{{ $data['bid_today'] }}</span>条信息</p>
                                        <div class="progress bid-progress mt-20">
                                            <div class="progress-bar bg-orange" role="progressbar" data-percent="{{$data['bid_percent']}}%" aria-valuemin="0" aria-valuemax="100" ></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4 bcs-item bc-item-hover">
                                    <p class="title"><a class="color-title" href="/rival" >竞争对手</a></p>
                                    <p class="total color-purple"><a class="color-purple" href="/rival" >{{ $data['competitor_today'] }}</a></p>
                                    <div class="pl-15 pr-15 pt-20">
                                        <p class="hint text-left">今日新增<span class="color-purple">{{ $data['competitor_today'] }}</span>条数据</p>
                                        <div class="progress bid-progress mt-20">
                                            <div class="progress-bar bg-purple" role="progressbar" data-percent="{{$data['competitor_percent']}}%" aria-valuemin="0" aria-valuemax="100" ></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-10 col-xs-12 col-md-offset-1 bcs-progress-section gone">
                                    <div class="bcs-progress-bar" style="width: 30%"></div>
                                    <div class="bcs-progress-bar bg-orange" style="width: 60%"></div>
                                    <div class="bcs-progress-bar bg-purple" style="width: 90%"></div>
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
                <div class="bc-section-title purple">最新发布
                    {{--<a href="/search-list?src=publish" class="color-two-title pull-right font-14 v-align-middle">查看更多</a>--}}
                </div>
                <div class="col-xs-12" id="list">
                    <div class="col-xs-12 text-center color-hint pt-50 pb-50">
                        暂无发布信息
                    </div>
                    {{--<div class="col-sm-6 col-xs-12 mt-10 p-0">--}}
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
                    {{--<div class="col-sm-6 col-xs-12 mt-10 p-0">--}}
                        {{--<div class="col-xs-12 box-shadow-3 bc-list-item">--}}
                            {{--<div class="col-xs-9">--}}
                                {{--<p class="text-cut"><span class="b-icon-tip mr-10 "></span>平顶山市石龙公安局政法专款设备招标公告</p>--}}
                                {{--<p class="col-xs-6">招标方式：公开</p>--}}
                                {{--<p class="col-xs-6">截止时间：2018-11-4</p>--}}
                                {{--<p class="col-xs-6">招标产品：地产</p>--}}
                                {{--<p class="col-xs-6">招标地点：北京</p>--}}
                            {{--</div>--}}
                            {{--<div class="col-xs-3 bcl-right">--}}
                                {{--<canvas class="vie-num" width="36" height="36">20</canvas>--}}
                                {{--<div class="vie-text">20</div>--}}
                                {{--<p class="mb-0">竞争力</p>--}}
                                {{--<p>2017-7-10</p>--}}
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


