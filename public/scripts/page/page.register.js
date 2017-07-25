/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        section : '#js_register_section',
        verifyCodeBtn : $('.verify-code'),
        submitBtn : $('#js_register_btn'),
        loading :false,

        initWaitingSecond : function (value) {
            var opts = {
                target:init.verifyCodeBtn,
                defaultStr:'获取验证码',
                initStartTimeKey:'js_verify_code_init_time',
                waitingSecond :60,
                interval :'',
                getRemainSec : function () {
                    var startTime = $.cookie(opts.initStartTimeKey);
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
                    $.cookie(opts.initStartTimeKey,value,{ expires: 1, path: '/' });
                }
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
                password:$.trim($("#js_ar_password").val()),
                checked:$("#read_btn").prop('checked'),
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

            if(!params.checked){
                $.showToast($.string.AGREEMENT_MUST_CHECKED,false);
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
                    type:0,
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
                $.cache.remove();
                $.wpost($.httpProtocol.REGISTER,params,function (data) {
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