@extends('layouts.default')
@section('title', '机场巴士')
@section('bodyBg', 'bg-white')

{{--内容区域--}}
@section('content')
    <div>
        @include('layouts.header',['title'=>'行程结束','leftBtnType'=>1])
        <main id="pages" class="page-wrap">
            <div class="page ">
                <div class="wrap">
                    {{--司机信息--}}
                    @include('templates.driver',['order'=>$order])

                    {{--成功支付--}}
                    <p class="gap-line">成功支付</p>
                    <div class="pay-section text-center">
                        <p class="font-16 "><span class="font-36">{{ $order['price']-$order['discount_price'] }}</span>元</p>
                        @if( $order['price']-$order['discount_price'] > 0 )
                            @if( $order['receipt_status'] == 2 )
                                <p class="color-hint">已开发票</p>
                            @elseif ($order['receipt_status'] == 1 )
                                <p class="color-hint">开票中</p>
                            @else
                                <p class="color-blue js_receipt_btn">扫码索要发票 ></p>
                            @endif
                        @endif
                    </div>

                    {{--评价司机--}}
                    <p class="gap-line">评价司机</p>
                    <div class="pay-section">
                        <div class="remark-star" data-score="{{ isset($order['rate']) ? $order['rate']['score'] : '0' }}">
                            <i class="ap-star "></i>
                            <i class="ap-star"></i>
                            <i class="ap-star"></i>
                            <i class="ap-star"></i>
                            <i class="ap-star"></i>
                        </div>

                        <div class="ap-tag-list">
                            @if( $tags )
                                @foreach($tags as $tag)
                                    <span class="ap-tag {{ in_array($tag['id'],$order['tag_ids']) ? 'active' : '' }} {{ empty($order['rate']) ? '' : 'disabled' }}" data-id="{{ $tag['id'] }}">{{ $tag['name'] }}</span>
                                @endforeach
                            @endif

                        </div>
                        @if( (isset($order['rate']) && $order['rate']['note']) || !isset($order['rate']['note']))
                            <textarea class="remark-desc" id="js_order_note"  {{ isset($order['rate']) ? 'readonly' : ''  }} placeholder="其他意见及建议">{{ isset($order['rate']) ? $order['rate']['note'] : ''  }}</textarea>
                        @endif
                    </div>

                    @if(empty($order['rate']))
                        <button class="text-center btn-primary full-width mt-10 mb-20" id="js_remark_btn">提交</button>
                    @endif
                    <input type="hidden" id="js_pay_order_id" value="{{ $order['order_id'] }}">
                    <input type="hidden" id="js_pay_fee" value="{{ $order['price'] }}">
                    <input type="hidden" id="js_pay_discount" value="{{ $order['discount_price'] }}">
                </div>
            </div>
        </main>


    </div>

@stop
