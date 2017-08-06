/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        searchInputMask : $('.input-mask'),
        searchInputNode : '.bcb-search',
        pager :'',
        loading :false,
        

        pageEvent : function () {
            init.pager = $('#wrapperPageList').Pager({
                protocol:$.httpProtocol.SEARCH_LIST,
                listSize:6,
                onPageInitialized:function(){
                    if (init.pager){
                        var top =$('#wrapperPageList').offset().top;
                        setTimeout(function () {
                            $(window).scrollTop(top)
                        }, 300);
                    }
                },
                wrapUpdateData:function(idx,data){
                    var param={};
                    param.limit=0;
                    param.src=$.getQueryParams('src');
                    param.keyword=$.getQueryParams('keyword');
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
