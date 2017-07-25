@extends('layouts.default')
@section('title', '班车列表')

{{--内容区域--}}
@section('content')
    <main id="page" >
        <div class="tab-nav">
            <div class="tab-items">
                <a class="tab-item {{ $type==0 ? 'active':'' }}" href="javascript:;" data-type="0">上班</a>
                <a class="tab-item {{ $type==1 ? 'active':'' }}" href="javascript:;" data-type="1">下班</a>
            </div>
        </div>

        <div id="js_drop_load_area">
            <div class="bus-list" id="list" >

                {{--<div class="bus-item">--}}
                    {{--<div class="bus-header clearfix">--}}
                        {{--<span class="code">K002</span>--}}
                        {{--<ul class="shifts">--}}
                            {{--<li>19:15</li>--}}
                            {{--<li>19:30</li>--}}
                            {{--<li>19:45</li>--}}
                        {{--</ul>--}}
                        {{--<span class="more bus-after-v js_more_btn" >更多</span>--}}
                    {{--</div>--}}
                    {{--<div class="bus-body">--}}
                        {{--<div class="item-bd">--}}
                            {{--<h4 class="bd-tt">方正国际大厦--回龙观</h4>--}}
                            {{--<div class="bd-txt">2017-03-30</div>--}}
                        {{--</div>--}}
                        {{--<div class="item-right">--}}
                            {{--<div class="font-16 bus-after-v relative">9元</div>--}}
                            {{--<div class="color-orange font-12">月票特惠</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

            </div>
            <button class="text-center btn-primary full-width mt-10 loading-more gone" ></button>
        </div>
        
    </main>
@stop


