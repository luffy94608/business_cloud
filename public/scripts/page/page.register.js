/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        section : '#js_register_section',
        modal : $('#js_confirm_modal'),
        verifyCodeBtn : $('#js_get_code_btn'),
        submitBtn : $('#js_input_submit'),
        
        inputMobile: $('#js_input_mobile'),
        inputPsw: $('#js_input_psw'),
        inputCode: $('#js_input_code'),
        inputName: $('#js_input_name'),
        inputSex: $('#js_input_sex'),
        inputJob: $('#js_input_job'),
        inputEmail: $('#js_input_email'),
        inputCompanyName: $('#js_input_company'),
        inputCompanyArea: $('#js_input_company_area'),
        inputCompanyIndustry: $('#js_input_company_industry'),
        inputFollowIndustry: $('#js_input_follow_industry'),

        followAreaListSection : $('#js_follow_area_list'),
        keywordListSection : $('#js_follow_keyword_list'),
        followAreaAddBtn : $('#js_follow_area_add_btn'),
        keywordAddBtn : $('#js_keyword_add_btn'),
        keywordInput : $('#js_keyword_input'),
        loading :false,
        tpl :'<span class="bck-item active" data-id="{0}">{1}<i class="b-icon-close ml-5"></i></span>',

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
                psw:$.trim(init.inputPsw.val()),
                name:$.trim(init.inputName.val()),
                gender:$.trim(init.inputSex.val()),
                job:$.trim(init.inputJob.val()),
                email:$.trim(init.inputEmail.val()),
                company_name:$.trim(init.inputCompanyName.val()),
                company_area:$.trim(init.inputCompanyArea.val()),
                company_industry:$.trim(init.inputCompanyIndustry.val()),
                follow_industry:$.trim(init.inputFollowIndustry.val()),
                follow_area:init.getItemArrById(init.followAreaListSection).join(','),
                follow_keyword:init.getItemArrById(init.keywordListSection).join(',')
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


            status = $.checkInputVal({val:params.name,type:'name',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }
            });
            if(status<=0){
                return false;
            }

            if (params.gender<1) {
                $.showToast($.string.GENDER_MUST,false);
                return false;
            }

            status = $.checkInputVal({val:params.job,type:'job',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }
            });
            if(status<=0){
                return false;
            }

            status = $.checkInputVal({val:params.email,type:'email',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }
            });
            if(status<=0){
                return false;
            }

            status = $.checkInputVal({val:params.company_name,type:'company',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }
            });
            if(status<=0){
                return false;
            }

            if (params.follow_area.length<1) {
                $.showToast($.string.AREA_MUST_ADD, false);
                return false;
            }

            if (params.follow_keyword.length<1) {
                $.showToast($.string.KEYWORD_MUST_ADD, false);
                return false;
            }

            return params;
        },
        /**
         * 获取结果集
         * @param target
         * @returns {Array}
         */
        getItemArrById : function (target) {
            var list = [];
            target.find('.bck-item ').each(function (index, item) {
                var id = $(item).data('id');
                list.push(id);
            });
            return list;
        },
        modalEvent : function () {
            init.modal.find('.js_modal_confirm').unbind().bind('click', function () {
                $.locationUrl('/');
            });
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

            /**
             * 注册
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
                $.wpost($.httpProtocol.REGISTER,params,function (data) {
                    var companyName = init.inputCompanyArea.find('option:checked').text();
                    init.modal.find('.js_location_html').html(companyName);
                    init.modal.find('.js_agent_mobile').html(data.mobile);
                    init.modal.find('.js_agent_address').html(data.address);
                    init.modal.modal();
                    // $.showToast($.string.REGISTER_SUCCESS, true);
                    // $.locationUrl('/');
                    init.loading = false;
                },function () {
                    init.loading = false;
                })
            });

            /**
             * 关键词添加
             */
            init.keywordAddBtn.unbind().bind('click', function () {
                var keyword = $.trim(init.keywordInput.val());
                if (keyword.length<1) {
                    $.showToast($.string.KEYWORD_MUST,false);
                    return false;
                }
                var arr = init.getItemArrById(init.keywordListSection);
                if (arr.indexOf(keyword)!==-1) {
                    $.showToast($.string.KEYWORD_EXISTS,false);
                    return false;
                }

                var html = init.tpl.format(keyword, keyword);
                init.keywordListSection.append(html);
                init.keywordInput.val('');
                    
            });

            /**
             * 关注区域添加
             */
            init.followAreaAddBtn.bind('change', function () {
                var val = parseInt($.trim($(this).val()));
                if (val == -1) {
                    return false;
                }
                var text = $.trim($(this).find('option:checked').text());
                var arr = init.getItemArrById(init.followAreaListSection);
                init.followAreaAddBtn.select2('val', -1);
                if (arr.indexOf(val)!==-1) {
                    $.showToast($.string.AREA_EXISTS,false);
                    return false;
                }
                var html = init.tpl.format(val, text);
                init.followAreaListSection.append(html);
            });

            /**
             * 删除事件
             */
            $(document).on('click', '.bck-item .b-icon-close',function () {
                $(this).parents('.bck-item').remove();
            });
        },
        run : function () {
            init.initBtnEvent();
            init.modalEvent();
            $('select').select2()
        }
    };
    init.run();

})($);