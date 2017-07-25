/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        verifyCodeBtn : $('.verify-code'),
        submitBtn : $('#js_reset_btn'),
        loading :false,

        initParams : function () {
            var params = {
                password:$.trim($("#js_ae_src_password").val()),
                new_password:$.trim($("#js_ae_new_password").val()),
                new_password_confirmation:$.trim($("#js_ae_confirm_password").val())
            };
            var status;
            if(!params.password.length){
                $.showToast($.string.SRC_PSW_MUST,false);
                return false;
            }

            status = $.checkInputVal({val:params.new_password,type:'password',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast($.string.SRC_NEW_PSW_MUST,false);
                }
            }
            });
            if(status<=0){
                return false;
            }

            if(params.new_password != params.new_password_confirmation){
                $.showToast($.string.CONFIRMED_PSW_ERROR,false);
                return false;
            }

            return params;
        },
        initBtnEvent : function () {

            /**
             * 注册
             */
            init.submitBtn.unbind().bind($.getClickEventName(),function () {
                if(init.loading){
                    return false;
                }
                var params = init.initParams();
                if(!params){
                    return false;
                }
                init.loading = true;
                $.wpost($.httpProtocol.EDIT_PSW,params,function (data) {
                    $.showToast($.string.EDIT_SUCCESS,true);
                    $("#js_ae_src_password").val('');
                    $("#js_ae_new_password").val('');
                    $("#js_ae_confirm_password").val('');
                    init.loading = false;
                },function () {
                    init.loading = false;
                });
            });
        },
        run : function () {
            init.initBtnEvent();
        }
    };
    init.run();

})($);