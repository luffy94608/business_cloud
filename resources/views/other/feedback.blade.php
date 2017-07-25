@extends('layouts.default')
@section('title', '投诉建议')

{{--内容区域--}}
@section('content')
    {{-- mobiscroll  mmenu  --}}
    <link rel="stylesheet" href="/bower_components/datepicker/css/mobiscroll.custom-3.0.0-beta5.min.css"/>
    <link rel="stylesheet" href="/bower_components/swiper/dist/css/swiper.min.css"/>
    <link rel="stylesheet" href="/styles/custom-mobiscroll.css"/>
    <main id="page  color-title" >
        <div class="form mt-10 pl-10">
            <div id="js_line_sec">
                <div class="form-item">
                    <label class="item-label">乘车线路：</label>
                    <div class="item-field">
                        <input type="text" id="js_input_line" placeholder="请输入线路编号或线路名称" class="f-text">
                    </div>
                </div>
            </div>
            <div class="form-item">
                <label class="item-label" for="datetime">乘车日期：</label>
                <div class="item-field">
                    <input type="text" id="datetime" placeholder="请选择乘车日期" class="f-text">
                </div>
                <icon class="icon-v-right"></icon>
            </div>
        </div>

        @if (!empty($complaints))
            <div class="form form--no-label mt-10 pl-10">

            @foreach( $complaints as $complaint )
                <div class="form-item">
                    <label class="block"><input type="checkbox" class="circle-input-checkbox js_reason_pick" value="{{ $complaint['id'] }}">{{ $complaint['content'] }}</label>
                </div>
            @endforeach
                
            </div>
        @endif


        <div class="form form--no-label mt-10 p-10">
            <div class="form-item">
                <textarea class="f-textarea" id="js_input_content" placeholder="请您尽量详情描述投诉内容，方便我们能及时解决您的问题。"></textarea>
            </div>
        </div>

        <div class="form mt-10 pl-10">
            <div class="form-item">
                <label class="item-label">联系方式：</label>
                <div class="item-field">
                    <input type="text" id="js_input_phone" placeholder="请输入您的手机号" class="f-text">
                </div>
            </div>
        </div>
        <div class="wrap">
            <button class="btn btn-primary text-center  full-width font-16 mt-20 mb-20"  id="js_remark_btn">提交</button>
        </div>
    </main>
    <section class="dialog-wrap bg-grey" id="js_search_line_modal" >
        <div class="dialog-scroll mt-10 pd-10 bg-white">
            <div class="form form--no-label mt-45 pl-10  hidden-border-top">
                @foreach( $lines as $line )
                    <div class="form-item js_line_item_option gone" data-line="{{ \GuzzleHttp\json_encode($line) }}">
                        <label class="block"><span class="color-orange mr-10">{{$line['line_code']}} </span>{{ $line['line_name'] }}</label>
                    </div>
                @endforeach
            </div>
        </div>

    </section>
@stop

