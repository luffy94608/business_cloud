/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        searchInputMask : $('.input-mask'),
        searchInputNode : '.bcb-search',
        loading :false,
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
             * tab切换
             */
            $(document).on('click', '.tab-item',function () {
                
            });

            $('select').select2({})

        },
        run : function () {
            //
            init.initBtnEvent();
            init.circleProgressEvent();
            init.fixBodyHeight();
        }
    };
    init.run();



})($);
