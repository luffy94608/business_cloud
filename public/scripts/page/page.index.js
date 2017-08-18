/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        searchInputMask : $('.input-mask'),
        searchInputNode : '.bcb-search',
        pager :'',
        first :true,
        loading :false,

        /**
         * 进度条初始化
         */
        progressEvent : function () {
            var target = $('.progress-bar');
            target.each(function (i,dom) {
                var item = $(dom);
                var percent = item.data('percent');
                item.css('width', percent);
            });
        },

        /**
         * 初始化进度条
         */
        circleProgressEvent : function () {
            $('canvas.vie-num').each(function() {
                // 获取进度数值
                var $this = $(this);
                var process = parseInt($this.text());
                var color = process > 50 ? '#FF7C7C' : '#24ca88';
                $.drawCircleProcess(this, process, color);
                var colorClass = process > 50 ? 'color-red' : 'color-green';
                $this.siblings('.vie-text').removeClass('color-red color-green').addClass(colorClass);
            });
        },
        /**
         * 首页内容区域和 side 高度一直
         */
        fixBodyHeight:function () {
            if ($(window).width()<768) {
                return false;
            }
            var sideSection = $('.bc-side-section');
            var bodySection = $('.bc-stat-section');
            var wrapSection = $('.bc-body-section');
            var bodyHeadSection = $('.bc-section-title', wrapSection);
            var bodyContentSection = $('.d-table', wrapSection);
            var sh = sideSection.height();
            var bh = bodySection.height();
            var bhh = bodyHeadSection.height();
            if (bh < sh) {
                bodySection.height(sh);
                bodyContentSection.height(sh-bhh);
            } else {
                sideSection.height(bh);
                bodyContentSection.height(bh-bhh);
            }
            setTimeout(function () {
                init.progressEvent();
            }, 300);
        },
        pageEvent : function () {
            init.pager = $('#wrapperPageList').Pager({
                protocol:$.httpProtocol.GET_BID_LIST,
                listSize:6,
                onPageInitialized:function(){
                    if (init.pager){
                        var top=$(window).scrollTop();
                        var top =$('#wrapperPageList').offset().top;
                        if(!init.first){
                            $(window).scrollTop(top)
                        }
                        init.first = false;
                    }
                },
                wrapUpdateData:function(idx,data){
                    var param={};
                    param.limit=5;
                    if (param){
                        $.extend(data, param);
                    }
                    return data;
                }
            });
            init.pager.updateList(0);
        },

        initBtnEvent : function () {

            /**
             * 跳转详情
             */
            $(document).on('focus', init.searchInputNode,function () {
                init.searchInputMask.hide();
            });
            $(document).on('blur', init.searchInputNode,function () {
                init.searchInputMask.fadeIn();
            });

            /**
             * hover
             */
            var listItemTarget = $('.js_border_item');
            $('.bc-item-hover').hover(function () {
                var index = $(this).index();
                listItemTarget.addClass('border-right');
                switch (index) {
                    case 0:
                        listItemTarget.eq(0).removeClass('border-right');
                        break;
                    case 1:
                        listItemTarget.removeClass('border-right');
                        break;
                    case 2:
                        listItemTarget.eq(1).removeClass('border-right');
                        break;
                }
            });

        },
        run : function () {
            //
            init.initBtnEvent();
            // init.circleProgressEvent();
            init.fixBodyHeight();
            init.pageEvent();
        }
    };
    init.run();



})($);
