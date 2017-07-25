@extends('layouts.default')
@section('title', '快捷巴士')

{{--内容区域--}}
@section('content')
    <script src='http://api.map.baidu.com/api?v=2.0&ak={{Config::get('app')['ak']}}' type='text/javascript'></script>

    <main id="page" >
        {{--<a href="/shuttle-map" data-replace="true" class="shuttle-switch-btn js_location_url">切换地图</a>--}}
        <div id="js_drop_load_area">
            <div class="shuttle-list" id="list" >
            </div>
            <button class="text-center btn-primary full-width mt-10 loading-more gone" ></button>
        </div>
    </main>
@stop

@include('templates.js')
