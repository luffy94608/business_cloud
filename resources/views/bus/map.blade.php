@extends('layouts.default')
@section('title', '线路详情')

<script src='http://api.map.baidu.com/api?v=2.0&ak={{Config::get('app')['ak']}}' type='text/javascript'></script>
<script src="http://api.map.baidu.com/library/LuShu/1.2/src/LuShu_min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/InfoBox/1.2/src/InfoBox.js"></script>

{{--内容区域--}}
@section('content')
    <main id="page" >
        <div id="map-container"></div>
        <div class="gone" id="js_station_data" data-info="{{ json_encode($stations) }}"></div>
    </main>
@stop

