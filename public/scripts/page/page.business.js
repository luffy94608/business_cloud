/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        section : '#js_register_section',
        submitBtn : $('#js_input_submit'),

        inputCompanyName: $('#js_input_company'),
        loading :false,
        tpl :'<span class="bck-item active" data-id="{0}">{1}<i class="b-icon-close ml-5"></i></span>',

        /**
         * 表单验证
         * @returns {*}
         */
        initParams : function () {
            var params = {
                company_name:$.trim(init.inputCompanyName.val())
            };

            var status;
            status = $.checkInputVal({val:params.company_name,type:'company',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }
            });
            if(status<=0){
                return false;
            }
            return params;
        },
        
        initBtnEvent : function () {

            /**
             * 提交
             */
            init.submitBtn.unbind().bind('click',function () {
                if(init.loading){
                    return false;
                }
                var params = init.initParams();
                if(!params){
                    return false;
                }
                init.loading = true;
                $.wpost($.httpProtocol.USER_BUSINESS,params,function (data) {
                    $.showToast($.string.COMPANY_OR_BUSINESS_SUCCESS, true);
                    // $.locationUrl('/login');
                    setTimeout(function () {
                        window.location.reload();
                        init.loading = false;
                    });
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