/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        searchInputMask : $('.input-mask'),
        searchInputNode : '.bcb-search',
        pager :'',
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
       
        pageEvent : function () {
            init.pager = $('#wrapperPageList').Pager({
                protocol:$.httpProtocol.GET_BID_LIST,
                listSize:6,
                onPageInitialized:function(){
                    if (init.pager){
                        var top=$(window).scrollTop();
                        var top =$('#wrapperPageList').offset().top;
                        if(top>100){
                            $(window).scrollTop(top)
                        }
                    }
                },
                wrapUpdateData:function(idx,data){
                    var param={};
                    param.limit=2;
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
             * tab切换
             */
            $(document).on('click', '.tab-item',function () {
                
            });

        },
        run : function () {
            //
            init.initBtnEvent();
            init.pageEvent();
        }
    };
    init.run();



})($);
