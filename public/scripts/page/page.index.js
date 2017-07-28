/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        searchInputMask : $('.input-mask'),
        searchInputNode : '.bcb-search',
        loading :false,

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
        }
    };
    init.run();
})($);