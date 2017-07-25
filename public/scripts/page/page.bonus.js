/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        loading :false,
        itemNode :'.shuttle-item',
        cacheKey :'js_bonus_item_detail_key_{0}',
        map :'',

        showBonus:function (data) {
            var shareInfo = data.bonus_package;
            var shareOpt = {
                link:shareInfo.link,
                title:shareInfo.wechat_title? shareInfo.wechat_title :'小伙伴们，哈叔发红包啦！',
                desc:shareInfo.wechat_desc? shareInfo.wechat_desc :"哈叔请你坐班车！戳这里领取红包，免费乘坐班车，上下班再也不拥挤！",
                imgUrl:shareInfo.wechat_img? shareInfo.wechat_img : window.location.origin+'/images/logo.png',// 分享图标
                successFunc:function () {
                    $.showShareRemindModal('hide');
                    $('#js_red_packet_modal').removeClass('active');
                }
            };
            $.initWxShareConfigWithData(shareOpt);
            $.showShareRemindModal()
        },

        getBonusDetail:function (id) {
            if (init.loading) {
                return false;
            }
            var cacheData = $.cache.get(init.cacheKey.format(id));
            if (cacheData) {
                init.showBonus(cacheData);
                return false;
            }
            init.loading = true;
            $.wpost($.httpProtocol.GET_BONUS_DETAIL,{id:id},function (data) {
                $.cache.set(init.cacheKey.format(id), data);
                init.loading = false;
                init.showBonus(data);
            },function () {
                init.loading = false;
            });
        },

        initBtnEvent : function () {
            /**
             * 上下拉分页
             * @type {jQuery|HTMLElement|*|jQuery}
             */
            $.cache.remove($.httpProtocol.GET_BONUS_LIST);
            init.pager = $('#page').DropLoadPager({
                protocol:$.httpProtocol.GET_BONUS_LIST
            });

            /**
             * 跳转详情
             */
            $(document).on('click', init.itemNode,function () {
                var $this = $(this);
                var id = $this.data('id');
                init.getBonusDetail(id);
            });

        },
        run : function () {
            //搜索
            init.initBtnEvent();

        }
    };
    init.run();
})($);