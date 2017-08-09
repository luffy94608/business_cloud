/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        inputMobile: $('#js_input_name'),
        inputPsw: $('#js_input_psw'),
        submitBtn : $('#js_input_submit'),
        loading :false,

        initParams : function () {
            var params = {
                mobile:$.trim(init.inputMobile.val()),
                psw:$.trim(init.inputPsw.val())
            };

            var status;
            status = $.checkInputVal({val:params.mobile,type:'mobile',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }
            });
            if(status<=0){
                return false;
            }


            status = $.checkInputVal({val:params.psw,type:'password',onChecked:function(val,state,hint){
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
           
            init.submitBtn.unbind().bind('click',function () {
                if(init.loading){
                    return false;
                }
                var params = init.initParams();
                if(!params){
                    return false;
                }
                init.loading = true;
                $.wpost($.httpProtocol.LOGIN,params,function (data) {
                    var callback = $.getQueryParams('callback');
                    var url  = ( callback && callback.length) ? callback : '/';
                    $.showToast($.string.LOGIN_SUCCESS, true);
                    $.locationUrl(url, true);
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
    // init.run();

})($);