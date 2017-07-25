/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        moreBtnNode : '.js_more_btn',
        loading :false,
        type :$.getQueryParams('type'),

        unlockSeat:function () {
            var params = $.cache.get($.lastLockSeatParamsKey);
            if (!params) {
                return false;
            }
            var url;
            if ($.ticketCategory.Day ===  params.type) {
                url = $.httpProtocol.LOCK_OR_UNLOCK_BY_DAY;
            } else {
                url = $.httpProtocol.LOCK_OR_UNLOCK_BY_Month;
            }
            $.wpost(url,params,function (data) {
                $.cache.set($.lastLockSeatParamsKey, false);
            },function () {
            },true, false);
        },

        initBtnEvent : function () {
            /**
             * 上下拉分页
             * @type {jQuery|HTMLElement|*|jQuery}
             */
            var type = $.getQueryParams('type');
            init.pager = $('#page').DropLoadPager({
                protocol:$.httpProtocol.GET_BUS_LIST,
                cacheKeySuffix:type,
                wrapUpdateData:function (data) {
                    var param={};
                    param.type=type ? type : 0;
                    if (param){
                        $.extend(data, param);
                    }
                    return data;
                }
            });

            
            /**
             * 跳转详情
             */
            $(document).on('click', '.bus-item',function () {
                var $this = $(this);
                var lineId = $this.data('line-id');
                $.recordPageScrollTopHeight();
                $.locationUrl('/pay/'+lineId);
            });

            /**
             * 查看更多
             */
            $('.js_more_btn').showShiftModal({
                title:'所有班次',
                checked:false
            });

            /**
             * tab切换
             */
            $(document).on('click', '.tab-item',function () {
                var type = $(this).data('type');
                if (init.type == type) {
                    return false;
                }
                var url = '/?type='+type;
                $.recordPageScrollTopHeight(0);
                $.locationUrl(url, true);
            });

        },
        run : function () {
            //搜索
            init.initBtnEvent();
            init.unlockSeat();
        }
    };
    init.run();
})($);