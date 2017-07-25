(function ($) {
    /**
     * 获取伪url 解包内容
     */
    $.getHollogoUrlDecodeKey=function(url){
        var result=false;
        var mResult=url.match(/^hollogo:\/\/(.*)/i);
        if(url && mResult!==null){
            var tmpIndex=mResult[1].indexOf('?');
            var key=mResult[1];
            if(tmpIndex!==-1){
                key=key.substr(0,tmpIndex);
            }
            switch (key)
            {
                case 'recharge'://充值 
                    //result='/';
                    break;
                case 'invite_reward'://邀请有奖
                    //result='/';
                    break;
                case 'bus_line_list'://班车列表
                    result='/';
                    break;
                case 'shuttle_line_list'://摆渡车列表
                    result='/shuttle-list';
                    break;
                case 'tour_line_list'://旅游线路列表
                    // result='/travel-list';
                    break;
                case 'bus_ticket_list'://班车车票凭证
                    result='/my-order?type=0';
                    break;
                case 'shuttle_ticket_list'://快捷巴士车票凭证
                    result='/my-order?type=1';
                    break;
                case 'tour_ticket_list'://旅游车票凭证
                    // result='/travel-ticket';
                    break;
                case 'company_verify'://企业认证
                    //result='/';
                    break;
                case 'mission_reward'://任务领取
                    // result='/my-task';
                    break;
                case 'recruit_line'://招募
                    // result='/my-custom-route/1/0';
                    break;
                case 'my_purse'://钱包列表
                    result='/auth/account';
                    break;
                case 'my_balance'://账户余额
                    result='/auth/cash';
                    break;
                case 'my_coupon'://优惠券
                    result='/auth/coupons';
                    break;
                case 'my_bonus'://红包
                    result='/auth/bonus';
                    break;
            }
        }
        return result;
    };

    /**
     * 红包显示
     * @param opts
     */
    $.checkRedPacketBonusEvent = function (opts) {
        var st = {
            bonus_package : false,
            modalNode : '#js_red_packet_modal',
            contentNode : '#js_rpm_content',
            clickBtnNode : '.js_bonus_click_btn',
            closeBtnNode : '.js_bonus_close_btn',
            shareBtnNode : '.js_bonus_share_btn',
            hasBonus : false,
            template : require('art-template'),
            styleTplMap : {
                bonus  : 'js_red_style_bonus',
                style1 : 'js_red_style_1',
                style2 : 'js_red_style_2',
                style3 : 'js_red_style_3',
                style4 : 'js_red_style_4'
            }
        };
        if (typeof opts ===  'object') {
            $.extend(st,opts);
        }
        var modal = $(st.modalNode);

        /**
         * 判断是否有红包信息
         */
        var packetInfo = $.cache.get($.bonusKey);
        if (st.bonus_package) {
            packetInfo = st.bonus_package;
        }
        // //TODO test
        // packetInfo =  {
        //     "show_type":1,        // [0|1|2] 0:无extra show  1:show in view 如alert、红包和预定义的在页面中浮出的视图  2:pop 如需要跳入的页面
        //     "jump_url":"",       // 当show_type = 2 jump_url 有效 且为跳转的url
        //     "content" : {        // 不同类型返回不同结构,
        //         "tag" : "style3",      // 为显示的样式，包括bonus(红包) ，alert(弹出的alert) ，style1，style2，style3，style4（对应相应的样式）等
        //         "data" : {
        //             "title":"aaaa",
        //             "description":"aaaa",
        //             "link":"aaaa",
        //             "wechat_desc":"这是一个测试的红包",
        //             "wechat_img":"aaaa",
        //             "wechat_title":"发红包了" ,
        //             "id":"aaaa"
        //         }
        //     },        // 内容暂时是红包的结构，其他待定
        //     "display" :{            // 显示几个按钮以及按钮的功能，当是红包时无效
        //         "buttons":[
        //             // {
        //             //     "title":"取消",
        //             //     "action":"dismiss",
        //             //     "jump_url":"hollogo://bus_line_list"
        //             // },
        //             {
        //                 "title":"发红包",
        //                 "action":"share",
        //                 "jump_url":"hollogo://shuttle_ticket_list"
        //             }
        //             ]
        //         }
        // };
        if(packetInfo){
            if (typeof  packetInfo === 'string') {
                packetInfo=JSON.parse(packetInfo);
            }
            if(packetInfo && packetInfo.content && packetInfo.content.data && packetInfo.content.tag!== 'alert')
            {
                var shareInfo=packetInfo.content.data;
                var shareOpt = {
                    link:shareInfo.link,
                    title:shareInfo.wechat_title? shareInfo.wechat_title :'小伙伴们，哈叔发红包啦！',
                    desc:shareInfo.wechat_desc? shareInfo.wechat_desc :"哈叔请你坐班车！戳这里领取红包，免费乘坐班车，上下班再也不拥挤！",
                    imgUrl:shareInfo.wechat_img? shareInfo.wechat_img : window.location.origin+'/images/logo.png',// 分享图标
                    successFunc:function () {
                        $.showShareRemindModal('hide');
                        $('#js_red_packet_modal').removeClass('active');
                    }
                };
                $.initWxShareConfigWithData(shareOpt);
                st.hasBonus = true;
            }
        }
        //没有红包die
        if (!st.hasBonus || packetInfo.show_type !== $.bonusShowType.Show) {
             return false;
        }

        /**
         * 初始化红包
         */
        if (!st.styleTplMap[packetInfo.content.tag]) {
            return false;
        }
        var contentHtml = st.template(st.styleTplMap[packetInfo.content.tag], packetInfo);
        modal.find(st.contentNode).html(contentHtml);

        /**
         * 显示红包
         * @type {*}
         */
        modal.addClass('active');
        $.cache.remove($.bonusKey);

        /**
         * 关闭按钮
         */
        modal.on('click', st.closeBtnNode, function () {
            modal.removeClass('active');
            $.cache.remove($.bonusKey);
        });

        /**
         * 按钮点击事件
         */
        modal.on('click', st.clickBtnNode, function () {
            var $this = $(this);
            var hollogoUrl = $this.data('url');
            var action =  $this.data('action');

            if (!action) {
                return false;
            }
            switch (action) {
                case  'jump':
                    if($.isHttpUrl(hollogoUrl)){
                        $.locationUrl(hollogoUrl)
                    }else{
                        var jumpUrl = $.getHollogoUrlDecodeKey(hollogoUrl);
                        if(jumpUrl) {
                            $.locationUrl(jumpUrl)
                        }
                        modal.removeClass('active');
                    }
                    break;
                case  'share'://分享
                    $.showShareRemindModal();
                    break;
                case  'dismiss'://取消
                    modal.removeClass('active');
                    break;
            }

        });

        /**
         * 分享按钮
         */
        modal.on('click', st.shareBtnNode, function () {
            $.showShareRemindModal();
        });

    };

})($);


