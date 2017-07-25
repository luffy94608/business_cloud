@extends('layouts.default')
@section('title', '哈罗同行')

{{--内容区域--}}
@section('content')
    <main id="page" >
        <div class="hd-download">
            <img  src="/images/other/intro-0.png">
            <img  src="/images/other/intro-1.png">
            <img  src="/images/other/intro-2.png">
            <img  src="/images/other/intro-3.png">
            <div class="hld-text">
                <div  class="hl-download" ng-click="download();">立即下载</div>
            </div>
        </div>
    </main>
@stop

