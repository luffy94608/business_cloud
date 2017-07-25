@extends('layouts.default')
@section('title', '支付车票')
@section('bodyBg', 'bg-white')

{{--内容区域--}}
@section('content')
    <main id="page" class="bg-grey">
        
        <div class="shuttle-buy-section">
            <div class="sb-header">
                <p>{{ \Carbon\Carbon::now()->format('m月d日') }}</p>
                <p class="title">{{ $contract['line_code'] }}</p>
            </div>
            <img class="sb-gap" src="/images/tk-gap@2x.png">
            <div class="sb-body">
                <p>当天有效，过期作废，不支持退票。</p>
                <p>一票一人，上车时请主动向检票员出示车票。</p>
            </div>
            <div class="sb-footer clearfix">
                <div class="fl color-orange">票价：{{ $contract['price'] }}元</div>
                <div class="sb-count fr">
                    <a href="javascript:void(0);" class="minus js_mp_btn disabled">  </a>
                    <input type="text" value="1" readonly id="js_input_count" />
                    <a href="javascript:void(0);" class="plus js_mp_btn " ></a>
                </div>
            </div>
        </div>

        <div class="bg-white border-top v-hidden js_pay_section">
            <h3 class="text-center font-18 pb-5 pt-5">
                <span class="js_shift_title">支付方式</span>
            </h3>
            <ul class="bus-pay-type mb-15">
                <li id="js_coupon_select">
                    <label for="js_coupon_type">
                        <div>
                            优惠券<span class="hint">共<span class="js_count">0</span>张</span>
                        </div>
                        <div class="py-right ">
                            <span class="js_title color-orange"></span>
                            <input type="checkbox" value="0" id="js_coupon_type" name="coupon_type" class="show-pigeon">
                        </div>
                    </label>
                </li>
                <li id="js_cash_select">
                    <label for="js_cash_type">
                        <div>
                            {{ \App\Models\Enums\SettingEnum::transform(\App\Models\Enums\SettingEnum::App_Name_Simple) }}币 <span class="hint"><span class="js_count">0</span>元</span>
                        </div>
                        <div class="py-right">
                            &nbsp;                        <span class="color-orange"><span class="js_title"></span></span>
                            <i class="icon-switch"></i>
                            <input type="checkbox" value="0" id="js_cash_type" name="cash_type" class="show-pigeon js_pay_type gone">
                        </div>
                    </label>
                </li>

                <li id="js_wechat_select">
                    <label for="js_wechat_type">
                        <div>
                            <i class="icon-wechat"></i>微信支付
                        </div>
                        <div class="py-right">
                            <span class="color-orange"><span class="js_title"></span></span>
                            <input type="checkbox" value="0" id="js_wechat_type" name="wechat_type" class="show-pigeon js_pay_type">
                        </div>
                    </label>
                </li>
            </ul>
            <div class="pl-15 pr-15">
                <button class="btn btn-primary text-center full-width font-16 js_pay_btn">
                    确认支付
                    {{--<span class="ml-5 mr-5 color-orange js_pay_fee">0</span>元--}}
                    <span class="btn-time-show color-orange js_pay_dead_second"></span>
                </button>
            </div>


        </div>
        <div class="gone" id="js_contract_data" data-info="{{ json_encode($contract) }}"></div>
    </main>
    @include('templates.coupon',[])
@stop

