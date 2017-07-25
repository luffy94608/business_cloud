/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        moreBtnNode : '.js_more_btn',
        order_click_tab : 'order_click_tab_key',
        loading :false,
        mapDataStatus :false,
        mapLoading :false,
        firstInit :true,
        currentDate:new Date(),
        swiper:null,
        swiperWraprNode:$('.swiper-wrapper'),
        template:"<li class='swiper-slide {0}' data-id='{1}' data-hash='{1}' id='js_date_{3}'><p class='week'>{2}</p><p class='day'>{3}</p></li>",
        weekMap : ['日','一','二','三','四','五','六'],
        hasTicketDays:[],
        hasTicketDay: 0,

        initRefresh:function (time) {
            init.hasTicketDays = [];
            init.toBuildDateHtml(time);
            init.initSwiperEvent();
            init.updateSwiperTab(time);
            init.initMonthTicket(time.getTime()/1000);
        },

        /**
         * 日期选择
         */
        initTimeSelectEvent:function () {
            $('#datetime').mobiscroll().date({
                theme: 'ios',      // Specify theme like: theme: 'ios' or omit setting to use default
                lang: 'zh',    // Specify language like: lang: 'pl' or omit setting to use default
                display: 'bottom',  // Specify display mode like: display: 'bottom' or omit setting to use default
                monthNames: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                showNow: true,
                rows : 4,
                cancelText:'取消',
                setText:'完成',
                monthSuffix:'月',
                yearSuffix:'年',
                dateWheels: 'yy mm',
                showLabel : false,
                // min: new Date(),
                // max: new Date(now.getTime() + 3*60*60*24*1000),
                headerText : '',
                dateFormat: 'yy-mm',
                hourText: "点",
                nowText: "今天",
                onSet: function (event, inst) {
                    var time = inst.getVal();
                    var title = time.Format('yyyy年MM月');
                    $('#datetime_title').html(title);
                    init.initRefresh(time);
                },
                onChange: function (event, inst) {

                },
                onInit: function (event, inst) {
                    var time =init.currentDate;
                    var cacheTab = $.cache.get(init.order_click_tab);
                    if (init.firstInit && cacheTab) {
                        time = new Date(parseInt(cacheTab)*1000);
                    }
                    var title = time.Format('yyyy年MM月');
                    $('#datetime_title').html(title);
                    $('#datetime_after_icon').removeClass('gone');
                    $('#datetime').val(title);
                    init.initRefresh(time);
                }
            });
        },

        /**
         * 生成日历html
         * @param date
         */
        toBuildDateHtml: function (date) {
            var html = "";
            var year = date.getFullYear();
            var month = date.getMonth();
            var start = 1;
            var end = new Date(year,month+1, 0).getDate();
            var today = new Date();
            for (var i = start; i<=end; i++) {
                var time = new Date(year, month, i);
                var week = time.getDay();
                var weekTitle = init.weekMap[week];
                var style = '';
                if ( $.inArray(time.getDate(),init.hasTicketDays) ===-1) {
                    style = 'disabled';
                }
                if (today.getFullYear() == time.getFullYear() && today.getMonth() == time.getMonth() && today.getDate() == time.getDate()) {
                    style += ' now';
                }
                html+= init.template.format(style, parseInt(time.getTime()/1000), weekTitle, i);
            }

            init.swiperWraprNode.html(html);
        },

        /**
         * 初始化swiper
         */
        initSwiperEvent : function () {
            if (init.swiper) {
                init.swiper.slideTo(0);
                init.swiper.destroy();
            }
            init.swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                slidesPerView: 7,
                // initialSlide :slideIndex,
                spaceBetween: 0 ,
                hashnav:false
            });

        },
        updateSwiperTab:function (date) {
            if (!init.mapDataStatus) {
                return false;
            }
            var slideIndex = 0;
            var datetime = $('#datetime').val();
            var day = init.currentDate.getDate();
            var cacheTab = $.cache.get(init.order_click_tab);
            var cacheDate = new Date(parseInt(cacheTab)*1000);
            var cacheDay = cacheTab ? cacheDate.getDate() : false;
            var isCurrentMonth = date.getMonth() ===  init.currentDate.getMonth();

            // if (init.firstInit && cacheTab) {
            //     if (!isCurrentMonth && init.hasTicketDay === -1) {
            //         day =cacheDate.getDate();
            //     }
            //     console.log(day);
            //     $.cache.remove(init.order_click_tab);
            // }

            var clickIndex = 0;
            if ( (init.firstInit && init.hasTicketDays.indexOf(day) !== -1) || isCurrentMonth) {
                clickIndex = day;
            } else {
                clickIndex = init.hasTicketDay;
            }
            if (isCurrentMonth && init.hasTicketDays.indexOf(cacheDay) !== -1) {
                clickIndex =cacheDay;
                // $.cache.remove(init.order_click_tab);
            }

            clickIndex = clickIndex -1 >0 ? clickIndex -1 : 0;
            slideIndex = (clickIndex-4)>=0 ?(clickIndex-4) : slideIndex;
            console.log('slideIndex ::'+slideIndex);
            console.log('clickIndex::'+clickIndex);
            $('.swiper-slide').eq(clickIndex).trigger('click');
            init.swiper.slideTo(slideIndex);
            init.firstInit = false;
            init.mapDataStatus = false;
        },
        /**
         * 获取车票列表
         */
        initTicKetList:function (timestamp) {
            if (init.loading && !timestamp) {
                return false;
            }
            timestamp = parseInt(timestamp);
            var cacheKey = 'js_ticket_list_cache_key_'+new Date(timestamp*1000).Format('yyyy_MM_dd');
            var type = $.getQueryParams('type');
            var params = {
                timestamp : timestamp
            };
            if (typeof  type === 'string') {
                params.type = type;
            }
            var cacheData = $.cache.get(cacheKey);
            console.log(cacheKey);
            if (cacheData) {
                $('#list').html(cacheData.html);
                if ($.cache.get($.string.ticketListIsShowTicket)) {
                    $('.js_show_ticket_btn').eq(0).trigger('click');
                    $.cache.set($.string.ticketListIsShowTicket, false);
                }
                return false;
            }
            init.loading = true;
            $.wpost($.httpProtocol.GET_TICKET_LIST_BY_DATE,params,function (data) {
                $.cache.set(cacheKey, data);
                $('#list').html(data.html);
                var cacheTicketId = $.cache.get($.string.ticketListIsShowTicket);
                if (cacheTicketId) {
                    $('#ticket_id_'+cacheTicketId+' .js_show_ticket_btn').trigger('click');
                    $.cache.set($.string.ticketListIsShowTicket, false);
                }
                init.loading = false;
            },function () {
                init.loading = false;
            });
        },
        /**
         * 获取当月的购票情况
         */
        initMonthTicket:function (timestamp) {
            if (init.mapLoading && !timestamp) {
                return false;
            }
            var cacheKey = 'key_month_ticket_map_'+new Date(timestamp*1000).Format('yyyy_MM');
            var type = $.getQueryParams('type');
            var params = {
                timestamp : timestamp,
                type : 0
            };
            if (typeof  type === 'string') {
                params.type = type == $.ticketType.Bus ? 1 : 2;
            }
            init.mapLoading = true;
            init.mapDataStatus = false;
            var cacheData = $.cache.get(cacheKey);
            if (cacheData) {
                init.initMonthTicketData(cacheData, timestamp);
                return false;
            }

            $.wpost($.httpProtocol.GET_TICKET_MONTH_MAP,params,function (data) {
                $.cache.set(cacheKey, data);
                init.initMonthTicketData(data, timestamp);
                // init.mapLoading = false;
            },function () {
                init.mapLoading = false;
            },true);
        },
        /**
         * 初始化data 数据
         * @param data
         * @param timestamp
         */
        initMonthTicketData:function (data, timestamp) {
            init.hasTicketDays = data.days;
            if (init.hasTicketDays.length) {
                for (var key in init.hasTicketDays) {
                    $('#js_date_'+init.hasTicketDays[key]).removeClass('disabled');
                }
                init.hasTicketDay = data.days.shift();
            } else {
                init.hasTicketDay = -1;
            }
            init.mapDataStatus = true;
            init.mapLoading = false;
            init.updateSwiperTab(new Date(timestamp*1000));
        },

        initBtnEvent : function () {
            /**
             * 出示车票
             */
            $('.js_show_ticket_btn').addShowTicketEvent({
                currentTarget : '.js_show_ticket_btn',
            });

            /**
             * 跳转详情
             */
            $(document).on('click', '.bt-item .item-bd',function () {
                var $this = $(this).parents('.bt-item');
                var ticketId = $this.data('id');
                var ticketType = $this.data('type');
                var url;
                if (ticketType == $.ticketType.Bus) {
                    url = '/order-detail/'+ticketId;
                } else {
                    url = '/ticket-detail/'+ticketId;
                }
                var time = $('.swiper-slide.active').data('id');
                $.cache.set(init.order_click_tab, time);
                $.locationUrl(url);
            });

            /**
             * 查看实时位置
             */
            $(document).on('click', '.icon-location-wrap',function (e) {
                e.stopPropagation();
                var $this = $(this);
                var lineId = $this.data('line-id');
                var time = $('.swiper-slide.active').data('id');
                $.cache.set(init.order_click_tab, time);
                $.locationUrl('/bus-location/'+lineId);
            });

            $(document).on('click', '.js_disabled_btn',function (e) {
                e.stopPropagation();
            });

            /**
             * 评价
             */
            $(document).on('click', '.js_remark_btn',function (e) {
                e.stopPropagation();
                var id = $(this).parents('.bt-item').data('id');
                var time = $('.swiper-slide.active').data('id');
                $.cache.set(init.order_click_tab, time);
                $.locationUrl('/remark/'+id);
            });

            /**
             * 日历选择
             */
            $('.swiper-container').on('click', '.swiper-slide',function () {
                var $this = $(this);
                $this.siblings().removeClass('active');
                $this.addClass('active');
                var timestamp = $this.data('id');
                init.initTicKetList(timestamp);
            });

        },
        run : function () {
            //搜索
            var timestamp = $.getQueryParams('timestamp');
            if (timestamp) {
                init.currentDate = new Date(timestamp*1000);
            }
            // $.cache.remove(init.order_click_tab);
            init.initBtnEvent();
            init.initTimeSelectEvent();
        }
    };
    init.run();
})($);