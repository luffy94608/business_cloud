/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        buyNode : '.js_buy_btn',
        template : require('art-template'),
        loading :false,
        payLoading :false,
        countInput :$('#js_input_count'),
        paySection :$('.js_pay_section'),
        maxTicketCount :0,
        listSection:$('#list'),
        couponSelectObj : '',
        fee : 0,
        ticketCount : 0,
        contract : $('#js_contract_data').data('info'),
        balanceCost : 0,
        wechatCost : 0,
        arrivedAtStr : '',
        balanceType : false,
        wechatType : false,
        initShowTicketTime : Math.floor((new Date().getTime()/1000)),
        couponSelectSection : $('#js_coupon_select'),
        cashSelectSection : $('#js_cash_select'),
        wechatSelectSection : $('#js_wechat_select'),

        /**
         * 买票加减计算事件
         */
        initCountBtnEvent:function () {
            var mpBtn = $('.js_mp_btn');
            mpBtn.unbind().bind('click', function () {
                var $this = $(this);
                var val = parseInt(init.countInput.val());
                var addStatus = $this.hasClass('plus');
                if (addStatus) {
                    ++val;
                } else {
                    --val;
                }
                if (val<1 || val>init.maxTicketCount) {
                    return false;
                }

                $this.siblings().removeClass('disabled');
                if (val == init.maxTicketCount ||  val == 1) {
                    $this.addClass('disabled');
                }
                init.countInput.val(val);
                init.updatePayTypeStatus();
            });
            
            if (init.maxTicketCount < 2) {
                mpBtn.addClass('disabled');
                init.countInput.val(1);
            }
        },
        /**
         * 支付区域高度调整
         */
        initScreenFix:function () {
            var windowHeight = $(window).height();
            var contentHeight = $('#page').height();
            // if (windowHeight> contentHeight) {
            //     init.paySection.addClass('fixed-bottom');
            // } else {
            //     init.paySection.removeClass('fixed-bottom');
            // }
            init.paySection.removeClass('v-hidden');

        },
        /**
         * 跳转车票页面
         */
        toTicketList : function () {
            var params = {
                contract_id:init.contract.contract_id
            };
            $.wpost($.httpProtocol.GET_PAID_SHUTTLE_TICKET,params,function (data) {
                if (data && data.ticket && data.ticket.length) {
                    $.cache.set($.cacheLastShuttleTicketKey, data.ticket);
                }
                if ($.getQueryParams('src') === 'map') {
                    window.history.go(-1);
                } else {
                    window.history.go(-2);
                }
            },function () {
            }, true, true);
        },

        /**
         * 通知支付结果
         * @returns {boolean}
         */
        notifyPay:function (tradeNo, status) {
            var params = {
                trade_no : tradeNo,
                status :status ? 'success': 'failed'
            };
            $.wpost($.httpProtocol.PAY_NOTIFY,params,function (data) {

                if(!status){//失败
                    init.payLoading = false;
                }else{//成功
                    $.showToast($.string.PAY_SUCCESS);
                    setTimeout(function () {
                        init.toTicketList();
                    }, 300);
                }
            },function () {
                init.payLoading = false;
            });
        },

        /**
         * 初始化微信支付
         */
        initJsPay:function (sign) {
            wx.chooseWXPay({
                timestamp: sign.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                nonceStr: sign.nonceStr, // 支付签名随机串，不长于 32 位
                package: sign.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
                signType: sign.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                paySign: sign.sign, // 支付签名
                success: function (res) {
                    // 支付成功后的回调函数
                    var status = false;
                    if(res.errMsg == "chooseWXPay:ok" ) {
                        status = true;
                        init.notifyPay(sign.out_trade_no,status);
                        // $.showToast($.string.PAY_SUCCESS,true);
                    }else{
                        init.payLoading = false;
                        // $.showToast($.string.PAY_FAILED,false);
                    }
                },
                cancel:function(){
                    // init.notifyPay(sign.out_trade_no,false);
                    init.payLoading = false;
                },
                error:function(res){
                    // init.notifyPay(sign.out_trade_no,false);
                    $.showToast($.string.PAY_ERROR,false);
                    init.payLoading = false;
                }
            });
        },
        /**
         * 计算优惠后支付的费用
         */
        updatePayTypeStatus : function(balanceType, wechatType){
            //处理优其他tab
            var data = init.contract;
            var ticketCount = parseInt(init.countInput.val());
            var cost= (parseFloat(init.contract.price) * ticketCount).toFixed(2);
            var balance=init.contract.cash_account?init.contract.cash_account.my_balance:0;
            var couponInfo = init.couponSelectObj.getSelectedVal();
            var discountValueWithCoupon=0;//优惠券抵扣金额
            var discountTitle = '';
            if(couponInfo){
                var couponVal=couponInfo.value;
                var couponType=couponInfo.type;//0次券 1金额券
                discountValueWithCoupon=couponType==1? couponVal : init.contract.price*1;
                discountValueWithCoupon=parseFloat(discountValueWithCoupon).toFixed(2);
                if (discountValueWithCoupon>cost) {
                    discountValueWithCoupon = cost;
                }
                discountTitle = $.string.COUPON_DISCOUNT.format(discountValueWithCoupon);
            } else {
                discountTitle = $.string.COUPON_NOT_USED;
            }
            if (init.contract.can_choose_coupons == 0) {
                discountTitle = $.string.COUPON_NOT_SUPPORT;
                init.couponSelectSection.addClass('disabled');
            } else {
                init.couponSelectSection.removeClass('disabled');
            }
            init.couponSelectSection.find('.js_title').html(discountTitle);

            if(typeof balanceType=='boolean'){
                init.balanceType=!!balanceType;

                if(init.balanceType){//使用余额
                    if(discountValueWithCoupon>=cost){
                        init.balanceType=false;
                        init.wechatType=false;
                        if(discountValueWithCoupon==cost && cost==0){
                            init.balanceType=true;
                        }
                    }else{
                        if(balance>0){
                            init.balanceType=true;
                            if((cost-discountValueWithCoupon).toFixed(2)<balance){
                                init.wechatType=false;
                            }else{
                                init.wechatType=true;
                            }
                        }else{
                            init.balanceType=false;
                            init.wechatType=true;
                        }

                    }
                }else{//不使用余额
                    if(discountValueWithCoupon>=cost){
                        init.wechatType=false;
                    }else{
                        init.wechatType=true;
                    }
                }

            }else{
                if(discountValueWithCoupon){//使用优惠券
                    if(discountValueWithCoupon>=cost){//优惠券够支付
                        init.balanceType=false;
                        init.wechatType=false;
                    }else{//不够支付
                        if(balance>0){
                            if(balance>=(cost-discountValueWithCoupon).toFixed(2)){
                                init.balanceType=true;
                                init.wechatType=false;
                            }else{
                                init.balanceType=true;
                                init.wechatType=true;
                            }

                        }else{
                            init.balanceType=false;
                            init.wechatType=true;
                        }

                    }

                }else{//不适用优惠券
                    if(balance>0){
                        if(balance>=cost){
                            init.balanceType=true;
                            init.wechatType=false;
                        }else{
                            init.balanceType=true;
                            init.wechatType=true;
                        }
                    }else{
                        init.balanceType=false;
                        init.wechatType=true;
                    }
                }

            }

            var cashInput = $('#js_cash_type');
            var switchBtn = cashInput.siblings('.icon-switch');
            cashInput.prop('checked', init.balanceType);
            if (init.balanceType) {
                switchBtn.addClass('active');
            } else {
                switchBtn.removeClass('active');
            }
            $('#js_wechat_type').prop('checked', init.wechatType);

            init.cashSelectSection.find('.js_count').html(data.cash_account.my_balance);
            var couponTotal = data.coupons ? data.coupons.count : 0;
            if (couponTotal ==0 ){
                init.couponSelectSection.addClass('disabled');
            }
            init.couponSelectSection.find('.js_count').html(couponTotal);
            init.balanceCost = (balance > (cost - discountValueWithCoupon)) ? cost -discountValueWithCoupon : balance;
            if (init.balanceType) {
                // init.cashSelectSection.show();
            } else {
                init.balanceCost = 0;
                // init.cashSelectSection.hide();
            }
            if (init.wechatType) {
                // init.wechatSelectSection.show();
            } else {
                // init.wechatSelectSection.hide();
            }
            init.wechatCost = cost - init.balanceCost - discountValueWithCoupon;
            init.wechatCost =  (init.wechatCost <0 ? 0 :init.wechatCost).toFixed(2);
            init.balanceCost = init.balanceCost.toFixed(2);
            var cashTitle = '';

            if (balance == 0) {
                cashTitle = $.string.PAY_NOT_AVAILABLE;
            }

            if (init.balanceCost>0 || (init.fee == 0)) {
                cashTitle = $.string.PAY_DISCOUNT.format(init.balanceCost);
            }else {
                cashTitle = $.string.PAY_NOT_USE;
            }
            init.cashSelectSection.find('.js_title').html(cashTitle);
            var wechatTitle = '';
            if (init.wechatCost>0) {
                wechatTitle = $.string.PAY_WECHAT_DISCOUNT.format(init.wechatCost);
            }
            init.wechatSelectSection.find('.js_title').html(wechatTitle);
            $('.js_pay_fee').html(cost);
        },

        initBtnEvent : function () {
            init.paySection.find('.js_pay_btn').addClass('disabled');
            var data = init.contract;
            init.maxTicketCount = parseInt(init.contract.max_can_buy);
            init.fee = parseFloat(init.contract.price);

            //获取默认优惠券
            var couponDefault = '';
            if (data.can_choose_coupons == 1 && data.coupons && data.coupons.suggest && data.coupons.suggest.coupon) {
                couponDefault = data.coupons.suggest.coupon;
            }
            /**
             * 优惠券
             */
            init.couponSelectObj = $('#js_coupon_select').initCouponModal({
                fee:init.fee,
                coupon : couponDefault,
                contractId: init.contract.contract_id,
                change:function (coupon) {
                    init.updatePayTypeStatus();
                    $('#js_coupon_type').prop('checked', (coupon ? true : false));
                    init.paySection.find('.js_pay_btn').removeClass('disabled');
                    init.paySection.removeClass('v-hidden');

                }
            });
            // init.updatePayTypeStatus();
            //支付方式切换事件
            $('.js_pay_type').unbind().bind('change', function () {
                var $this = $(this);
                var checked = $this.prop('checked');
                var name = $this.attr('name');
                var  balanceType ='',
                    wechatType = '';
                if (name == 'wechat_type') {
                    wechatType = checked;
                } else {
                    balanceType = checked;
                }
                init.updatePayTypeStatus(balanceType, wechatType);
            });

            //支付
            init.paySection.find('.js_pay_btn').unbind().bind('click',function () {
                if ($(this).hasClass('disabled')) {
                    return false;
                }
                if(init.payLoading){
                    return false;
                }
                var contract = init.contract;
                var couponId = '';
                var coupon =  init.couponSelectObj.getSelectedVal();
                if (coupon && coupon.coupon_id) {
                    couponId = coupon.coupon_id;
                }
                var ticketCount = parseInt(init.countInput.val());
                var params = {
                    id : contract.contract_id,
                    count : ticketCount,
                    use_balance : (init.balanceCost>0 || init.fee*ticketCount ==0) ? 1 : 0,
                    use_3rd_trade : init.wechatCost>0 ? 1 : 0,
                    use_coupon : couponId.length ? 1: 0,
                    coupon_id : couponId
                };
                if (params.count<1) {
                    $.showToast($.string.TICKET_COUNT_MUST, false);
                    return false;
                }
                init.payLoading = true;
                $.wpost($.httpProtocol.PAY_SHUTTLE,params,function (data) {
                    //直接付款成功（0 元支付）

                    if(params.use_3rd_trade==1 && init.fee != 0){//微信支付
                        if(data.wechat && data.wechat.sign){
                            init.initJsPay(data.wechat.sign);
                        }else{
                            $.showToast($.string.WECHAT_PAY_UP_FAILED, false);
                            init.payLoading = false;
                        }
                    } else {
                        $.showToast($.string.PAY_SUCCESS);
                        setTimeout(function () {
                            init.toTicketList();
                        }, 300);
                    }
                },function () {
                    init.payLoading = false;
                });
            });

        },
        run : function () {
            //搜索
            init.initBtnEvent();
            init.initCountBtnEvent();
            init.initScreenFix();

            wx.ready(function(){
                wx.hideOptionMenu();
            });

        }
    };
    init.run();
})($);