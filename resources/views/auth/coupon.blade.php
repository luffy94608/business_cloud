@extends('layouts.default')
@section('title', '我的优惠券')
@section('content')
    <main id="page" >
        <div class="me-a-header">
            <p class="title"><span>{{ $total }}</span> 张</p>
            <p class="sub-title">我的优惠券</p>
        </div>
        <div class="coupon-exchange-sec">
            <div class="coupon-exchange" >
                <input type="text" id="js_input_code" placeholder="请输入兑换码兑换优惠券" >
                <button class="btn btn-primary" id="js_submit" >兑换</button>
            </div>
        </div>
       <ul class="hl-coupon-list" id="list">
          
           {{--@if( !$total )--}}
           {{--@else--}}
               {{--@foreach( $coupons as $coupon )--}}
                   {{--<li class='hl-coupon {{ ($coupon['is_available'] && $coupon['expired_time']>\Carbon\Carbon::now()->timestamp)?'':'expired'  }}'  >--}}
                       {{--<div class='hlc-title'>{{ $coupon['title'] }}</div>--}}
                       {{--<div class="hlc-content">--}}
                           {{--<div class="hlcc-left">--}}
                                    {{--<span>--}}
                                       {{--{{ $coupon['value'] }}--}}
                                    {{--</span>--}}
                               {{--<sub>{{ $coupon['type'] ==0 ?'次':'￥' }}</sub>--}}
                           {{--</div>--}}
                           {{--<div class='hlcc-right'>--}}
                               {{--<p class='title'>{{ $coupon['type_desc'] }}</p>--}}
                               {{--<p class='sub-title'>{{ $coupon['description'] }}</p>--}}
                               {{--<p class='sub-title'>有效期至：{{ \Carbon\Carbon::createFromTimestamp($coupon['expired_time'])->toDateString() }}</p>--}}
                           {{--</div>--}}
                       {{--</div>--}}
                   {{--</li>--}}
               {{--@endforeach--}}
           {{--@endif--}}
       </ul>
    </main>

@stop
