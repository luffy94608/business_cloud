(function ($) {
    //选座插件

    $.fn.initSeatSelectEvent = function (opts) {
        var st = {
            result: '',
            template : require('art-template'),
            loading:false,
            onSet:'',
            ticketCategory:'',
            params:'',
            seatTitle:'',
            seatPageState:'seat_select_page',
            modal:$('#js_seat_select_modal'),
            confirmBtn:$('.js_confirm_seat_btn'),
            seatContentHtmlNode:"#js_seat_modal_content",
            seatStyle : {0:'out',1:'',2:'active'},
            seatItemTpl:'<div class="bsmb-item">{0}</div>',
            seatGapTpl:'<div><p class="seat-title"></p></div>',
            seatItemNode:'.js_seat_item',
            seatItemPieceTpl:'<div class="{0} js_seat_item" data-seat="{1}" data-status="{2}"><p class="seat-pic"></p></div>'
        };
        if (opts) {
            $.extend(st, opts);
        }
        var $this = $(this);

        /**
         * 显示车票
         */
        $this.showSeatModal = function () {
            var json = {
                id:st.seatPageState
            };
            window.history.pushState(json,"选择座位","?page=seat_select");
            st.modal.addClass('active');
            $('body').addClass('o-hidden');
            $('.dialog-scroll').height($(window).height());
            $(window).on('scroll', function(e){
                $(document).scrollTop(0);
            });
        };

        /**
         * 隐藏modal
         * @param status  true 不返回 false 默认
         */
        $this.hideModal = function (status) {
            st.modal.removeClass('active');
            $('html,body').removeClass('o-hidden');
            $(window).off('scroll');
            if (!status) {
                window.history.back();
            }
        };

        /**
         * 座位信息 html
         * @param seat
         */
        $this.initSeatInfoHtml = function (seat, busTypeInfo) {
            var seatNum = seat.seat;
            var statusStatus = seat.status;
            var style = st.seatStyle[seat.status];
            if (statusStatus == $.seatStatus.Normal && !!st.result && st.result.seat == seatNum) {
                style=st.seatStyle[$.seatStatus.Checked];
            }
            if (statusStatus == $.seatStatus.Checked) {
                $this.setVal(seat);
            }
            if (busTypeInfo.excludes && busTypeInfo.excludes.indexOf(seatNum) !== -1) {
                style += ' v-hidden';
            }
            return st.seatItemPieceTpl.format(style, seatNum, statusStatus);
        };

        /**
         * 初始化座位图html
         */
        $this.initSeatsHtml = function (data) {
            var html = '';
            if (data && data.seats) {
                var busTypeInfo = data.bus_type_info;
                var seats = data.seats;
                var len = seats.length;
                var pieceHtml = '';
                seats.forEach(function (seat, index) {
                    if (!(busTypeInfo.have_five_column && index === len-1) && busTypeInfo) {
                        pieceHtml+=$this.initSeatInfoHtml(seat, busTypeInfo);
                        if ( (seat.seat%busTypeInfo.column+1) === Math.floor(busTypeInfo.column/2) ) {
                            if (busTypeInfo.have_five_column && Math.ceil(seat.seat/busTypeInfo.column) === Math.floor(len/busTypeInfo.column)) {
                                pieceHtml += $this.initSeatInfoHtml(seats[len-1], busTypeInfo);
                            } else {
                                pieceHtml += st.seatGapTpl;
                            }
                        }
                        if (Math.floor(seat.seat%busTypeInfo.column) === busTypeInfo.column - 1) {
                            html += st.seatItemTpl.format(pieceHtml);
                            pieceHtml = '';
                        }

                    }
                })    
            }
            st.modal.find(st.seatContentHtmlNode).html(html);
            $this.showSeatModal();
        };

        /**
         * 初始化座位信息数据
         */
        $this.initSeatsMapData = function () {
            if (st.loading) {
                return false;
            }
            var params = $this.data('params');
            if (!(params && !$.isEmptyObject(params))) {
                return false;
            }
            st.loading = true;
            var url;
            if (params.type === $.ticketCategory.Day) {
                url = $.httpProtocol.STATUS_BY_DAY;
            } else {
                url = $.httpProtocol.STATUS_BY_Month;
            }
            $.wpost(url, params,function (data) {
                if (params.type === $.ticketCategory.Day) {
                    var busScheduleIds = [];
                    var schedules = data && data.schedules ? data.schedules : [];
                    params.schedules =schedules;
                    schedules.forEach(function (item) {
                        if (item.bus_schedule_id) {
                            busScheduleIds.push(item.bus_schedule_id);
                        }
                    });
                    params.bus_schedule_ids = busScheduleIds;
                }
                st.params =  params;
                $this.initSeatsHtml(data);
                st.loading = false;
            },function () {
                st.loading = false;
            },false);
        };

        /**
         * 获取选中结果
         */
        $this.getVal = function () {
            return st.result;
        };

        /**
         * 设置选中结果
         */
        $this.setVal = function (val) {
            st.result = val;
        };

        /**
         * 获取选中结果
         */
        $this.getSeatTitle = function () {
            return st.seatTitle;
        };

        /**
         *  1解锁 0锁座
         * @param lockType 0 锁 1解
         * @param isHideLoading
         * @returns {boolean}
         */
        $this.lockOrLockSeat = function (lockType, isHideLoading) {
            if (st.loading) {
                return false;
            }
            var params = st.params;
            var url;
            var seat = $this.getVal();
            if (!seat) {
                 return false;
            }
            params.seat = seat.seat;
            params.lock_type = lockType;
            if ($.ticketCategory.Day ===  params.type) {
                url = $.httpProtocol.LOCK_OR_UNLOCK_BY_DAY;
            } else {
                url = $.httpProtocol.LOCK_OR_UNLOCK_BY_Month;
            }
            return $.wpost(url,params,function (data) {
                st.seatTitle = (data && data.seat) ? data.seat : '';
                if (lockType == 1) {//
                    $this.setVal('');
                    $.cache.set($.lastLockSeatParamsKey, false);
                } else {
                    params.lock_type = 1;
                    $.cache.set($.lastLockSeatParamsKey, params);
                }
                st.loading = false;
            },function () {
                st.loading = false;
            },isHideLoading, false, lockType == 1 ? true : false);
        };

        /**
         * 锁定座位
         * @param isHideLoading
         * @returns {boolean}
         */
        $this.lockSeat = function (isHideLoading) {
            return $this.lockOrLockSeat(0, isHideLoading);
        };

        /**
         * 解锁座位
         * @param isHideLoading
         * @returns {boolean}
         */
        $this.unLockSeat = function (isHideLoading) {
            return $this.lockOrLockSeat(1, isHideLoading);
        };
        
        /**
         * 事件初始化
         */
        $this.initBtnEvent = function () {
            /**
             * modal显示事件
             */
            $this.focusin(function () {
                $(this).blur();
                console.log($(this).data('params'));
                $this.initSeatsMapData();
            });

            /**
             * 确认选择事件
             */
            st.confirmBtn.unbind().bind('click', function () {
                var seat = $this.getVal();
                if ($.isEmptyObject(seat)) {
                    $.showToast($.string.SEAT_NUMBER_MUST);
                    return false;
                }

                $this.lockSeat().done(function (data) {
                    if (data.code === 0) {
                        //选中回调函数
                        if ($.isFunction(st.onSet)) {
                            st.onSet($this);
                        }
                        $this.hideModal();
                    }
                });
            });

            /**
             * 监听 push state change
             */
            window.addEventListener("popstate", function(e) {
                var currentState = history.state;
                if (currentState && currentState.id && currentState.id === st.seatPageState) {
                    st.modal.addClass('active');
                    $('body').addClass('o-hidden');
                } else {
                    // st.modal.removeClass('active');
                    // $('body').removeClass('o-hidden');
                    $this.hideModal(true);
                }
            });
            /**
             * 选中事件
             */
            st.modal.find(st.seatContentHtmlNode).on('click', st.seatItemNode, function (e) {
                e.stopPropagation();
                e.preventDefault();
                var seatTarget = $(this);
               var seat = seatTarget.data();
               if ($.isEmptyObject(seat) || seat.status == $.seatStatus.Disabled) {
                    return false;
               }
               var checkedNodes = st.modal.find(st.seatItemNode+'.'+$.seatStatusStyle.Checked);
               checkedNodes.removeClass($.seatStatusStyle.Checked);
               seatTarget.addClass($.seatStatusStyle.Checked);
               $this.setVal(seatTarget.data());
            });

        };


        /**
         * 初始化
         */
        $this.run = function () {
            $this.initBtnEvent();
        };
        $this.run();
        return $this;
    };

})($);


