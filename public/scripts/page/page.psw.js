/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        submitBtn : $('#js_login_btn'),
        loading :false,

        initParams : function () {
            var params = {
                mobile:$.trim($("#js_ar_mobile").val()),
                password:$.trim($("#js_ar_password").val()),
                type:2
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

            status = $.checkInputVal({val:params.password,type:'password',onChecked:function(val,state,hint){
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
             * 密码登录
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
                $.wpost($.httpProtocol.LOGIN,params,function (data) {
                    var callback = $.getQueryParams('callback');
                    var url  = ( callback && callback.length) ? callback : '/auth/account';
                    $.locationUrl(url, true);
                    init.loading = false;
                },function () {
                    init.loading = false;
                });
            });

            var fixBtn = $('.lf-fixed-btn');
            var wHeight = $(window).height();
            fixBtn.css('top', wHeight -40);

        },
        run : function () {
            init.initBtnEvent();
            // $.canvasAntCollision('canvas');
            // $.canvasAntCollision('canvas', {
            //     isRandColor: true,
            //     hasLine: false
            // });
        }
    };
    init.run();

})($);