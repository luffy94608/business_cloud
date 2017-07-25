@extends('layouts.default')
@section('title', '车票详情')

{{--内容区域--}}
@section('content')
    <main id="page" >
        <div id="js_drop_load_area">
            <div class="bus-ticket-list" id="list" >
                <div class="bt-item">
                    <div class="bt-header text-center">
                        <span class="name">{{ $ticket['line_code']  }}</span>
                        <span class="name ml-5">班车车票</span>
                        {{--<span class="icon-location " ></span>--}}
                    </div>
                    <div class="ticket-gap"></div>
                    <div class="bt-detail">
                        <div class="item-bd">
                            <span class="bd-tt">上车站点</span><span class="bd-txt">{{ $ticket['departure_name']  }}</span>
                        </div>
                        <div class="item-bd">
                            <span class="bd-tt">下车站点</span><span class="bd-txt">{{ $ticket['destination_name']  }}</span>
                        </div>
                        <div class="item-bd">
                            <span class="bd-tt">乘车日期</span><span class="bd-txt">{{ $ticket['dept_date']  }}</span>
                        </div>
                        <div class="item-bd">
                            <span class="bd-tt">乘车时间</span><span class="bd-txt">{{ $ticket['dept_time']  }}</span>
                        </div>
                        <div class="item-bd">
                            <span class="bd-tt">车牌号</span><span class="bd-txt">{{ empty($ticket['plate']) ? '未知' :$ticket['plate']  }}</span>
                        </div>
                        <div class="item-bd">
                            <span class="bd-tt">座位号</span><span class="bd-txt color-orange">{{ $ticket['seat']  }}</span>
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
                            <span class="bd-tt">购票时间</span><span class="bd-txt">{{ \Carbon\Carbon::createFromTimestamp($ticket['date'])->format('H:i')  }}</span>
                        </div>
                    </div>
                    <p class="ticket-line-gap"></p>
                    <div class="bt-footer">
                        <p class="date">预订日期：{{ \Carbon\Carbon::createFromTimestamp($ticket['date'])->format('Y-m-d ')  }} {{ \App\Tools\FuncTools::getWeekTitle($ticket['date']) }}</p>
                            @if ($ticket['status'] == \App\Models\Enums\TicketStatusEnum::UnPaid)
                                <button class="btn full-width bt-btn" >待支付</button>
                            @elseif( $ticket['status'] == \App\Models\Enums\TicketStatusEnum::UnUsed )
                                @if( $ticket['type'] == \App\Models\Enums\TicketMonthTypeEnum::Day )
                                    @if( \App\Tools\FuncTools::canRefundStatus($ticket['dept_at'], $ticket['refund_ahead_in_seconds']) )
                                        <p class="agree">
                                            <input id="read_btn" type="checkbox"  class="read-checkbox" value="1">
                                            <label for="read_btn">我已阅读</label>
                                            <a href="{{ \App\Models\Enums\SettingEnum::transform(\App\Models\Enums\SettingEnum::Refund_Policy_URL) }}"  class="color-orange">退票须知</a>
                                        </p>
                                        <button class="btn full-width bt-btn disabled" id="js_refund_btn" data-dept-at="{{ $ticket['dept_at'] }}" data-ahead="{{ $ticket['refund_ahead_in_seconds'] }}" data-id="{{ $ticket['id'] }}">申请退票</button>
                                    @else
                                        <button class="btn full-width bt-btn disabled" >不可退票</button>
                                    @endif
                                @else
                                    <p class="month">月票不支持退票，一经售出，不可退换</p>
                                @endif

                            @elseif( $ticket['status'] == \App\Models\Enums\TicketStatusEnum::WaitRemark )
                                <button class="btn full-width bt-btn js_remark_btn" data-id="{{ $ticket['id'] }}">评价</button>
                            @elseif( $ticket['status'] == \App\Models\Enums\TicketStatusEnum::Finished )
                                {{--<button class="btn full-width bt-btn disabled" >已完成</button>--}}
{{--                                @include('templates.remark',['ticket'=>$ticket])--}}
                            @elseif( $ticket['status'] == \App\Models\Enums\TicketStatusEnum::Refund )
                                <button class="btn full-width bt-btn disabled" >已退票</button>
                            @endif

                    </div>
                </div>

            </div>
            @if( $ticket['status'] == \App\Models\Enums\TicketStatusEnum::Finished )
                @include('templates.remark',['ticket'=>$ticket, 'commentItems'=>isset($ticket['comment']['items']) ? $ticket['comment']['items'] : false])
            @endif
        </div>

    </main>
@stop

