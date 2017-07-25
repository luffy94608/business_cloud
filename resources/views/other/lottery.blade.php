@extends('layouts.default')
@section('title', '幸运大抽奖')

{{--内容区域--}}
@section('content')
    <link rel="stylesheet" href="/styles/activity.css">
    <main id="page" >
        <div class="page-loading">
            <div class="pl-content">
                <span class="flower-loader"></span>
            </div>
        </div>

        <img src="/images/activity/header.png" width="100%">
        <header class="title-1 text-center relative">
            抽奖说明
            <i class="h-icon icon-msg-info fixed-right"></i>
        </header>
        <section class="detail">
            <h4 class="title-2">1、抽奖规则：</h4>
            <div class="content">① 哈罗用户请关注“哈罗班车”，智享用户请关注“智享出行”微信公众号；
                ② 每个微信ID每天每次抽奖仅限一次；
                ③ 奖品为班车/快捷巴士优惠券。
            </div>
        </section>

        <section class="detail">
            <h4 class="title-2">2、领奖规则：</h4>
            <div class="content" >① 中奖后，您将获得优惠券兑换码码，请于3日内兑换，过期将无法兑换；
                ② 兑换流程：
                哈罗用户请在【哈罗同行app或微信公众号】内“我的钱包-优惠券”中进行兑换；
                智享用户请在【智享微信公众号】内“我的智享-我的账号-优惠券”中进行兑换；
                ③ 优惠码不折现、不补码；
                ④ 兑换后可得优惠券一张，可抵班车/快捷巴士车票现金一次，仅供购票时使用；
                ⑤ 优惠码所得优惠券限一次性使用，有效期限为30天，过期作废，不折现、不补券。如使用后发生退款事宜，优惠券不作为退款款项，不补发优惠券；
                ⑥ 月票用户抽中，不折现、不抵扣月票金额，但可转让其他用户；
                ⑦ 中奖号码请妥善保管，如有泄露被他人兑换，办公司不承担补发责任。
            </div>
        </section>

        <section class="award-section  text-center " id="js_raffle_content">
            {!! $contentHtml !!}
        </section>

        {{--<footer class="footer title-3 text-center">【最终解释权归哈罗同行所有】</footer>--}}
        <img src="/images/activity/footer.png" width="100%">

        <!--中奖结果-->
        <section class="dialog-wrap " id="js_dialog_section">
            <div class="overlay"></div>
            <div class="dialog-table">
                <div class="dialog-cell ">

                </div>
            </div>
        </section>

        <!--中奖列表-->
        <section class="dialog-wrap " id="js_dialog_list_section">
            <div class="overlay"></div>
            <div class="dialog-table">
                <div class="dialog-cell ">
                    <!-- 中奖列表 -->
                    <div class="dialog pd-10">
                        <i class="h-icon icon-close "></i>
                        <div class="lighter-white">
                            <img src="/images/activity/window_list_2.png" width="100%">
                            <table class="lottery-list">
                                {!! $listHtml !!}
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!--loading -->
        <section class="toast-wrap " id="js_loading_toast_section">
            <div class="toast toast--loading has-close">
                <svg class="icon-svg icon-loading">
                    <use xlink:href="/images/icons.svg#icon-loading"></use>
                </svg>
                <p class="toast-text">抽奖中...</p>
            </div>
        </section>
    </main>
@stop

