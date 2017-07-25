/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        moreBtnNode : '.js_more_btn',
        seatNumInputNode : '#js_seat_num',
        upStationNode : '#js_up_station',
        downStationNode : '#js_down_station',
        createOrderBtn : $('.jd_create_order_btn'),
        loading :false,
        ticketLoading :false,
        cancelLoading :false,
        payLoading :false,
        firstIn :true,
        applyMonthPriceRule :$('#apply_monthly_price_rule').val() == 1 ? true : false,
        monthPriceRule :$('#js_month_price_rule').data('info'),
        currentShift : $('.shifts>li.active').data('id'),
        defaultShift : $('#js_default_shift').val(),
        stationData : $('#js_stations_data').data('info'),//站点数据
        shiftStations : '',//选中班次站点数据
        shiftsData : $('#js_shifts_data').data('info'),//调度聚合data
        shiftsMap : $('#js_shifts_map').data('info'),//班次信息
        line : $('#js_line_data').data('info'),//线路信息
        monthTicket : $('#js_month_ticket_data').data('info'),//
        monthStatusTitleMap : {1:'已选',2:'已购',3:'售罄'},//
        paidTicketCount : $('#js_paid_ticket_count').data('info'),
        shiftModalEvent : '',
        calendarObj : '',
        seatObj : '',
        couponSelectObj : '',
        fee : 0,
        ticketCount : 0,
        contract : '',
        balanceCost : 0,
        wechatCost : 0,
        arrivedAtStr : '',
        contractId : '',
        balanceType : false,
        wechatType : false,
        initShowTicketTime : Math.floor((new Date().getTime()/1000)),
        couponSelectSection : $('#js_coupon_select'),
        cashSelectSection : $('#js_cash_select'),
        wechatSelectSection : $('#js_wechat_select'),
        payModal : $('#js_pay_modal'),

        /**
         * 倒计时
         * @param value
         * @returns {boolean}
         */
        initWaitingSecond : function (value) {
            var opts = {
                target:$('.js_pay_dead_second'),
                defaultStr:'',
                initStartTimeKey:'js_pay_end_time',
                waitingSecond :120,
                interval :'',
                getRemainSec : function (status) {
                    var startTime = $.cookie(opts.initStartTimeKey);
                    var sec = startTime ? parseInt(startTime) : 0;
                    return sec>0 ? sec : 0;
                },
                running : function () {
                    var str = '';
                    var remainSeconds = opts.getRemainSec();
                    if(remainSeconds<=0){
                        $.cookie(opts.initStartTimeKey,0);
                        clearInterval(init.interval);
                        str = opts.defaultStr;
                        init.cancelOrder();
                    }else{
                        var minute = Math.floor(remainSeconds/60);
                        if (minute>0) {
                            minute = minute<10 ? '0'+minute : minute;
                            str += minute+' 分';
                        }
                        var seconds = remainSeconds%60;
                        seconds = seconds<10 ? '0'+seconds : seconds;
                        str += seconds+' 秒';
                    }
                    $.cookie(opts.initStartTimeKey,remainSeconds-1);
                    opts.target.html(str);
                }
            };

            if(typeof value != 'undefined'){
                if(value == 'status'){
                    return  (opts.getRemainSec() <0) ? false : true;
                }else{
                    $.cookie(opts.initStartTimeKey,value);
                }
            }

            opts.running();
            if(opts.getRemainSec() <=0){
                return false;
            }

            init.interval = setInterval(function () {
                opts.running();
            },1000);

        },

        /**
         * 获取车票类型
         */
        getTicketType : function () {
            return $('.js_ticket_pick_btn.active').data('type')
        },
        /**
         * 计算当前总费用
         */
        calcToTalFee : function(){
            var type = init.getTicketType();
            init.fee = 0;
            if (type == 'day') {
                  var price = init.line.is_discount ? init.line.discount_price : init.line.price;
                  price = parseFloat(price);
                  var scheduleIds = init.calendarObj.getVal(true);
                  var  len = scheduleIds.length;
                init.ticketCount = len;
                if (init.applyMonthPriceRule) {
                    for(var i=0;i<len;i++){
                        var tmpItem=scheduleIds[i];
                        var currentNum;
                        var tmpFee=0;
                        if(tmpItem.isCurrentMonth){
                            currentNum= init.paidTicketCount.current+i+1;
                            init.monthPriceRule.current.forEach(function(val,key){
                                if(currentNum>=val.begin && currentNum<=val.end){
                                    tmpFee=price*val.value;
                                }
                            });
                        }
                        if(tmpItem.isNextMonth){
                            currentNum= init.paidTicketCount.next+i+1;
                            init.monthPriceRule.next.forEach(function(val,key){
                                if(currentNum>=val.begin && currentNum<=val.end){
                                    tmpFee=price*val.value;
                                }
                            });
                        }
                        if(!tmpFee){
                            tmpFee=price;
                        }
                        init.fee+=tmpFee;
                    }
                } else {
                    init.fee = len*price;
                }

            } else {
                var monthTicketStatus = init.getMonthTicketStatus(init.currentShift);
                init.fee = monthTicketStatus ? monthTicketStatus.price : 0;
                init.ticketCount = init.monthTicket ? init.monthTicket.days : 0;
            }
            $('.js_ticket_price').html(parseFloat(init.fee).toFixed(2));
            $('.js_ticket_count').html(init.ticketCount);
        },

        /**
         * 计算优惠后支付的费用
         */
        updatePayTypeStatus : function(balanceType, wechatType){
            //处理优其他tab
            var data = init.contract;
            var cost= init.contract.cash_account.bus_cost;
            var balance=init.contract.cash_account?init.contract.cash_account.my_balance:0;
            var couponInfo = init.couponSelectObj.getSelectedVal();
            var discountValueWithCoupon=0;//优惠券抵扣金额
            var discountTitle = '';
            if(couponInfo){
                var couponVal=couponInfo.value;
                var couponType=couponInfo.type;//0次券 1金额券
                discountValueWithCoupon=couponType==1? couponVal : init.contract.max_ticket_price*1;
                discountValueWithCoupon=parseFloat(discountValueWithCoupon).toFixed(2);
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
        /**
         * 根据站点类型获取站点数据
         * @param stations
         * @param type
         * @returns {Array}
         */
        getStationsByStationType: function(stations, type) {
            var result = [];
            if (stations && stations.length && type) {
                stations.forEach(function (item) {
                    if (item.station_type == type) {
                        result.push(item);
                    }
                });
            }
            return result;
        },

        /**
         * 获取站点选择插件数据
         * @returns {Array}
         */
        toBuildSelectOptions : function (type) {
            var result = [];
            var isMergeStationsStatus = !!init.line.all_station_can_depart;
            if(init.shiftStations){
                var tpl = '{0} ({1})';
                var stations = [];
                var selectHint = '';
                if (isMergeStationsStatus) {
                    if (type == 'up') {
                        stations = init.shiftStations.slice(0, -1);
                    } else {
                        var startId = $(init.upStationNode).val();
                        var start = init.getShiftStationIdIndex(startId);
                        if ( start!=-1 && start>=0) {
                            var next = parseInt(start)+1;
                            stations = init.shiftStations.slice(next);
                        }
                    }
                } else {
                    stations = init.getStationsByStationType(init.shiftStations, type);
                }

                stations.forEach(function (item,i) {
                    var tmpItem = {
                        text : tpl.format(item.short_name, item.arrived_at_str),
                        value : item.station_id
                    };
                    result.push(tmpItem);
                });
            }
            return result;
        },
        /**
         * 车站选择
         * @param type up 上车站 down 下车站
         */
        initSelectEvent : function (type) {
            var target = type == 'up' ? init.upStationNode : init.downStationNode;
            var initData = init.toBuildSelectOptions(type);
            $(target).mobiscroll('destroy');
            $(target+'_dummy').remove();
            $(target).data('id',-1);
            // $(target).val('');
            $(target).mobiscroll().select({
                theme: 'ios',      // Specify theme like: theme: 'ios' or omit setting to use default
                lang: 'zh',    // Specify language like: lang: 'pl' or omit setting to use default
                display: 'bottom',  // Specify display mode like: display: 'bottom' or omit setting to use default
                layout:'liquid',
                placeholder:'请选择',
                width: $(window).width(),
                cancelText:'取消',
                setText:'完成',
                data:initData,
                onSet: function (event, inst) {
                    var val = inst.getVal();
                    var info =init.shiftStations[val];
                    $(target).data('id',info ? info.station_id : val);
                    if (type == 'up' && !!init.line.all_station_can_depart) {
                        init.initSelectEvent('down');
                    }
                },
                onBeforeShow : function (event, inst){
                    var val = inst.getVal();
                    if (!val) {
                        $(target+'_dummy').val('');
                    }
                    var upVal = $(init.upStationNode).val();
                    if (!upVal && target ==  init.downStationNode) {
                        $.showToast($.string.DEPT_STATION_MUST, false);
                        return false;
                    }
                },
                onInit: function (event, inst) {
                    var cookieVal = $.cookie(target+init.line.line_id);
                    //TODO 默认选中
                    if (!cookieVal && init.firstIn && initData.length) {
                        if (type === 'up') {
                            cookieVal = initData[0].value;
                        } else {
                            cookieVal = initData[initData.length-1].value;
                        }
                        console.log(cookieVal);
                    }

                    if ( cookieVal)
                    {
                        inst.setVal(cookieVal);
                    }
                    var val = inst.getVal();
                    if (!val) {
                        $(target+'_dummy').val('');
                    }
                }
            });

            var cookieVal = $.cookie(target+init.line.line_id);
            if ( !(cookieVal && init.firstIn))
            {
                // $(target+'_dummy').val('');
            }

        },
        /**
         * 获取站点索引
         * @param stationId
         * @returns {number}
         */
        getStationIdIndex:function (stationId) {
            var index = 0;
            if(init.stationData){
                init.stationData.forEach(function (item,i) {
                    if (item.station_id == stationId) {
                        index=i;
                    }
                });
            }
            return index;
        },
        /**
         * 获取站点索引
         * @param stationId
         * @returns {number}
         */
        getShiftStationIdIndex:function (stationId) {
            var index = 0;
            if(init.shiftStations){
                init.shiftStations.forEach(function (item,i) {
                    if (item.station_id == stationId) {
                        index=i;
                    }
                });
            }
            return index;
        },
        /**
         * 获取当前班车月票的状态
         * @param shift
         * @returns {Array}
         */
        getMonthTicketStatus:function (shift) {
            var res = [];
            var shiftStatusArr = init.monthTicket.frequency_status;
            if (shiftStatusArr && shiftStatusArr.length) {
                shiftStatusArr.forEach(function (item ,key) {
                    if (item.frequency == shift) {
                        res = item;
                    }
                });
            }
            return res;
        },
        
        /**
         * 班次选中事件
         * @param val
         * @param status  false 默认刷新显示 false 不刷新
         */
        checkedShiftVal : function(val, status){
            var options = $('.shifts>li');
            var checkSortNum = 0;
            var len = options.length;
            var start = 0;
            var end = 0;
            options.each(function (key, item) {
                var target = $(item);
                var curVal = target.data('id');
                if (curVal == val) {
                    checkSortNum = key;
                    if (len - key >=3) {
                        start = key;
                        end = key+2;
                    } else {
                        start = len-3 >=0 ? len-3 : 0;
                        end = len-1;
                    }
                    target.addClass('active');
                } else {
                    target.removeClass('active');
                }
            });
            if (!status) {
                options.each(function (key, item) {
                    var target = $(item);
                    if (key>=start && key<=end) {
                        target.removeClass('gone');
                    } else {
                        target.addClass('gone');
                    }
                });
            }
            if (init.currentShift != val)
            {
                init.currentShift = val;
                init.refreshShiftRelateData(val);
            }
        },
        /**
         * 班次变化对应的站点和日票跟随变化
         * @param val
         */
        refreshShiftRelateData :function(val){
            var shiftInfo = init.shiftsMap[val];
            if (shiftInfo) {
                var startIndex = init.getStationIdIndex(shiftInfo.begin_station_id);
                var endIndex = init.getStationIdIndex(shiftInfo.end_station_id);
                if (endIndex === 0) {
                    endIndex = init.stationData.length-1;
                }
                init.shiftStations = init.stationData.slice(startIndex, endIndex+1);
                init.updateStationTimeStr(init.shiftStations);
                init.initSelectEvent('up');
                init.initSelectEvent('down');
                init.renderCalendar();
                init.initMonthlyTicketEvent();                                                                         
                init.firstIn = false;
                //无可选中时候btn禁止操作
                if (init.calendarObj.getVal().length<1 && init.getTicketType() === 'day') {
                    init.createOrderBtn.addClass('disabled');
                } else {
                    init.createOrderBtn.removeClass('disabled');
                }
                init.calcToTalFee();
                init.setSeatValue();
            }
        },

        updateStationTimeStr:function (list) {
            var len = list.length;
            if (len) {
                var arrivedAtStr = init.currentShift;
                for (var i=0;i<len;i++) {
                    var date = new Date();
                    var timeArr = arrivedAtStr.split(':');
                    date.setHours(parseInt(timeArr[0]));
                    date.setMinutes(parseInt(timeArr[1]));
                    var timeGapSec = i ===0 ? 0 : parseInt(list[i].time_gap)*60*1000;
                    var targetTime = date.getTime()+timeGapSec;
                    arrivedAtStr = new Date(targetTime).Format('hh:mm');
                    list[i].arrived_at_str = arrivedAtStr;
                }
            }

        },

        /**
         * 刷新月票信息
         */
        initMonthlyTicketEvent:function () {
            var monthTicketStatus = init.getMonthTicketStatus(init.currentShift);
            var status = monthTicketStatus.status;
            $('.js_month_price').html(monthTicketStatus.price);
            var monthTitle = init.monthStatusTitleMap[status];
            $('.js_month_status_title').html(monthTitle);
            var target = $('.hlm4-pick-m-ticket');
            if ($.monthTicketStatus.Full == status) {
                target.removeClass('active');
            } else {
                target.addClass('active');
            }
        },

        /**
         * 初始化日历
         */
        renderCalendar : function () {
            if (!!init.calendarObj) {
                init.calendarObj.setOptions(init.shiftsData[init.currentShift]);
                init.calendarObj.draw();
                return false;
            }
            init.calendarObj = $('#js_calendar_section').initCalendarSelect({
                options:init.shiftsData[init.currentShift],
                changeFunc:function (data) {
                    init.calcToTalFee();
                    init.setSeatValue();
                    if (data.length) {
                        init.createOrderBtn.removeClass('disabled');
                    } else {
                        init.createOrderBtn.addClass('disabled');
                    }
                }
            });
        },
        /**
         * 跳转车票列表
         */
        toTicketList:function () {

            var cacheKey = 'js_ticket_list_cache_key_'+new Date(init.initShowTicketTime*1000).Format('yyyy_MM_dd');
            $.cache.remove(cacheKey);
            var params = {
                contract_id:init.contractId
            };
            $.wpost($.httpProtocol.GET_PAID_BUS_TICKET,params,function (data) {
                $.cache.set($.lastLockSeatParamsKey, false);
                if (data && data.ticket && data.ticket.date_seats) {
                    var firstTicket =  data.ticket.date_seats[0];
                    init.initShowTicketTime = firstTicket.dept_at;
                    $.cache.set($.string.ticketListIsShowTicket, firstTicket.ticket_id);
                    data.ticket.date_seats.forEach(function (ticket) {
                        var cacheKey = 'js_ticket_list_cache_key_'+new Date(ticket.dept_at*1000).Format('yyyy_MM_dd');
                        $.cache.remove(cacheKey);
                    });
                }
                $.locationUrl('/my-order/?type=0&timestamp='+init.initShowTicketTime,true);
            },function () {
            });
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
                    init.toTicketList();
                }
            },function () {
                init.payLoading = false;
            });
        },

        /**
         * 取消订单
         */
        cancelOrder : function () {
            if(init.cancelLoading){
                return false;
            }
            init.cancelLoading = true;
            $.wpost($.httpProtocol.CANCEL_ORDER,{id:init.contract.contract.contract_id},function (data) {
                init.payModal.modalDialog('hide');
                init.payLoading= false;
                if (init.interval) {
                    clearInterval(init.interval);
                }
                init.setSeatValue();//解锁座位
                setTimeout(function () {
                    $.showToast($.string.ORDER_CANCEL);
                    init.cancelLoading = false;
                },300);
            },function (res) {
                // $.showToast(res.msg);
                init.cancelLoading = false;
            })
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
                    $.cache.set($.lastLockSeatParamsKey, false);
                    var status = false;
                    if(res.errMsg == "chooseWXPay:ok" ) {
                        status = true;
                        init.notifyPay(sign.out_trade_no,status);
                        // $.showToast($.string.PAY_SUCCESS,true);
                    }else{
                        init.payLoading = false;
                        // $.showToast($.string.PAY_FAILED,false);
                    }
                    //支付成功后清除锁座信息防止返回继续解锁座位
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
         * 初始化支付
         * @param data
         */
        initPayModalEvent:function (data) {
            init.payModal.find('.js_pay_btn').addClass('disabled');
            init.contract = data;
            //倒计时
            var countVal = data.contract.reserve_end_time-data.timestamp;
            init.initWaitingSecond(countVal);
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
                contractId:data.contract.contract_id,
                change:function (coupon) {
                    init.updatePayTypeStatus();
                    $('#js_coupon_type').prop('checked', (coupon ? true : false));
                    init.payModal.find('.js_pay_btn').removeClass('disabled');

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

            //modal 显示
            init.payModal.modalDialog();
            init.payModal.find('.js_cancel_btn').unbind().bind('click', function () {
                init.cancelOrder();
            });
            //支付
            init.payModal.find('.js_pay_btn').unbind().bind('click',function () {
                if ($(this).hasClass('disabled')) {
                    return false;
                }

                if(init.payLoading){
                    return false;
                }
                var contract = init.contract.contract;
                var couponId = '';
                var coupon =  init.couponSelectObj.getSelectedVal();
                if (coupon && coupon.coupon_id) {
                    couponId = coupon.coupon_id;
                }
                var params = {
                    id : contract.contract_id,
                    use_balance : (init.balanceCost>0 || contract.amount ==0) ? 1 : 0,
                    use_3rd_trade : init.wechatCost>0 ? 1 : 0,
                    use_coupon : couponId.length ? 1: 0,
                    coupon_id : couponId
                };
                init.contractId =  params.id;
                init.payLoading = true;
                $.wpost($.httpProtocol.PAY,params,function (data) {
                    //直接付款成功（0 元支付）

                    if(params.use_3rd_trade==1 && contract.amount != 0){//微信支付
                        if(data.wechat && data.wechat.sign){
                            init.initJsPay(data.wechat.sign);
                        }else{
                            $.showToast($.string.WECHAT_PAY_UP_FAILED, false);
                            init.payLoading = false;
                        }
                    } else {
                        init.toTicketList();
                    }
                },function () {
                    init.payLoading = false;
                });
            });
        },
        /**
         * 设置座位信息
         * @param seat
         * @param title
         */
        setSeatValue : function (seat, title) {
            var seatInputTarget = $(init.seatNumInputNode);
            var seatNum = -1;
            var seatShowTitle = '随机';
            if (title) {
                seatNum = seat;
                seatShowTitle = title;
            } else {
                if (seatInputTarget.data('seat') != seatNum) {
                    if ($.cache.get($.lastLockSeatParamsKey)) {
                        init.seatObj.unLockSeat(true);
                    }
                }
            }
            seatInputTarget.data('seat', seatNum);
            seatInputTarget.val(seatShowTitle);
        },

        initBtnEvent : function () {
            /**
             * 线路展开
             */
            $(document).on('click', '.js_up_down_click',function () {
                $('.js_up_down_btn').toggleClass('down');
                $('.js_up_down_section').toggleClass('gone');
            });

            /**
             * 点击站点
             */
            $(document).on('click', '.js_station_item',function () {
                var id = $(this).data('id');
                var lineId = $(this).parents('ul').data('line-id');
                $.locationUrl('/bus-map/{0}/?station_id={1}'.format(lineId, id));
            });

            /**
             * 选票tab
             */
            $(document).on('click', '.js_ticket_pick_btn',function () {
                var $this = $(this);
                var index = $this.index();
                var type = $this.data('type');
                $this.siblings().removeClass('active');
                $this.addClass('active');
                var target = $('.js_ticket_pick');
                target.addClass('gone');
                target.eq(index).removeClass('gone');
                var titleDayObj = $('.js_day_title');
                var titleObj = $('.js_month_title');
                if (type == 'month') {
                    titleObj.removeClass('gone');
                    titleDayObj.addClass('gone');
                } else {
                    titleObj.addClass('gone');
                    titleDayObj.removeClass('gone');
                }
                init.calcToTalFee();
                init.setSeatValue();
            });

            /**
             * 选择班次
             */
            init.showShiftModal = $('.js_more_btn').showShiftModal({
                title:'请选择乘车班次',
                checked:true,
                selectVal:init.currentShift,
                changeFunc:function (val) {
                    init.checkedShiftVal(val);
                }
            });
            /**
             * 班次选择
             */
            $('.shifts').on('click', 'li',function () {
                var $this = $(this);
                var val = $this.data('id');
                init.checkedShiftVal(val,true);
                init.showShiftModal.setVal(val);//同步modal值
            });
            

            /**
             * 创建订单
             */
            init.createOrderBtn.unbind().bind($.getClickEventName(),function () {
                if(init.loading || $(this).hasClass('disabled')){
                    return false;
                }
                var seat = $(init.seatNumInputNode).data('seat');
                var params = {
                    line_id : init.line.line_id,
                    dept_station_id : $(init.upStationNode).val(),
                    dest_station_id : $(init.downStationNode).val(),
                    seat : seat ? seat : -1,
                    type : init.getTicketType()
                };
                //不是随机的座位重新锁座
                if (params.seat !== -1) {
                    init.seatObj.lockSeat(true);
                }

                if (params.type == 'day') {
                    params.schedule_ids = init.calendarObj.getVal();
                    if (params.schedule_ids.length<1) {
                        $.showToast($.string.TICKET_DAY_MUST, false);
                        return false;
                    }
                    var scheduleItems = init.calendarObj.getVal(true);
                    init.initShowTicketTime =  Math.floor(scheduleItems[0].time/1000);
                    
                } else {
                    var monthStatus = init.getMonthTicketStatus(init.currentShift);
                    params.year = init.monthTicket.year;
                    params.month = init.monthTicket.month;
                    params.frequency = monthStatus.frequency;
                    if (!params.year || !params.month) {
                        $.showToast($.string.TICKET_MONTH_MUST, false);
                        return false;
                    }
                    if (monthStatus.status == 2 ) {
                        $.showToast($.string.TICKET_MONTH_BUY, false);
                        return false;
                    }
                    if (monthStatus.status == 3 ) {
                        $.showToast($.string.TICKET_MONTH_FULL, false);
                        return false;
                    }
                    init.initShowTicketTime = Math.floor(new Date(params.year, params.month-1)/1000);
                    
                }
                if (params.dept_station_id<1) {
                    $.showToast($.string.DEPT_STATION_MUST, false);
                    return false;
                }
                if (params.dest_station_id<1) {
                    $.showToast($.string.DEST_STATION_MUST, false);
                    return false;
                }
                init.loading = true;
                $.wpost($.httpProtocol.CREATE_ORDER,params,function (data) {
                    $.cookie(init.upStationNode+params.line_id, params.dept_station_id);
                    $.cookie(init.downStationNode+params.line_id, params.dest_station_id);
                    init.initPayModalEvent(data);
                    init.loading = false;
                },function () {
                    init.loading = false;
                });
            });

            /**
             * 选座位插件和参数处理
             */
            $(init.seatNumInputNode).focusin(function () {
                var params = {
                    line_id : init.line.line_id,
                    type : init.getTicketType()
                };
                
                if (params.type === $.ticketCategory.Day) {
                    params.schedules = init.calendarObj.getVal();
                    if (params.schedules.length<1) {
                        $.showToast($.string.TICKET_DAY_MUST, false);
                        return false;
                    }
                } else {
                    var monthStatus = init.getMonthTicketStatus(init.currentShift);
                    params.year = init.monthTicket.year;
                    params.month = init.monthTicket.month;
                    params.frequency = monthStatus.frequency;
                    if (!params.year || !params.month) {
                        $.showToast($.string.TICKET_MONTH_MUST, false);
                        return false;
                    }
                    if (monthStatus.status == 2 ) {
                        $.showToast($.string.TICKET_MONTH_BUY, false);
                        return false;
                    }
                    if (monthStatus.status == 3 ) {
                        $.showToast($.string.TICKET_MONTH_FULL, false);
                        return false;
                    }
                }
                $(this).data('params', params);
            });
            init.seatObj = $(init.seatNumInputNode).initSeatSelectEvent({
                onSet:function (inst) {
                    var seat = inst.getVal();
                    var seatTitle = inst.getSeatTitle();
                    var seatNum = $.isEmptyObject(seat)  ? -1 : seat.seat;
                    init.setSeatValue(seatNum, seatTitle);
                }
            });
        },
        /**
         * 线路分享
         */
        shareEvent:function () {
            var shareOpt = {
                link:$.string.LINE_SHARE_URL.format(document.global_config_data.platform, init.line.line_id),
                desc:$.string.LINE_SHARE_TITLE.format(init.line.line_name, document.global_config_data.app_name),
                successFunc:function () {
                }
            };
            $.initWxShareConfigWithData(shareOpt);
        },

        run : function () {
            //搜索
            if (init.defaultShift.length) {
                init.currentShift = init.defaultShift;
                init.checkedShiftVal(init.currentShift);
            }
            init.shareEvent();
            init.initBtnEvent();
            init.refreshShiftRelateData(init.currentShift);
        }
    };
    init.run();
})($);