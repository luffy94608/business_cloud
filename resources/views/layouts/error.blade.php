@extends('layouts.default')
@section('title', '商情云')
@section('bodyBg', 'bg-white')

@section('content')
    <div class="error-section">
        <div class="es-content">
            @if( !empty($title) )
                <p class="es-title">{{  $title }}</p>
            @else
                <p class="es-title">正在努力升级中...</p>
                <p>为了给您提供更好的服务，过一会再来看看吧。</p>
            @endif
        </div>
    </div>
@stop
