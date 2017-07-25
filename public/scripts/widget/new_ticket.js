(function ($) {
    //车票插件

    $.showTicket = function (opts) {
        var st = {
            ticketList: [],
            timer:null,
            swiper:null,
            template : require('art-template'),
            uniqueId:null,
            loading:false,
            checkedCount:0,
            allCheckedFunc:false,
            ticketType:$.ticketType.Shuttle,
            itemPrefix:'#js_ticket_',
            checkBtnNode:'.js_ticket_check_btn',
            modalNode:'#js_ticket_list_modal',
            modal:$('#js_ticket_list_modal'),
            styleSecTargetNode:'.js_style_section',
            ticketListSecNode:'#js_ticket_list',
            busTicketTpl:"js_tpl_bus_ticket",
            shuttleTicketTpl:"js_tpl_shuttle_ticket"
        };
        if (opts) {
            $.extend(st, opts);
        }
        var $this = {};

        /**
         * 车票背景颜色变色
         * @param target
         * @param color
         * @param checkedColor
         */
        $this.cgTicketBg = function (target, color, checkedColor) {
            var styleTpl = '<style type="text/css">{0} .js_before_after:after,{0} .js_before_after:before { background: radial-gradient(circle, rgba(0,0,0,0),rgba(0,0,0,0) 50%,{1}  50%,{1} )!important; }</style>';
            var id = '#'+target.attr('id');
            var styleStr = styleTpl.format(id, color);
            $('.js_bg_color', target).css('backgroundColor', color);
            if (checkedColor) {
                $('.js_bg_checked_color', target).css('backgroundColor', checkedColor);
            }
            $(st.styleSecTargetNode, target).html(styleStr);
        };

        /**
         * 车票状态切换
         * @param target
         * @param step  变化 0 正常状态 1开始验票 2已经验票
         * @param color  1状态下的颜色
         * @param time  验票时间或者开始验票时间 时间戳秒
         */
        $this.ticketStep = function (target ,step, color, time) {
            //重复处理
            var prevStep = target.data('step');
            if (typeof  prevStep === 'undefined' || prevStep !== step) {
                target.data('step', step)
            } else {
                return false;
            }

            var targetBtn = target.find(st.checkBtnNode);
            var changeHeaderTarget = target.find('.tk-header');
            color='#'+color.substr(2);
            target.removeClass('active checked expired'); //文字颜色
            switch (step){
                case 0 : //正常状态验票之前
                    // targetBtn.html($.string.TICKET_CHECKED_BTN_AHEAD_TITLE.format($.showEndTime(time)));
                    targetBtn.html($.string.TICKET_CHECKED_BTN_NOT_TITLE);
                    targetBtn.addClass('disabled');
                    changeHeaderTarget.addClass('bg-color-transition');
                    $this.cgTicketBg(target, '#ffffff');
                    break;
                case 1 : //开始验票
                    targetBtn.html($.string.TICKET_CHECKED_BTN_TITLE);
                    $this.cgTicketBg(target, color);
                    targetBtn.removeClass('disabled');
                    changeHeaderTarget.addClass('bg-color-transition');
                    target.addClass('active');
                    break;
                case 2 ://已经验票
                    changeHeaderTarget.removeClass('bg-color-transition');
                    if (time>0) {
                        targetBtn.html($.string.TICKET_CHECKED_BTN_ACTIVE_TITLE.format(new Date(time*1000).Format('hh:mm:ss')));
                    } else {
                        targetBtn.html($.string.TICKET_CHECKED_TITLE);
                    }
                    targetBtn.addClass('disabled');
                    $this.cgTicketBg(target, '#ffffff', color);
                    target.addClass('checked');
                    break;
                case 3 ://已经过验票时间 未验票
                    changeHeaderTarget.removeClass('bg-color-transition');
                    targetBtn.html($.string.TICKET_LINE_CLOSED);
                    $this.cgTicketBg(target, '#ffffff');
                    targetBtn.addClass('disabled');
                    target.addClass('expired');
                    break;
            }
        };

        /**
         * 获取当前车票信息
         * @param id
         * @returns {{}}
         */
        $this.getTicketInfoById = function(id){
            var len = st.ticketList.length;
            var ticket = {};
            if (len){
                for (var i=0; i<len; i++) {
                    var item = st.ticketList[i];
                    if (item.ticket_id == id) {
                        ticket = id;
                        break;
                    }
                }
            }
            return ticket;
        };

        /**
         * 初始化车票html
         */
        $this.initTicketHtml = function () {
            var len = st.ticketList.length;
            if (len) {
                var html = '';
                for (var i=0; i<len; i++) {
                    var item = st.ticketList[i];
                    if (st.ticketType == $.ticketType.Bus) {
                        html += st.template(st.busTicketTpl, item);
                    } else {
                        html += st.template(st.shuttleTicketTpl, item);
                    }

                }
                $(st.ticketListSecNode, st.modal).html(html);
            }

        };

        /**
         * 更新车票状态
         */
        $this.updateTicketStat = function () {
            var len = st.ticketList.length;
            if (len){
                var currentTime = new Date().getTime()/1000;

                for (var i=0; i<len; i++) {
                    var item = st.ticketList[i];
                    var id = item.ticket_id;
                    var aheadSeconds = parseInt(item.show_color_ahead_in_seconds);
                    var aheadTime,destTime,status;
                    var filterStatus;
                    if (st.ticketType == $.ticketType.Bus) {
                         aheadTime = parseInt(item.dept_at)-aheadSeconds;
                         destTime = parseInt(item.dest_at);
                         status = item.use_status;
                        filterStatus = [$.ticketStatus.WaitRemark,$.ticketStatus.Finished];
                    } else {
                         var shuttleConfig = document.global_config_data.config.shuttle_config;
                         aheadTime = $.buildTimeStampInTimeStr(item.shuttle_line.business_start)-aheadSeconds;
                         destTime = $.buildTimeStampInTimeStr(item.shuttle_line.business_end);
                         status = item.status;
                        filterStatus = [$.shuttleTicketStatus.Finished];
                    }
                    var checkTime = parseInt(item.check_time);
                    var ticketColor = item.ticket_color;
                    var target = $(st.itemPrefix+id);

                    //验票按钮 处理
                    if (filterStatus.indexOf(status) !==-1) {
                        $this.ticketStep(target,2 ,ticketColor, checkTime);
                        continue;
                    }
                    if (currentTime<=aheadTime) {//验票之前
                        $this.ticketStep(target,0 ,ticketColor, aheadTime);
                    } else if(currentTime>aheadTime && currentTime<destTime) { //开始验票
                        $this.ticketStep(target,1 ,ticketColor);
                    } else {//结束验票 已过期
                        $this.ticketStep(target,3 ,ticketColor);
                    }
                }
            }

        };

        /**
         * 车票状态轮询
         */
        $this.initTimer = function () {
            if (st.timer) {
                clearInterval(st.timer);
            }
            $this.updateTicketStat();
            st.timer = setInterval($this.updateTicketStat, 1000);
        };

        $this.clear = function () {
            if (st.swiper) {
                st.swiper.destroy();
            }
        };

        /**
         * 初始化swiper
         */
        $this.initSwiperEvent = function () {
            $('.swiper-ticket-container').css('width', $(window).width());
            if (st.swiper) {
                st.swiper.destroy();
            }
            st.swiper = new Swiper('.swiper-ticket-container', {
                roundLengths : true,
                initialSlide :0,
                speed:300,
                slidesPerView:"auto",
                centeredSlides : true,
                followFinger : true,
                hashnav:true,
            });
        };

        /**
         * 显示车票
         */
        $this.show = function () {
            st.modal.addClass('active');
            $this.initTimer();
        };

        /**
         * 验票完成后的回调
         * @param id
         * @param checkTime
         */
        $this.checkedTicketCallback = function (id, checkTime) {
            var len = st.ticketList.length;
            if (len){
                for (var i=0; i<len; i++) {
                    var item = st.ticketList[i];
                    if (item.ticket_id == id) {
                        if (st.ticketType == $.ticketType.Bus) {
                            st.ticketList[i].use_status = $.ticketStatus.WaitRemark;
                        } else {
                            st.ticketList[i].status = $.shuttleTicketStatus.Finished;
                        }
                        st.checkedCount+=1;
                        st.ticketList[i].check_time = checkTime;
                    }
                }
                if (st.checkedCount === st.ticketList.length && $.isFunction(st.allCheckedFunc)) {
                    st.allCheckedFunc();
                }
            }
        };

        $this.initBtnEvent = function () {
            /**
             * 验票
             */
            st.modal.on('click', st.checkBtnNode,function () {
                var id = $(this).data('id');
                if (st.loading || !id || $(this).hasClass('disabled')) {
                    return false;
                }
                var params = {
                    ticket_id : id
                };
                var isBusTicket = st.ticketType == $.ticketType.Bus ? true : false;
                $.confirm({
                    content:$.string.TICKET_CHECKED_HINT,
                    titleShow:true,
                    success:function () {
                        if (!navigator.onLine) {//没有网络
                            var checkTime = Math.floor(new Date().getTime()/1000);
                            var cacheData = $.localCache.get($.offLineCheckedTicketKey);
                            if (!cacheData) {
                                cacheData = {
                                    bus_ticket:[],
                                    shuttle_ticket:[],
                                }
                            }
                            var checkedTicketItem = {
                                    ticket_id : id,
                                    check_time :checkTime
                                };
                            
                            if (isBusTicket) {
                                cacheData.bus_ticket.push(checkedTicketItem);
                            } else {
                                cacheData.shuttle_ticket.push(checkedTicketItem);
                            }
                            $.localCache.set($.offLineCheckedTicketKey, cacheData);
                            $this.checkedTicketCallback(id, checkTime);
                            return false;
                        }

                        st.loading = true;
                        var url = isBusTicket ?  $.httpProtocol.CHECK_BUS_TICKET : $.httpProtocol.CHECK_SHUTTLE_TICKET;
                        $.wpost(url,params,function (data) {
                            $this.checkedTicketCallback(id, data.time);
                            st.loading = false;
                        },function () {
                            st.loading = false;
                        },false);

                    }
                });

            });

            //modal 关闭事件
            st.modal.find('.js_close_btn').unbind().bind('click', function () {
                st.modal.removeClass('active');
                if (st.timer) {
                    clearInterval(st.timer);
                }
            });

        };


        /**
         * 初始化
         */
        $this.run = function () {
            $this.initTicketHtml();
            $this.initBtnEvent();
            $this.initTimer();
            $this.show();
            $this.initSwiperEvent();
        };
        $this.run();
        return $this;
    };

})($);


