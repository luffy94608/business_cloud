@extends('layouts.default')
@section('title', '评价')

{{--内容区域--}}
@section('content')
    <main id="page" >
        @include('templates.remark',['ticket'=>$ticket])
    </main>
@stop

