@extends('layouts.default')
@section('title', '列表页')

{{--内容区域--}}
@section('content')
    {{--header--}}
    @include('layouts.header',['bgStyle'=>'bg-xs-green bc-header-section clear-position'])

    {{--banner--}}
    {{--@include('templates.banner')--}}


    {{--content--}}
    <div class="container-fluid" id="wrapperPageList">
        <div class="container">
            <div class="row bc-body-section">
                <div class="col-sm-9 col-xs-12 bg-white box-shadow-1 bc-stat-section">
                    <div class="row ">
                        <div class="bc-section-title text-left">全部信息</div>
                        <div class=" col-xs-12 mt-10 mb-10" id="list">
                            <div class="col-xs-12 text-center color-hint pt-50 pb-50">
                                暂无结果
                            </div>
                            {{--<div class="col-xs-12 box-shadow-3 bc-list-item mt-15">--}}
                                {{--<div class="col-xs-2 bcl-img">--}}
                                    {{--<img src="/images/default@2x.png" width="60px">--}}
                                {{--</div>--}}
                                {{--<div class="col-xs-6 text-left">--}}
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
                        </div>
                        <div id='list_count' class="gone"></div>
                        {!! \App\Http\Builders\OtherBuilder::createPageIndicator() !!}
                    </div>
                </div>
                <div class="col-sm-3 col-xs-12 bc-side-section">
                    <div class="row bg-white box-shadow-1  ht-100">
                        @include('templates.side')
                    </div>
                </div>

            </div>
        </div>


        @include('templates.ad')

    </div>
    
    @include('layouts.footer')
@stop


