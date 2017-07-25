@extends('layouts.default')
@section('title', '车票详情')

{{--内容区域--}}
@section('content')
    <main id="page" >
        <div id="js_drop_load_area">
            <div class="bus-ticket-list " id="list" >
                <div class="bt-item">
                    <div class="bt-header text-center">
                        <span class="name">快捷巴士购票凭证</span>
                        {{--<span class="icon-location " ></span>--}}
                    </div>
                    <div class="ticket-gap"></div>
                    <div class="bt-detail">
                        <div class="item-bd">
                            <span class="bd-tt">乘车日期</span><span class="bd-txt">{{ $ticket['dept_date']  }}</span>
                        </div>
                    </div>
                    <p class="ticket-line-gap"></p>
                    <div class="bt-detail">
                        <div class="item-bd">
                            <span class="bd-tt">票价</span><span class="bd-txt">{{ $ticket['amount']  }}元</span>
                        </div>
                        <div class="item-bd">
                            <span class="bd-tt">支付方式</span><span class="bd-txt">{{ $ticket['trade_type']  }}</span>
                        </div>
                        <div class="item-bd">
                            <span class="bd-tt">实际支付</span><span class="bd-txt">{{ $ticket['real_pay'] }}元</span>
                        </div>
                        <div class="item-bd">
                            <span class="bd-tt">购票时间</span><span class="bd-txt">{{ \Carbon\Carbon::createFromTimestamp($ticket['create_time'])->format('H:i')  }}</span>
                        </div>
                    </div>
                    <p class="ticket-line-gap"></p>
                    <div class="bt-footer">
                        <p class="date">预订日期：{{ \Carbon\Carbon::createFromTimestamp($ticket['create_time'])->format('Y-m-d ')  }} {{ \App\Tools\FuncTools::getWeekTitle($ticket['create_time']) }}</p>

                        @if( !empty($ticket['refundable']) )
                            @if( $ticket['status'] == \App\Models\Enums\ShuttleTicketStatusEnum::UnUsed )
                            <p class="agree">
                                <input id="read_btn" type="checkbox"  class="read-checkbox" value="1">
                                <label for="read_btn">我已阅读</label>
                                <a href="http://m.hollo.cn/notice/ticket_refund.html" class="color-orange">退票须知</a>
                            </p>
                            <button class="btn full-width bt-btn disabled" id="js_refund_btn" data-id="{{ $ticket['ticket_id'] }}">申请退票</button>
                            @elseif( $ticket['status'] == \App\Models\Enums\ShuttleTicketStatusEnum::Expired )
                                <button class="btn full-width bt-btn disabled" >已经过期</button>
                            @elseif( $ticket['status'] == \App\Models\Enums\ShuttleTicketStatusEnum::Checked )
                                <button class="btn full-width bt-btn disabled" >已完成</button>
                            @elseif( $ticket['status'] == \App\Models\Enums\ShuttleTicketStatusEnum::Refund )
                                <button class="btn full-width bt-btn disabled" >已退票</button>
                            @endif
                        @else
                            <p class="month">快捷巴士不支持退票，一经售出，不可退换</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </main>
@stop

