@extends('layouts.default')
@section('title', '实时位置')

<script src='http://api.map.baidu.com/api?v=2.0&ak={{Config::get('app')['ak']}}' type='text/javascript'></script>
<script src="http://api.map.baidu.com/library/LuShu/1.2/src/LuShu_min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/InfoBox/1.2/src/InfoBox.js"></script>

{{--内容区域--}}
@section('content')
    <main id="page">
        <div id="map-container"></div>
        <div class="fix-map-up p-10" id="js_position_section">
            <div class="fm-empty ">
                暂未运营
            </div>
            
            {{--<div class="fm-item ">--}}
                {{--<div class="fmo-left">--}}
                    {{--<p class="title">孙师傅 * 京123123</p>--}}
                    {{--<p class="sub-title">已驶离上地站，发往下一站中关村</p>--}}
                    {{--<p class="color-orange">车辆位置：胡夏银行</p>--}}
                {{--</div>--}}
                {{--<div class="fmo-right">--}}
                    {{--<a href="tel:18500227320"  class="js_driver_mobile">--}}
                        {{--<img width="40px" height="40px" src="/images/icons/tel@3x.png">--}}
                    {{--</a>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>
        <div class="fx-bus" id="js_location_bus"></div>

        <div class="gone" id="js_station_data" data-info="{{ json_encode($stations) }}"></div>
        <div class="gone" id="js_line_id_data" data-info="{{ $line['line_id'] }}"></div>
    </main>
@stop

