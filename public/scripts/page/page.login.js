/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        verifyCodeBtn : $('.verify-code'),
        submitBtn : $('#js_login_btn'),
        loginTypeBtn : $('#js_login_type_btn'),
        loading :false,

        initWaitingSecond : function (value) {
            var opts = {
                target:init.verifyCodeBtn,
                defaultStr:'获取验证码',
                initStartTimeKey:'js_verify_code_init_time_login',
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
                type:1,
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
                    type:1,
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
                    $.showToast($.string.PLEASE_GET_VERIFY_CODE);
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
                if (!init.submitBtn.hasClass('btn-stress')) {
                    $.showToast($.string.PLEASE_CHECKED_USER_POLICY);
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
                })
            });

            /**
             * 同意选择框
             */
            $('#read_btn').unbind().bind('change', function () {
                init.submitBtn.toggleClass('btn-stress');
            });

            var fixBtn = $('.lf-fixed-btn');
            var wHeight = $(window).height();
            fixBtn.css('top', wHeight -40);
            
        },
        run : function () {
            init.initBtnEvent();
            // $.canvasAntCollision('canvas');
        }
    };
    init.run();

})($);