{{--支付modal--}}
<section class="dialog-wrap " id="js_pay_modal" >
    <div class="overlay"></div>
    <div class="dialog-content">
        <div class="dialog white fixed-bottom">
            <h3 class="text-center font-18 pb-5 pt-5">
                <span class="js_shift_title">支付方式</span>
                <svg class="icon-svg fxWH-25 fr mt-5 js_cancel_btn" style="position: absolute;right: 15px;">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/images/icons.svg#icon-close"></use>
                </svg>
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
&nbsp;                          <span class="color-orange"><span class="js_title"></span></span>
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
            <button class="btn btn-primary text-center full--width font-16 js_pay_btn">
                确认支付
                {{--<span class="ml-5 mr-5 color-orange js_pay_fee">0</span>元--}}
                <span class="btn-time-show color-orange js_pay_dead_second"></span>
            </button>

        </div>
    </div>
</section>