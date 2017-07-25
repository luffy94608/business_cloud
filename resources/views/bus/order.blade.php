@extends('layouts.default')
@section('title', '车票列表')
{{-- mobiscroll  mmenu  --}}
<link rel="stylesheet" href="/bower_components/datepicker/css/mobiscroll.custom-3.0.0-beta5.min.css"/>
<link rel="stylesheet" href="/bower_components/swiper/dist/css/swiper.min.css"/>
<link rel="stylesheet" href="/styles/custom-mobiscroll.css"/>
{{--内容区域--}}
@section('content')
    <main id="page" >
        <div class="ticket-map-header">
              <div >
                  <label for="datetime" class="block">
                      <label for="datetime" id="datetime_title"></label>
                      <input type="hidden" id="datetime" value="">
                      <label for="datetime " class="gone" id="datetime_after_icon">
                          <svg class="icon-svg">
                              <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/images/icons.svg#icon-down-o"></use>
                          </svg>
                      </label>
                  </label>
              </div>
        </div>
        <!-- Swiper -->
        <div class="swiper-container ticket-map ">
            <ul class="swiper-wrapper ">
                
            </ul>
        </div>

        <div id="js_drop_load_area">
            <div class="bus-ticket-list" id="list" >
                
                {{--<div class="bt-item">--}}
                    {{--<div class="bt-header">--}}
                        {{--<span class="code">K002</span>--}}
                        {{--<span class="name">班车车票</span>--}}

                        {{--<span class="icon-location " ></span>--}}
                    {{--</div>--}}
                    {{--<div class="ticket-gap"></div>--}}
                    {{--<div class="bt-body">--}}
                        {{--<div class="item-bd">--}}
                            {{--<h4 class="bd-txt">方正国际大厦--回龙观</h4>--}}
                            {{--<h4 class="bd-txt">乘车时间：</h4>--}}
                            {{--<h4 class="bd-txt">上车站点：</h4>--}}
                            {{--<h4 class="bd-txt">下车站点：</h4>--}}
                        {{--</div>--}}
                        {{--<div class="item-right">--}}
                            {{--<button class="btn btn-primary full-width btn-s  " id="js_coupon_confirm_btn" >出示车票</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

                
            </div>
            <button class="text-center btn-primary full-width mt-10 loading-more gone" ></button>
        </div>
        
    </main>
    @include('templates.ticket',[])
@stop


