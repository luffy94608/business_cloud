/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        submitBtn : $('#js_submit'),
        inputName : $('#js_input_name'),
        editBtn : $('.icon-edit'),
        showTitleTarget : $('.name-title'),
        loading :false,
        initParams : function () {
            var params = {
                name:$.trim(init.inputName.val()),
            };

            var status = $.checkInputVal({val:params.name,type:'name',onChecked:function(val,state,hint){
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
            //提交
            init.submitBtn.unbind().bind('click',function () {
                if($(this).hasClass('disabled')){
                    return false;
                }

                if(init.loading){
                    return false;
                }
                var params = init.initParams();
                if(!params){
                    return false;
                }
                if (init.inputName.data('src') == params.name) {
                    init.changeSubmitEvent(0);
                    return false;
                }
                init.loading = true;
                $.wpost($.httpProtocol.UPDATE_PROFILE,params,function (data) {
                    $.showToast($.string.EDIT_SUCCESS,true);
                    init.changeSubmitEvent(0);
                    init.inputName.data('src', params.name);
                    init.showTitleTarget.html( params.name);
                    init.loading = false;
                },function () {
                    init.loading = false;
                });
            });
            
            init.editBtn.unbind().bind('click', function () {
               init.changeSubmitEvent(1);
            });

            //退出
            $('#js_logout').unbind().bind('click', function () {
                $.confirm({
                    content:$.string.LOGOUT_CONFIRM_HINT,
                    titleShow:true,
                    success:function () {
                        $.cache.clear();
                        $.locationUrl('/auth/logout', true);
                    }
                });
            });

        },
        /**
         * 姓名编辑事件
         * @param status 0 normal 1 编辑状态
         */
        changeSubmitEvent:function (status) {
            if(status == 1){
                init.inputName.removeClass('gone').removeAttr('disabled');
                init.editBtn.hide();
                init.submitBtn.show();
                init.showTitleTarget.hide();
                init.inputName.focus();
                var val = init.inputName.val();
                init.inputName.val('');
                init.inputName.val(val);
            }else{
                init.inputName.addClass('gone').attr('disabled', true);
                init.submitBtn.hide();
                init.editBtn.show();
                init.showTitleTarget.show();
                init.inputName.blur();
            }
        },

        initData : function () {
            $.wpost($.httpProtocol.GET_PROFILE_INFO, {}, function (data) {
                var avatar = '/images/avatar.png';
                var name = '未知';
                var count = 0;
                var balance = 0;
                var coupon = 0;
                if (data && data.user) {
                    var user = data.user;
                    avatar = user.avatar ? '{0}{1}'.format(document.global_config_data.upyun_host, user.avatar) : avatar;
                    name = user.name;
                    count = data.contract_count;
                    balance = data.balance;
                    coupon = data.coupon_count;
                }

                $('.js_profile_img').attr('src', avatar);
                init.inputName.data('src', name).val(name);
                $('.js_profile_name').html(name);
                $('.js_profile_ticket_count').html(count);
                $('.js_profile_cash').html(balance);
                $('.js_profile_coupon').html(coupon);
            }, function () {
            })
        },

        run : function () {
            init.initBtnEvent();
            init.initData();
        }
    };
    init.run();



})($);