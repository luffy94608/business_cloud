/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        verifyCodeBtn : $('#js_get_code_btn'),
        submitBtn : $('#js_input_submit'),

        inputMobile: $('#js_input_mobile'),
        inputPsw: $('#js_input_psw'),
        inputCode: $('#js_input_code'),
        loading :false,

        /**
         * 验证码倒计时
         * @param value
         * @returns {boolean}
         */
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

        /**
         * 表单验证
         * @returns {*}
         */
        initParams : function () {
            var params = {
                mobile:$.trim(init.inputMobile.val()),
                code:$.trim(init.inputCode.val()),
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
            if(!params.code.length){
                $.showToast($.string.VERIFY_CODE_NOT_EMPTY,false);
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
            /**
             * 验证码倒计时
             */
            init.initWaitingSecond();
            init.verifyCodeBtn.unbind().bind('click',function () {
                if(init.initWaitingSecond('status') || init.loading){
                    return false;
                }

                var params = {
                    type:0,
                    mobile:$.trim(init.inputMobile.val())
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
                    $.showToast($.string.VERIFY_CODE_SEND_SUCCESS, true);
                    init.loading = false;
                },function () {
                    init.loading = false;
                });


            });

           
            init.submitBtn.unbind().bind('click',function () {
                if(init.loading){
                    return false;
                }
                var params = init.initParams();
                if(!params){
                    return false;
                }
                init.loading = true;
                $.wpost($.httpProtocol.RESET,params,function (data) {
                    $.showToast($.string.EDIT_SUCCESS, true);
                    // $.locationUrl('/login');
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