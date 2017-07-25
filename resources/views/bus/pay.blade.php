@extends('layouts.default')
@section('title', '预定车票')
@inject('funcTools', 'App\Tools\FuncTools')
{{-- mobiscroll  mmenu  --}}
<link rel="stylesheet" href="/bower_components/datepicker/css/mobiscroll.custom-3.0.0-beta5.min.css"/>
<link rel="stylesheet" href="/styles/custom-mobiscroll.css"/>

{{--内容区域--}}
@section('content')
    <main id="page "  class="pb-100">
        <!--线路名称-->
        <header class="bus-detail-header js_up_down_click" >
            <div class="bdh-content">
                <div>
                    <p>
                        <span>{!! $funcTools::secureOutput($line, 'line_code') !!}</span>
                    </p>
                </div>
                <div>
                    <p>{!! $funcTools::secureOutput($line, 'line_name') !!}</p>
                    <p class="color-orange">票价：{!! $line['is_discount'] == 0 ? $funcTools::secureOutput($line, 'price') : $funcTools::secureOutput($line, 'discount_price') !!}元</p>
                </div>
            </div>
            <p class="bdh-footer arrow down js_up_down_btn"></p>
        </header>                                                                                                                                                          
        <!--线路站点-->
        @if( !empty($stations) )
            <ul class="bus-station-list gone js_up_down_section" data-line-id="{{ $line['line_id']  }}">
                @foreach($stations as $station)
                    <li class="bsl-item js_station_item" data-id="{{ $station['station_id']  }}">
                        <div class="item-content">
                            {{  $station['short_name'] }}
                        </div>

                        <div class="item-time bus-after-v">
{{--                            预计 {{  $station['arrived_at'] }}--}}
                        </div>
                    </li>
                  @endforeach
            </ul>
        @endif
        @if( $line['status'] != \App\Models\Enums\BusLineStatusEnum::Not_Operation )
            {{--班次--}}
            <div class="bus-shift clearfix">
                <div class="title icon-time">
                    乘坐班次
                </div>
                <ul class="shifts clearfix">
                    @if( isset($shiftMap) )
                        @foreach(array_values($shiftMap) as $index => $shift)
                            <li class="{{ $index ==0 ?'active' :''  }} {{ $index>2 ? 'gone' :'' }}" data-id="{{  $shift['line_frequency_date'] }}">{{  $shift['line_frequency_date'] }}</li>
                        @endforeach
                    @endif
                </ul>
                @if( count($shiftMap)>3 )
                    <span class='more bus-after-v js_more_btn' data-info="{{  json_encode(array_keys($shiftMap)) }}" >更多</span>
                @endif
            </div>
            {{--$type 0 没有月票和日票 1 只有月票 2只有日票 3月票日票都有--}}
            {{--0：可预约，1：已预约，2:满员，3:即将开放--}}
        <!--买票-->
            <div class="bus-pick-ticket {{ $type ==0 || $line['status']==3 ? 'gone': '' }}">
                <header class="{{ in_array($type, [3]) ?  '' : 'gone' }}">
                    @if(!in_array($type, [0,1]))
                        <div class="js_ticket_pick_btn  {{ in_array($type, [2,3]) ? 'active' : 'gone' }}" data-type="day">日票</div>
                    @endif
                    @if(!in_array($type, [0,2]))
                        <div class="js_ticket_pick_btn  {{ in_array($type, [1]) ? 'active' : '' }} {{ in_array($type, [0,2]) ? 'gone' : '' }}" data-type="month">月票</div>
                    @endif
                </header>
                @if(!in_array($type, [0,1]))
                    <div class="js_ticket_pick {{ in_array($type, [2,3]) ? '' : 'gone' }}" >
                        <table class="hlm4-calendar">
                            <thead>
                            <tr>
                                <th class="disabled">日</th>
                                <th>一</th>
                                <th>二</th>
                                <th>三</th>
                                <th>四</th>
                                <th>五</th>
                                <th class="disabled">六</th>
                            </tr>
                            </thead>
                            <tbody id="js_calendar_section">

                            </tbody>
                        </table>
                    </div>
                @endif
                @if(!in_array($type, [0,2]))
                    <div class="js_ticket_pick {{ in_array($type, [3]) ? 'gone' : '' }}">
                        @if( !empty( $monthlySchedule ) )
                            <div  class="hlm4-pick-m-ticket active {{  $monthlySchedule['frequency_status'][0]['status'] != 3 ? 'active' : '' }}"  >
                                <div class="hml4-pmt-info">
                                    <p class="hml4-pmti-date">
                                        <span>{{ $monthlySchedule['month']  }}月</span>
                                        <span>月票</span>
                                        <span>{{ $monthlySchedule['days']  }}天</span>
                                    </p>
                                    <p class="hml4-pmti-price">
                                        <span><sub>￥</sub><span class="js_month_price">{{ $monthlySchedule['frequency_status'][0]['price']  }}</span></span>
                                        <span >原价：￥{{ $monthlySchedule['origin_price']  }}</span>
                                    </p>
                                    <p class="hml4-pmti-hint">{{ $monthlySchedule['desc']  }}</p>
                                </div>
                                <div class="hml4-pmt-select js_month_status">
                                    {{--#状态  1已选 2已购 3售罄--}}
                                    @if( $monthlySchedule['frequency_status'][0]['status'] == 1 )
                                        <span class="js_month_status_title">已选</span>
                                    @elseif( $monthlySchedule['frequency_status'][0]['status'] == 2 )
                                        <span class="js_month_status_title">已购</span>
                                    @elseif( $monthlySchedule['frequency_status'][0]['status'] == 3 )
                                        <span class="js_month_status_title">售罄</span>
                                    @endif
                                    <span>{{ $monthlySchedule['deadline']  }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <p  class="color-orange pl-10 pr-10 js_day_title">
                您本月已购 {{  $paidTicketCount['current'] }} 张车票
            </p>
            @if( !empty( $monthlySchedule ) && $line['monthly_support'] ==1 && $monthlySchedule['frequency_status'][0]['status'] != 2 )
                <p  class="color-orange pl-10 pr-10 js_month_title {{ in_array($type, [0,2,3])  ? 'gone' : '' }}">
                    购买{{ $monthlySchedule['month'] }}月月票，{{ $monthlyDesc }}，共计节省{{ $monthlySchedule['origin_price'] - $monthlySchedule['frequency_status'][0]['price']  }}元
                </p>
            @endif

            <ul class="pick-ticket-info ">
                <label for="js_seat_num" class="border-bottom display-flex">
                <li >
                    <div class="icon-ticket-base seat">我的座位</div>
                    <div class="seat-num-2">
                        <input style="padding-left: 0;"  type="text" placeholder="随机" readonly id="js_seat_num" data-seat="-1" value="" >
                    </div>

                </li>
                </label>
                <li>
                    <div class="icon-ticket-base up-station">上车站点</div>
                    <div>
                        <input type="hidden" placeholder="请选择" id="js_up_station" value="" >
                    </div>
                </li>
                <li>
                    <div class="icon-ticket-base down-station" >下车站点</div>
                    <div>
                        <input type="hidden" placeholder="请选择" id="js_down_station" >
                    </div>
                </li>
            </ul>
        @else
             <div class="empty-bus">
                 <div class="eb-content">暂未运营</div>
             </div>
        @endif


        <div class="bus-buy-footer">
            <div class="bbf-body">
                <a href="{{ \App\Models\Enums\SettingEnum::transform(\App\Models\Enums\SettingEnum::Ticket_Policy_URL) }}" class="body-left icon-ticket-help">购票规则</a>
                <div class="body-bd">
                    <span class="count">共计 <span class="js_ticket_count">0</span> 张</span>
                    <span class="price-info">票价<span class="price "> <span class="js_ticket_price">0</span> 元</span></span>
                </div>
            </div>
            <button class="btn btn-primary text-center full--width bbf-btn jd_create_order_btn {{  $line['status']==\App\Models\Enums\BusLineStatusEnum::Not_Operation ? 'disabled' : '' }}" >立即购买</button>
        </div>

        <div id="js_stations_data" data-info="{{ json_encode($stations) }}"></div>
        <div id="js_shifts_data" data-info="{{ json_encode($shifts) }}"></div>
        <div id="js_shifts_map" data-info="{{ json_encode($shiftMap) }}"></div>
        <div id="js_line_data" data-info="{{ json_encode($line) }}"></div>
        <div id="js_month_ticket_data" data-info="{{ json_encode($monthlySchedule) }}"></div>
        <div id="js_month_price_rule" data-info="{{ json_encode($monthlyPriceRule) }}"></div>
        <div id="js_paid_ticket_count" data-info="{{ json_encode($paidTicketCount) }}"></div>
        <input id="apply_monthly_price_rule"  type="hidden" value="{{ $apply_monthly_price_rule }}">
        <input id="js_default_shift"  type="hidden" value="{{ $defaultShift }}">
    </main>
@stop

@include('templates.pay_modal',[])
@include('templates.coupon',[])
@include('templates.seat',[])

