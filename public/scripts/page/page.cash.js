/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        submitBtn :$('#js_submit'),
        inputCode :$('#js_input_code'),
        pager :'',
        loading :false,
        initBtnEvent : function () {
            // /**
            //  * 分页
            //  * @type {jQuery}
            //  */
            // init.pager = $('#page').Pager({
            //     protocol:$.httpProtocol.GET_BILL_LIST
            // });
            // init.pager.updateList();

            /**
             * 上下拉分页
             * @type {jQuery|HTMLElement|*|jQuery}
             */
            $.cache.remove($.httpProtocol.GET_BILL_LIST);
            init.pager = $('#page').DropLoadPager({
                protocol:$.httpProtocol.GET_BILL_LIST
            });

        },
        run : function () {
            init.initBtnEvent();
        }
    };
    init.run();
})($);