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
            /**
             * 上下拉分页
             * @type {jQuery|HTMLElement|*|jQuery}
             */
            $.cache.remove($.httpProtocol.GET_COUPON_LIST);
            init.pager = $('#page').DropLoadPager({
                protocol:$.httpProtocol.GET_COUPON_LIST
            });

            /**
             * 注册
             */
            init.submitBtn.unbind().bind('click', function () {
                if(init.loading){
                    return false;
                }
                var code = $.trim(init.inputCode.val());
                if(!code.length){
                    $.showToast($.string.EXCHANGE_CODE_NOT_EMPTY,false);
                    return false;
                }
                
                init.loading = true;
                $.wpost($.httpProtocol.EXCHANGE_SHARE_CODE,{code:code},function (data) {
                    init.inputCode.val('');
                    window.location.reload();
                    init.loading = false;
                },function () {
                    init.loading = false;
                })
            });


        },
        run : function () {
            init.initBtnEvent();
        }
    };
    init.run();
})($);