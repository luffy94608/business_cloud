/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        verifyCodeBtn : $('.verify-code'),
        submitBtn : $('#js_reset_btn'),
        loading :false,

        initWaitingSecond : function (value) {
            var opts = {
                target:init.verifyCodeBtn,
                defaultStr:'获取验证码',
                initStartTimeKey:'js_verify_code_init_time_reset',
                waitingSecond :120,
                interval :'',
                getRemainSec : function () {
                    var startTime = $.cache.get(opts.initStartTimeKey);
                    startTime = startTime ? parseInt(startTime) : 0;
                    return Math.floor(parseInt(startTime + opts.waitingSecond - new Date().getTime()/1000));
                },
                running : function () {
                    var str = '';
                    if(opts.getRemainSec()<0){
                        str = opts.defaultStr;
                        opts.target.removeClass('active');
                        clearInterval(init.interval);
                    }else{
                        str = opts.getRemainSec()+' s';
                        opts.target.addClass('active');
                    }
                    opts.target.html(str);
                }
            };

            if(typeof value != 'undefined'){
                if(value == 'status'){
                    return  (opts.getRemainSec() <0) ? false : true;
                }else{
                    $.cache.set(opts.initStartTimeKey,value);
                }
            } else {
                $.cache.set(opts.initStartTimeKey,-1);
            }

            opts.running();
            if(opts.getRemainSec() <0){
                return false;
            }

            init.interval = setInterval(function () {
                opts.running();
            },1000);

        },

        initParams : function () {
            var params = {
                mobile:$.trim($("#js_ar_mobile").val()),
                code:$.trim($("#js_ar_code").val()),
                password:$.trim($("#js_ar_password").val())
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

            if(!params.code.length){
                $.showToast($.string.VERIFY_CODE_NOT_EMPTY,false);
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
             * 验证码倒计时
             */
            init.initWaitingSecond();
            init.verifyCodeBtn.unbind().bind($.getClickEventName(),function () {
                if(init.initWaitingSecond('status') || init.loading){
                    return false;
                }

                var params = {
                    type:2,
                    mobile:$.trim($('#js_ar_mobile').val())
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
                init.loading = true;
                $.wpost($.httpProtocol.GET_VERIFY_CODE,params,function (data) {
                    var startTime = Math.floor(new Date().getTime()/1000);
                    init.initWaitingSecond(startTime);
                    init.loading = false;
                },function () {
                    init.loading = false;
                });


            });


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
                $.wpost($.httpProtocol.RESET,params,function (data) {
                    var callback = $.getQueryParams('callback');
                    var url  = ( callback && callback.length) ? callback : '/auth/account';
                    $.locationUrl(url);
                    init.loading = false;
                },function () {
                    init.loading = false;
                })
            });
            var inputObj = $('input');
            var fixBtn = $('.lf-fixed-btn');
            inputObj.on('focus',function () {
                fixBtn.hide();
            });
            inputObj.on('blur',function () {
                fixBtn.show();
            });
        },
        run : function () {
            init.initBtnEvent();
            // $.canvasAntCollision('canvas');
        }
    };
    init.run();

})($);