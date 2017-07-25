@extends('layouts.default')
@section('title', '快捷巴士')
{{--内容区域--}}
@section('content')
    <link rel="stylesheet" href="/bower_components/swiper/dist/css/swiper.min.css"/>
    <script src='http://api.map.baidu.com/api?v=2.0&ak={{Config::get('app')['ak']}}' type='text/javascript'></script>
    <script src="http://api.map.baidu.com/library/LuShu/1.2/src/LuShu_min.js" type="text/javascript"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/library/InfoBox/1.2/src/InfoBox.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/library/EventWrapper/1.2/src/EventWrapper.min.js"></script>

    <script type="text/javascript" src="/scripts/libs/TextIconOverlay.js"></script>
    <script type="text/javascript" src="/scripts/libs/MarkerClusterer.js"></script>
    
    <main id="page" >
        <a href="/shuttle-list" data-replace="false" class="shuttle-switch-btn js_location_url"></a>
        <div id="map-container"></div>
        <a href="javascript:void(0);" class="shuttle-quick-btn  js_show_all_ticket_btn gone">购票</a>
        <section class="shuttle-buy-wrap js_shuttle_buy_wrap_sec gone">
        </section>
    </main>

    {{--隐藏数据data--}}
    <div class="gone" id="js_shuttle_lines_data" data-info="{{ json_encode($list) }}"></div>
    <input type="hidden" value="{{ time() }}" id="js_load_time">

    {{--购票panel--}}
    <script id="shuttle_buy_wrap_content" type="text/html">
        <div class="sbw-content">
            <div class="sml-body">
                @{{ each lines as line index  }}
                    <div class="js_line_item js_item_@{{ line.line_id }} @{{ index==0 ? '' : 'gone' }} ">
                        <p class="smb-name " >@{{ line.line_name }}</p>

                        <p class="smb-hint  ">
                            <i class="station-time"></i>
                            @{{  line.business_hour }}    <span class="smb-hint @{{ distance > 1000 ? 'color-red' : '' }}">（距我 @{{ distanceTitle }} ）</span>
                        </p>

                        {{--线路code --}}
                        <div class="smi-buy-btn js_buy_btn  @{{ line.status == 0 ? '' : 'disabled' }}">

                            @{{ if line.status == 0  }}
                            <span class="price-base">￥</span><span class="price mr-5">@{{ line.price }}</span>购票
                            @{{ else }}
                            暂未运营
                            @{{ /if }}
                        </div>
                        
                    </div>


                @{{ /each }}
            </div>
            <div class="sml-footer ">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @{{ each lines as line index  }}
                        <span class="swiper-slide smh-code @{{ index==0 ? 'active' : '' }}" data-id="@{{ line.line_id }}" data-status="@{{ line.status }}" data-line_code="@{{ line.line_code }}">@{{ line.line_code }}</span>
                        @{{ /each }}
                        {{--<div class="bg-slide"></div>--}}
                    </div>
                </div>
            </div>

        </div>  ​
    </script>

    {{--站点--}}
    <script id="shuttle_map_info_window" type="text/html">
        <div class="sm-info-window">
            @{{ station.short_name }}
        </div>
    </script>
    @include('templates.ticket',[])
    @include('templates.js',[])

@stop

