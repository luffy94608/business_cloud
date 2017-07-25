(function($){
    /**
     * 注册fastclick
     */
    var FastClick = require('fastclick');
    FastClick.attach(document.body);

    /**
     * 对象是否为空
     * @param e
     * @returns {boolean}
     */
    $.isEmptyObject = function(e){
        if (!e) return !0;
        var t;
        for (t in e)
            return !1;
        return !0
    };

    $.rad  = function (d){
        return d * Math.PI / 180.0;//经纬度转换成三角函数中度分表形式。
    };
    
    /**
     * 计算两点间的距离
     * @param lat1
     * @param lng1
     * @param lat2
     * @param lng2
     * @returns {number} 米
     */
    $.calcDistanceBetweenPoints = function(lat1,lng1,lat2,lng2) {
        var radLat1 = $.rad(lat1);
        var radLat2 = $.rad(lat2);
        var a = radLat1 - radLat2;
        var  b = $.rad(lng1) - $.rad(lng2);
        var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a/2),2) +
                Math.cos(radLat1)*Math.cos(radLat2)*Math.pow(Math.sin(b/2),2)));
        s = s *6378.137 ;// EARTH_RADIUS;
        // s = (Math.round(s * 10000) / 10000); //输出为公里
        s = (Math.round(s * 10000) / 10); //输出为公里
        //s=s.toFixed(4);
        return s;
    };

    /**
     * 格式化距离
     * @param val
     * @returns {string}
     */
    $.distanceFormat = function (val) {
        val = parseInt(val);
        var str = '';
        if (val>1000) {
            str  = '{0} km'.format((val/1000).toFixed(1));
        } else {
            str  = '{0} m'.format(val);
        }
        return str;
    };

    /**
     * 根据 时间获取时间错
     * @param str
     * @returns {number}
     */
    $.buildTimeStampInTimeStr = function(str){
        var date = new Date();
        var arr = str.split(':');
        date.setHours(arr[0]);
        date.setMinutes(arr[1]);
        return Math.floor(date.getTime()/1000);
    };

    /**
     * 判断是否是http或https url
     */
    $.isHttpUrl=function(url){
        var result=false;
        if(url && url.match(/^http[s]?:\/\/.+/i)!==null){
            result=true;
        }
        return result;
    };


    /**
     * 记住当前页面滚动条高度
     */
    $.recordPageScrollTopHeight=function(initValue){
        var scrollTop=$(window).scrollTop();
        if(typeof initValue !== 'undefined'){
            scrollTop=initValue;
        }
        $.cache.set('JS_PAGE_SCROLL_TOP'+encodeURIComponent(window.location.pathname), scrollTop);
    };
    /**
     * 获取当前记录位置
     * @param initValue
     * @returns {number}
     */
    $.getRecordPageScrollTopHeight=function(){
        var scrollTop=$.cache.get('JS_PAGE_SCROLL_TOP'+encodeURIComponent(window.location.pathname));
        return scrollTop  ? scrollTop : 0;
    };

    /**
     * 日期格式化
     * @param fmt
     * @returns {*}
     * @constructor
     */
    Date.prototype.Format = function(fmt){
        var weekMap = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'];
        var amPm = ['上午','下午'];
        var o = {
            "M+" : this.getMonth()+1,                 //月份
            "d+" : this.getDate(),                    //日
            "h+" : this.getHours(),                   //小时
            "m+" : this.getMinutes(),                 //分
            "s+" : this.getSeconds(),                 //秒
            "q+" : Math.floor((this.getMonth()+3)/3), //季度
            "S"  : this.getMilliseconds(),             //毫秒
            "a"  : this.getHours() >=12 ? amPm[1] : amPm[0],             //毫秒
            "w"  : weekMap[this.getDay()]             //星期
        };
        if(/(y+)/.test(fmt))
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
        for(var k in o)
            if(new RegExp("("+ k +")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        return fmt;
    };

    /**
     * 获取url参数
     * @param name
     * @returns {*}
     */
    $.getQueryParams = function (name) {
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  decodeURIComponent(r[2]); return null;
    };

    $.log = function(v){
        console.log(v);
    };

    /**
     * 获取当前url
     * @returns {string}
     */
    $.getLocationUrl = function(){
        return window.location.href;
    };

    /**
     * 倒计时
     * @param timestamp
     * @returns {string}
     */
    $.showEndTime =function(timestamp) {
        var oldTime=new Date(timestamp*1000);
        var nowTime=new Date();
        var oStamp=oldTime.getTime();
        var nStamp=nowTime.getTime();
        var lastStamp=(oStamp-nStamp)/1000;
        var day=Math.floor(lastStamp/(60*60*24));
        var hour=Math.floor(lastStamp%(60*60*24)/(60*60));
        var minute=Math.floor(lastStamp%(60*60)/(60));
        var second=Math.floor(lastStamp%60);
        $('#day').html(day);
        if(hour<10){
            hour='0'+hour;
        }
        if(minute<10){
            minute='0'+minute;
        }
        if(second<10){
            second='0'+second;
        }
        
        var str=hour+":"+minute+":"+second;
        return str;
    };

    /**
     * 浏览器ua判断
     * @type {{version: *, safari: boolean, opera: boolean, msie: boolean, mozilla: boolean, iPhone: boolean, android: boolean, inWeChat: boolean, inWeibo: boolean}}
     */
    var userAgent = navigator.userAgent.toLowerCase();
    $.browser =  {
        version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [0,'0'])[1],
        safari: /webkit/.test( userAgent ),
        opera: /opera/.test( userAgent ),
        msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
        mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent ),
        iPhone:userAgent.indexOf('iphone') > -1 ||  userAgent.indexOf('ipad') > -1,
        android:userAgent.indexOf('android') > -1 || userAgent.indexOf('linux') > -1,
        inWeChat: userAgent.indexOf('micromessenger') > -1,
        inWeibo:userAgent.indexOf('weibo') > -1
    };

    /**
     * 获取点击事件
     * @returns {*}
     */
    $.getClickEventName = function () {
        return 'click';
        return ($.browser.iPhone || $.browser.android) ? 'touchend' : 'click';
    };

    /**
     * 跳转
     * @param url
     * @param status
     */
    $.locationUrl = function (url,status) {
        if (status) {
            window.location.replace(url);
        } else {
            window.location.href = url;
        }
    };

    /**
     * 全局跳转跳转
     */
    $.ALocationUrlEvent = function () {
        var opts = {
            target : 'a.js_location_url'
        };
        $(document).on('click' , opts.target, function (e) {
            e.stopPropagation();
            e.preventDefault();
            var url = $(this).attr('href');
            var replace = $(this).data('replace');
            var pathIndex = url.indexOf('?');
            var paramsStr = pathIndex === -1 ? '' : url.substr(pathIndex);
            var paramsArr = [];
            if (paramsStr.length) {
                var tmpArr = paramsStr.split(/[?&]/);
                if (tmpArr.length) {
                    for (var key in tmpArr) {
                        var item = tmpArr[key];
                       if (item && item.length) {
                           paramsArr.push(item);
                       }
                    }
                }
            }
            var callback = $.getQueryParams('callback');
            if (callback && callback.length) {
                paramsArr.push('callback=' + encodeURIComponent(callback));
            }
            if (paramsArr.length) {
                url = '{0}?{1}'.format(url, paramsArr.join('&'));
            }

            if (replace) {
                window.location.replace(url);
            } else {
                window.location.href = url;
            }
        });

    };

    /**
     * loading 动画
     * @param str
     */
    $.loadingToast = function (str) {
       var toast = $('#js_loading_toast_section');
        if (str == 'hide') {
            toast.removeClass('active');
            return;
        }
        if (str == 'status') {
            return toast.hasClass('active');
        }

        var textObj = toast.find('.toast-text');
        if(str){
            textObj.show().html(str);
        }else{
            textObj.hide()
        }

        toast.addClass('active');
    };

    /**
     * toast
     * @param str
     * @param YesOrNo
     * @param callback
     */
    $.showToast = function(str,YesOrNo,callback){
        var opts = {
            target : '#js_toast_section',
            success_icon : 'icon-right',
            error_icon : 'icon-cross',
            time : 2000
        };

        var target = $(opts.target);
        if (target.hasClass('active')) {
            return;
        }

        if(typeof YesOrNo != 'undefined'){
            var removeIcon = YesOrNo ? opts.error_icon : opts.success_icon;
            var addIcon = YesOrNo ? opts.success_icon : opts.error_icon;
            target.find('.toast-icon').show().removeClass(removeIcon).addClass(addIcon);
        }else{
            target.find('.toast-icon').hide();
        }

        target.find('.toast-txt').html(str);
        target.addClass('active');

        setTimeout(function(){
            target.removeClass('active');
            if($.isFunction(callback)){
                setTimeout(callback,300);
            }
        },opts.time);
    };

    /**
     * modal
     * @param str
     * @returns {boolean}
     */
    $.fn.modalDialog = function (str) {
        var st = {
            cancel_btn : '.js_cancel_btn'
        };
        var modal = $(this);
        if (str == 'hide') {
            modal.removeClass('active');
            return false;
        }
        modal.addClass('active');
        //modal 关闭
        // modal.find(st.cancel_btn).unbind().bind($.getClickEventName(),function () {
        //     modal.removeClass('active');
        // });
    };

    /**
     * 评分事件
     */
   $.initScoreEvent = function (options) {
       var opts = {
           container : '.remark-star',
           starClass : '.hl-star',
           hintMap : ['很差','差','一般','好','很好']
       };
       if (typeof options == 'object') {
           $.extend(opts,options);
       }
       var objs = $(opts.container);
       objs.each(function (index, item) {
           var target = $(item);
           var score = target.data('src') ? target.data('src') : 0;
           score = parseInt(score);

           var score2 = target.data('score') ? target.data('score') : 0;
           score2 = parseInt(score2);
           if (score == 0) {
               score = score2
           }
           if (score > 0) {
               for (var i =0 ;i<score;i++){
                   target.find(opts.starClass).eq(i).addClass('active');
               }
           }
       });

       objs.find(opts.starClass).unbind().bind('click',function () {
           var $this = $(this);
           var section = $this.parents(opts.container);
           var score = parseInt(section.data('src'));
           if(score > 0){
               return false;
           }
           $this.addClass('active').siblings().removeClass('active');
           var index = $this.index();
           for (var i =0 ;i<index;i++){
               section.find(opts.starClass).eq(i).addClass('active');
           }
           section.find('.hint').html(opts.hintMap[index]);
           section.data('score',index+1);
       });
   };

    /**
     * 班次选择 modal
     * @param options
     */
    $.fn.showShiftModal = function(options){
        var opts = {
            modal : '#js_shifts_dialog',
            titleNode : '.js_shift_title',
            title : '',
            inAnt : 'fadeInLeft',
            outAnt : 'fadeOutDown',
            targetNode:'.js_more_btn',
            shiftSecNode : '.shift-list',
            shiftNode : '.shift-item',
            shiftItemTemplate : '<span class="shift-item {0}" data-id="{1}">{1}</span>',
            cancel_btn : '.js_cancel_btn',
            changeFunc : '',
            selectVal : '',
            checked:false
        };

        if (typeof options == 'object') {
            $.extend(opts,options);
        }
        var modal = $(opts.modal);
        var $this = $(this);
        
        //init data
        var initData = function (object) {
            var info = object.data('info');
            if (typeof info !== 'object') {
                return;
            }
            var html = '';
            for (var key in info){
                var val = info[key];
                var active = val == opts.selectVal ? 'active' : '';
                html+= opts.shiftItemTemplate.format(active, val)
            }
            $(opts.shiftSecNode, modal).html(html);
        };

        $this.setVal = function (val) {
          opts.selectVal = val;
        };
        modal.on('touchmove', function(event) {
            event.preventDefault();
        });

        //title 显示
        $(opts.titleNode, modal).html(opts.title);
        var dialogContent = modal.find('.dialog');
        // dialogContent.addClass('animated');
        //modal 显示
        $(document).on($.getClickEventName(), opts.targetNode, function (e) {
            e.stopPropagation();
            initData($(this));
            modal.addClass('active');
            // dialogContent.removeClass(opts.inAnt + ' ' + opts.outAnt).addClass(opts.inAnt);
            $('body').css('overflow', 'hidden');
        });
        //modal 关闭
        modal.find(opts.cancel_btn).unbind().bind($.getClickEventName(),function () {
            // dialogContent.addClass(opts.outAnt);
            // setTimeout(function () {
            //     modal.removeClass('active');
            // },600);
            modal.removeClass('active');
            $('body').css('overflow', 'auto');
        });
        //checked 事件
        if (opts.checked) {
            $(opts.shiftSecNode).on($.getClickEventName(), opts.shiftNode,function () {
                var $cThis = $(this);
                $cThis.siblings().removeClass('active');
                $cThis.addClass('active');
                var val = $cThis.data('id');
                opts.selectVal = val;
                // dialogContent.addClass(opts.outAnt);
                // setTimeout(function () {
                //     modal.removeClass('active');
                // },600);
                modal.removeClass('active');
                $('body').css('overflow', 'auto');
                if ($.isFunction(opts.changeFunc)) {
                    opts.changeFunc(val);
                }
            });
        }
        return $this;
    };

    /**
     * 分享modal
     */
    $.showShareRemindModal = function(str){
        var opts = {
            target : '#js_share_hint_modal',
            contentNode : '.js_share_content',
            hintObj : $('.share-overlay'),
            cancel_btn : '.overlay,.share-overlay'
        };

        var target = $(opts.target);
        if (str == 'hide') {
            opts.hintObj.css('top','-115px');
            setTimeout(function () {
                target.removeClass('active');
            },300);
            return false;
        }
        if (target.hasClass('active')) {
            return;
        }

        if (str) {
            target.find(opts.contentNode).html(str);
        }
        
        target.addClass('active');
        setTimeout(function () {
            opts.hintObj.css('top','0%');
        },100);
        
        target.find(opts.cancel_btn).unbind().bind($.getClickEventName(),function () {
            opts.hintObj.css('top','-115px');
            setTimeout(function () {
                target.removeClass('active');
            },300);
        });

    };


    /**
     * 确认框
     * @param opts
     */
    $.confirm=function(opts){
        var options={
            'target':'#js_dialog_section',
            'title':'提示',
            'content':'提示内容',
            'cancelTitle':'取消',
            'confirmTitle':'确定',
            'subTitle':'',
            'subTitleTemplate':'<span class="bd-st">{0}</span>',
            'textCenter':true,
            'titleShow':false,
            'iconShow':false,
            'bgHide':false,
            'cancelBtnHide':false,
            'successBtnHide':false,
            'success':function(){},
            'cancel':function(){},
            buttons:[]
            // 'buttons':[
            //     {
            //         btn_name : '前往评论',
            //         callback : function () {alert(1)}
            //     },
            //     {
            //         btn_name : '稍后再说',
            //         callback : function () {alert(2)}
            //     }
            // ]
        };
        $.extend(options,opts);

        var modal=$(options.target);
        var headObj = modal.find('.bd-tt');
        var contentObj = modal.find('.bd-txt');
        var cancelBtn = modal.find('.js_cancel');
        var submitBtn = modal.find('.js_submit');
        var iconObj = modal.find('.bd-table');

        //content 居中
        if(options.textCenter){
            contentObj.removeClass('txt--left');
        }else{
            contentObj.addClass('txt--left');
        }
        //是否显示 标题
        if(options.titleShow){
            headObj.html(options.title);
            headObj.show();
        }else{
            headObj.hide();
        }
        var content = '';
        if(options.subTitle.length){
            content += options.subTitleTemplate.format(options.subTitle);
        }
        content += options.content;
        contentObj.html(content);
        iconObj.removeClass('active');
        if(options.iconShow){
            iconObj.addClass('active');
        }
        //事件绑定
        submitBtn.html(options.confirmTitle).unbind().bind($.getClickEventName(),function(){
            if($.isFunction(options.success)){
                options.success();
            }
            modal.removeClass('active');
        });
        cancelBtn.html(options.cancelTitle).unbind().bind($.getClickEventName(),function(){
            if($.isFunction(options.cancel)){
                options.cancel();
            }
            modal.removeClass('active');
        });
        if(options.cancelBtnHide){
            cancelBtn.hide();
            submitBtn.addClass('one-btn');
        }else{
            cancelBtn.show();
            submitBtn.removeClass('one-btn');
        }
        if(options.successBtnHide){
            submitBtn.hide();
        }else{
            submitBtn.show();
        }
        if(options.cancelBtnHide && options.successBtnHide){
            submitBtn.removeClass('one-btn');
        }

        //背景点击是否隐藏
        if(options.bgHide) {
            modal.find('.overlay').unbind().bind($.getClickEventName(),function () {
                modal.removeClass('active');
            });
        }else{
            modal.find('.overlay').unbind();
        }
        //自定义按钮
        if(options.buttons.length){
            var btnSection = modal.find('.dialog-ft').addClass('ft--full');
            btnSection.html('');
            options.buttons.forEach(function (item,key) {
                var html = "<span class='ft-btn'>"+item.btn_name+"</span>";
                var tmpNode = $(html);
                tmpNode.unbind().bind($.getClickEventName(),function () {
                    if($.isFunction(item.callback)){
                        item.callback();
                        modal.removeClass('active');
                    }
                });
                btnSection.append(tmpNode);
            });
        }
        //show
        modal.addClass('active');
    };

    $.wpost = function(url, data, callback,failback,withoutLoading, withoutHint, disableAsync){
        if(data == null)
            data = {};
        data.request_type = 'ajax';
        //网络判断
        if (!navigator.onLine) {
            $.showToast($.string.PLEASE_CHECK_NETWORK);
            return false;
        }

        if (!withoutLoading){
            if($.loadingToast('status')){
                return false;
            }
            $.loadingToast('请求中...');
        }
        $.log(">>>>>>>>>>>>>"+url+">>>>>>>>>>>>>>>");
        return $.ajax({type: 'post',url: url,data: data,
            async:disableAsync ? false : true,
            success: function(res){
                $.log('===========');
                $.log(res);
                if (!withoutLoading){
                    $.loadingToast('hide');
                }
                document.global_config_data.heart_at = new Date().getTime();
                // console.log('set time:'+document.global_config_data.heart_at);
                switch (res.code)
                {
                    case 0:
                        if(res.data.heart){
                            document.global_config_data.heart_at = new Date().getTime();
                        }

                        if($.isFunction(callback)){
                            //红包数据处理 start
                            if (res.data.extra) {
                                $.cache.set($.bonusKey , res.data.extra);
                            }
                            //红包数据处理 end

                            callback(res.data);
                        }
                        break;
                    case 401:
                        var $redirectUrl = res.data.url;
                        window.location.href = $redirectUrl;
                        break;
                    case -10001://token 过期
                        var url = '/auth/login/?callback='+ encodeURIComponent($.getLocationUrl());
                        window.location.replace(url);
                        break;
                    default :
                        if (parseInt(res.code) != 0){
                            if(!withoutHint){
                                $.showToast( res.msg,false);
                            }
                            if($.isFunction(failback)) {
                                failback(res);
                            }
                            return;
                        }
                        if($.isFunction(failback))
                            failback(res);
                        else
                        if(!withoutHint){
                            $.showToast( res.msg,false);
                        }
                        break;
                }

            },
            error:function(request, textStatus, err){
                // var desc = request.responseText ? request.responseText : '服务器错误';
                //     $.showToast(desc,false);
                if($.isFunction(failback)) {
                    failback();
                }
                if (!withoutLoading){
                    $.loadingToast('hide');
                }
            },
            dataType: 'json'});
    };

    // input verify
    $.hintFormatter = {
        empty:'{0}为必填项',
        tooShort:'{0}至少{1}个字符',
        tooLong:'{0}最多{1}个字符',
        bad:'{0}格式不合法'
    };
    $.defaultVerifyConfig = {
        name:{
            min:2,
            max:16,
            banSpecial:true,
            title:'真实姓名',
            hasSpecial:function(val){
                var regular = /^[\u4E00-\u9FA5a-zA-Z0-9\s]+$/;
                if (regular.test(val)){
                    var regularS = /^（）[！？。，《》{}【】“”·、：；‘’……]+$/;
                    return regularS.test(val);
                }
                return true;
            },
            test:function(val){
                return true;
            }
        },
        password:{
            min:6,
            max:60,
            title:'登录密码',
            test:function(val){
                return true;
            }
        },
        email:{
            min:-1,
            max:-1,
            title:'邮箱',
            test:function(val){
                var regEmail = /^[a-z0-9]+([._-]*[a-z0-9]+)*@([a-z0-9\-_]+([.\_\-][a-z0-9]+))+$/i;
                return regEmail.test(val);
            }
        },
        url:{
            min:-1,
            max:-1,
            title:'URL',
            test:function(val){
                var regUrl = /^(https?:\/\/)?(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
                return regUrl.test(val);
            }
        },
        id_code:{
            min:-1,
            max:-1,
            title:'身份证',
            test:function(val){
                var regUrl = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                return regUrl.test(val);
            }
        },
        qq:{
            min:5,
            max:15,
            title:'QQ',
            test:function(val){
                var regQQ = /^[1-9][0-9]{4,}$/i;
                return regQQ.test(val);
            }
        },
        mobile:{
            min:-1,
            max:-1,
            title:'手机号',
            test:function(val){
                // var regMobile = /^(\+|00)?[0-9\s\-]{3,20}$/;
                var regMobile = /^1[3|4|5|7|8]\d{9}$/;
                return regMobile.test(val);
            }
        },
        remark:{
            min:10,
            max:200,
            title:'评价内容',
            test:function(val){
                return true;
            }
        }
    };

    // state 1:ok 0:empty -1:short -2:bad
    $.checkInputVal = function(opts){
        var st = {
            val:null,
            onChecked:function(value,state,hint){
            },
            type:null,
            showHint:false,
            required:true
        };
        st = $.extend(st,opts);
        st.onChecked = $.isFunction(st.onChecked) ? st.onChecked : function(value,state,hint){};
//		st.type = st.type || $(this).attr('checkType');

        var getErrorHint = function(error,config){
            if (error == 0){
                return $.hintFormatter.empty.format(config.title);
            } else if (error == -1){
                return $.hintFormatter.tooShort.format(config.title,parseInt(config.min ));
            } else if (error == -2){
                return $.hintFormatter.tooLong.format(config.title,parseInt(config.max));
            } else if (error == -3){
                return $.hintFormatter.bad.format(config.title);
            }

            return '';
        };

        var val = st.val;
         val = $.trim(val);

        var config = $.defaultVerifyConfig[st.type];
        if (!val || val.length == 0){
            st.onChecked(val, 0, getErrorHint(0,config));
            return 0;
        }

        var length = val.length;
        if (config.min > 0 && length < config.min){
            st.onChecked(val, -1, getErrorHint(-1,config));
            return -1;
        }

        if (config.max > 0 && length > config.max){
            st.onChecked(val, -2, getErrorHint(-2,config));
            return -2;
        }

        if (config.banSpecial){
            if ($.isFunction(config.hasSpecial)){
                if (config.hasSpecial(val)){
                    st.onChecked(val, -4, '不能包含特殊字符');
                    return -4;
                }
            } else {
//				var regular= /['.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/i;
                var regular = /^[\u4E00-\u9FA5a-zA-Z0-9\s\.\,\(\)\+#\-]+$/;
                if (!regular.test(val)){
                    st.onChecked(val, -4, '不能包含特殊字符');
                    return -4;
                } else {
                    var regularS = /^[（）！？。，《》{}【】“”·、：；‘’……]+$/;
                    if (regularS.test(val)){
                        st.onChecked(val, -4, '不能包含特殊字符');
                        return -4;
                    }
                }
            }
        }

//		var regular = /^([^\`\+\~\!\#\$\%\^\&\*\(\)\|\}\{\=\"\'\！\￥\……\（\）\——]*[\+\~\!\#\$\%\^\&\*\(\)\|\}\{\=\"\'\`\！\?\:\<\>\尠“\”\；\‘\‘\〈\ 〉\￥\……\（\）\——\｛\｝\【\】\\\/\;\：\？\《\》\。\，\、\[\]\,]+.*)$/;
        if (!config.test(val)){
            st.onChecked(val, -3, getErrorHint(-3,config));
            return -3;
        }
        st.onChecked(val, 1, '');
        return 1;
    };

    /**
     * 微信分享config
     * @param opts
     */
    $.initWxShareConfigWithData = function (opts) {
        var st = {
            title:'{0}班车'.format(document.global_config_data.app_name),// 分享标题
            desc:'{0}班车'.format(document.global_config_data.app_name),// 分享描述
            link:window.location.href,// 分享链接
            imgUrl:window.location.origin+'/images/logo.png',// 分享图标,
            successFunc:null
        };
        if(opts){
            st = $.extend(st,opts);
        }
        //分享config
        var config = {
            title: st.title, // 分享标题
            desc: st.desc, // 分享描述
            link: st.link, // 分享链接
            imgUrl: st.imgUrl, // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function(res){
                console.log('wx share success');
                var msg = res ? res.errMsg : '';
                
                console.log(msg);
                switch (msg){
                    case 'sendAppMessage:ok'://朋友
                        if($.isFunction(st.successFunc)){
                            st.successFunc(0);
                        }
                        break;
                    case 'shareTimeline:ok'://朋友圈
                        if($.isFunction(st.successFunc)){
                            st.successFunc(1);
                        }
                        break;
                    default:
                        break;

                }

            },
            cancel: function(res){
                console.log('wx share cancel');
                console.log(res);
            }
        };
        if(typeof wx == 'undefined'){
            return false;
        }
        wx.ready(function(){
            // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，
            //所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，
            //则可以直接调用，不需要放在ready函数中。
            // 分享朋友
            wx.onMenuShareAppMessage(config);
            // 分享到朋友圈
            wx.onMenuShareTimeline(config);
            // 分享QQ
            wx.onMenuShareQQ(config);
            // 分享微博
            wx.onMenuShareWeibo(config);
        });

    };

    /**
     * 分页
     * @param opts
     * @returns {jQuery|HTMLElement|*}
     * @constructor
     */
    $.fn.Pager = function(opts) {
        var st = {
            protocol: null,
            cursorId : 0,
            past: 0,
            loading: false,
            withoutLoading: true,
            cacheKeySuffix:'',
            listHtmlSectionNode:'#list',
            loadMoreNode:'.loading-more',
            wrapUpdateData:null,
            successFunc:null
        };

        var $this = $(this);
        $.extend(st, opts);
        var cacheKey = st.protocol+st.cacheKeySuffix;
        $this.getCursorId = function(){
            return st.cursorId;
        };
        $this.setCursorId = function(cursorId){
            st.cursorId = cursorId;
        };
        $this.updateList = function(){
            var loadMoreObj = $this.find(st.loadMoreNode);
            var listHtmlObj = $this.find(st.listHtmlSectionNode);

            //==== cache data start ====
            var srcCacheData = $.cache.get(cacheKey);
            if(srcCacheData && !$.trim(listHtmlObj.html())){
                var cursor_ids=srcCacheData.cursor_ids;
                st.cursorId = cursor_ids[cursor_ids.length-1];
                st.past = 1;
                listHtmlObj.html(srcCacheData.html);
                if(st.cursorId>0 && srcCacheData.hasMore){
                    loadMoreObj.removeClass('gone');
                }else{
                    loadMoreObj.addClass('gone');
                }
                if($.isFunction(st.successFunc)){
                    st.successFunc();
                }
                return false;
            }
            //==== cache data over ====

            if(st.loading){
                return false;
            }
            var data = {
                cursor_id:st.cursorId,
                timestamp:0,
                past:st.past
            };
            if($.isFunction(st.wrapUpdateData)){
                data = st.wrapUpdateData(data);
            }
            st.loading = true;
            $this.find(st.loadMoreNode).addClass('loading');
            $.wpost(st.protocol, data ,function(res){
                if(res.cursor_id>0){
                    if(st.cursorId  != res.cursor_id && res.length==10){
                        loadMoreObj.removeClass('gone');
                    }else{
                        loadMoreObj.addClass('gone');
                    }
                    st.past =1;
                }else{
                    loadMoreObj.addClass('gone');
                    listHtmlObj.html('');
                    st.past =0;
                }

                //==== cache data start ====
                var srcCacheData = $.cache.get(cacheKey);
                var cacheCursorIds =  [];
                var cacheHtml =  '';
                if(srcCacheData){
                    cacheHtml = srcCacheData.html + res.html;
                    cacheCursorIds = srcCacheData.cursor_ids;
                }else{
                    if (listHtmlObj.html().indexOf('empty-list')===-1) {
                        cacheHtml = res.html;
                    }
                }
                if(cacheCursorIds.indexOf(res.cursor_id)===-1){
                    cacheCursorIds.push(res.cursor_id);
                    var cacheData = {
                        cursor_ids : cacheCursorIds,
                        hasMore : res.length==10 ? true : false,
                        html : cacheHtml
                    };
                    $.cache.set(cacheKey,cacheData);
                }
                //==== cache data over ====

                st.cursorId = res.cursor_id;
                listHtmlObj.append(res.html);
                st.loading = false;
                loadMoreObj.removeClass('loading');
                if($.isFunction(st.successFunc)){
                    st.successFunc();
                }
            },function () {
                st.loading = false;
                $this.find(st.loadMoreNode).removeClass('loading');
            },st.withoutLoading);
        };
        $this.find(st.loadMoreNode).unbind().bind('click',function (e) {
            e.stopPropagation();
            $this.updateList();
        });
        return $this;
    };

    /**
     * 上下拉刷新
     * @param opts
     * @returns {jQuery|HTMLElement|*}
     * @constructor
     */
    $.fn.DropLoadPager = function(opts) {
        var st = {
            protocol: null,
            firstCursorId : 0,
            cursorId : 0,
            past: 0,
            first_get_status: true,
            cacheKeySuffix:'',
            loading: false,
            scrollTarget: window,
            withoutLoading: true,
            listHtmlSectionNode:'#list',
            dropShowNode:'#js_drop_load_area',
            wrapUpdateData:null
        };

        var $this = $(this);
        $.extend(st, opts);
        var cacheKey = st.protocol+st.cacheKeySuffix;
        $this.getCursorId = function(){
            return st.cursorId;
        };
        $this.setCursorId = function(cursorId){
            st.cursorId = cursorId;
        };
        $this.resetLoad = function (me, resCursorId) {
            if($('.fadeInUp').length>=10) {
                setTimeout(function () {
                    me.resetload();
                    if(resCursorId ==0) {
                        $('.dropload-noData').html('');
                    }
                }, 1000);
            } else {
                me.resetload();
                if(resCursorId ==0) {
                    $('.dropload-noData').html('');
                }
            }
        };
        $this.updateList = function(me,direction){
            
            console.log(direction);
            var isUpDirection = (direction == 'up');
            var listHtmlObj = $this.find(st.listHtmlSectionNode);

            //==== cache data start ====
            var srcCacheData = $.cache.get(cacheKey);
            if(srcCacheData && !$.trim(listHtmlObj.html()).length){
                st.first_get_status = false;
                var cursor_ids=srcCacheData.cursor_ids;
                st.cursorId = cursor_ids[cursor_ids.length-1];
                st.firstCursorId = srcCacheData.firstCursorId;
                listHtmlObj.html(srcCacheData.html);
                //TODO 修复动画bug
                $this.resetLoad(me, st.firstCursorId);
                return false;
            }
            //==== cache data over ====

            if(st.loading){
                return false;
            }
            var data = {
                cursor_id: isUpDirection? st.firstCursorId : st.cursorId,
                timestamp:isUpDirection && st.firstCursorId  ? st.firstCursorId :0,
                past:isUpDirection ? 0 : 1
            };
            if($.isFunction(st.wrapUpdateData)){
                data = st.wrapUpdateData(data);
            }
            if (isUpDirection && !st.first_get_status) {
                setTimeout(function () {
                    me.resetload();
                },600);
                return false;
            }
            st.first_get_status = false;
            st.loading = true;
            $this.find(st.loadMoreNode).addClass('loading');
            $.wpost(st.protocol, data ,function(res){
                if(st.cursorId==0){
                    st.firstCursorId = res.first_cursor_id;
                }
                //无数据
                if(st.cursorId  == res.cursor_id){
                    // 锁定
                    me.lock();
                    me.noData();
                }

                //==== cache data start ====
                var srcCacheData = $.cache.get(cacheKey);
                var cacheCursorIds =  [];
                var cacheHtml =  '';
                if(srcCacheData){
                    cacheHtml = isUpDirection ? res.html+ srcCacheData.html  :srcCacheData.html + res.html;
                    cacheCursorIds = srcCacheData.cursor_ids;
                }else{
                    if (listHtmlObj.html().indexOf('empty-list')===-1) {
                        cacheHtml = res.html;
                    }
                }
                if(cacheCursorIds.indexOf(res.cursor_id)===-1 && res.cursor_id!=0){
                    if(isUpDirection){
                        cacheCursorIds.unshift(res.cursor_id);
                    }else{
                        cacheCursorIds.push(res.cursor_id);
                    }
                    var cacheData = {
                        cursor_ids : cacheCursorIds,
                        firstCursorId : st.firstCursorId,
                        html : cacheHtml
                    };
                    $.cache.set(cacheKey,cacheData);
                }
                //==== cache data over ====

                st.cursorId = res.cursor_id;
                if(isUpDirection){
                    if (listHtmlObj.html().indexOf('empty-list')===-1) {
                        listHtmlObj.prepend(res.html);
                    }
                }else{
                    listHtmlObj.append(res.html);
                }
                st.loading = false;
                // 每次数据加载完，必须重置
                //TODO 修复动画bug
                $this.resetLoad(me, res.cursor_id);

            },function () {
                // st.loading = false;
                // 即使加载出错，也得重置
                me.resetload();
            },st.withoutLoading);
        };

        $this.find(st.dropShowNode).dropload({
            scrollArea : st.scrollTarget,
            domUp : {
                domClass   : 'dropload-up',
                domRefresh : '<div class="dropload-refresh">↓下拉刷新</div>',
                domUpdate  : '<div class="dropload-update">↑释放更新</div>',
                domLoad    : '<div class="dropload-load"><span class="loading"></span>加载中...</div>'
            },
            domDown : {
                domClass   : 'dropload-down',
                domRefresh : '<div class="dropload-refresh">↑上拉加载更多</div>',
                domLoad    : '<div class="dropload-load"><span class="loading"></span>加载中...</div>',
                domNoData  : '<div class="dropload-noData">没有更多了</div>'
            },
            loadUpFn : function(me){
                $this.updateList(me,'up');
            },
            loadDownFn : function(me){
                var status = 'down';
                if(st.cursorId == 0){
                    status = 'up';
                }
                $this.updateList(me,status);
            },
            threshold : 50
        });
        return $this;
    };


    /**
     *优惠卷
     */
    $.fn.initCouponModal = function (opts) {
        var st = {
            res: [],
            result: [],
            loading: false,
            first: true,
            contractId: '',
            withoutLoading: true,
            listSelectItemNode:'.ar-coupon-modal #list li input',
            pagerSectionNode: '.ar-coupon-modal',
            pager: '',
            confirmBtnNode:'#js_coupon_confirm_btn',
            unSelectedCouponNode:'#js_un_use_coupon',
            change :''
        };

        var $this = $(this);
        $.extend(st, opts);

        /**
         * 获取已选中的优惠券
         */
        $this.getSelectedVal = function () {
            var select = $(st.listSelectItemNode+':checked');
            return select.data('info');
        };

        /**
         * 分页
         * @type {jQuery}
         */
        st.pager = $(st.pagerSectionNode).Pager({
            protocol:$.httpProtocol.GET_COUPON_LIST,
            cacheKeySuffix: st.contractId,
            wrapUpdateData:function(data){
                var param={
                    show_checked:1,
                    contract_id:st.contractId
                };
                if (param){
                    $.extend(data, param);
                }
                return data;
            },
            successFunc:function () {
                if (st.coupon && st.coupon.is_available) {
                    $('#'+st.coupon.coupon_id).prop('checked',true);
                }
                if($.isFunction(st.change)){
                    st.change(st.coupon);
                }
            }
        });
        st.pager.updateList();
        /**
         * 确认操作
         */
        $(st.confirmBtnNode).unbind().bind($.getClickEventName(),function () {
            var val = $this.getSelectedVal();
            if($.isFunction(st.change)){
                st.change(val);
            }
            $(st.pagerSectionNode).removeClass('active');
        });
        /**
         * 显示操作
         */
        $this.unbind().bind($.getClickEventName(),function () {
            if ($(this).hasClass('disabled')) {
                return false;
            }
            if (st.coupon && st.coupon.is_available && st.first) {
                $('#'+st.coupon.coupon_id).prop('checked',true);
                st.first = false;
            }
            $(st.pagerSectionNode).removeClass('gone');
            setTimeout(function () {
                $(st.pagerSectionNode).addClass('active');
            }, 300);
        });
        /**
         * 不使用操作
         */
        $(st.unSelectedCouponNode).unbind().bind('change',function () {
            var checked = $(this).prop('checked');
            if(checked){
                $(st.listSelectItemNode).prop('checked', false);
            }
        });
        /**
         * 优惠券选择事件
         */
        $(document).on('click',st.listSelectItemNode,function () {
            $(st.unSelectedCouponNode).prop('checked', false);
        });
      
        return $this;
    };

    /**
     * 日历选择车票日票
     */
    $.fn.initCalendarSelect = function (opts) {
        var st = {
            data: [],
            result: [],
            cacheResultDate: [],
            type:false,
            availableTime: '',
            options:'',
            displayTime:'',
            template:"<td class='{0}' data-info='{1}'>{2}</td>",
            changeFunc:''
        };

        var $this = $(this);
        $.extend(st, opts);
        
        /**
         * 计算最大可用日期
         */
        $this.availableTime = function () {
            st.availableTime = 0;
            if(st.options && st.options.length){
                var maxLen=st.options.length;
                for(var i=0;i<maxLen;i++){
                    var tmpDate = st.options[i].line_schedule_date;
                    if(tmpDate>st.availableTime){
                        st.availableTime= tmpDate;
                    }
                }
                st.availableTime =st.availableTime*1000;
            } else {
                st.availableTime = (new Date()).getTime();
            }
        };
        /**
         * 初始化数据
         */
        $this.initData = function () {
            var result=[];
            var currentTime=new Date();
            var maxTime;
            if(st.displayTime){
                currentTime=new Date(st.displayTime*1000);
            }
            maxTime=new Date(st.availableTime);

            //最大限制时间
            var maxTimeMonth=maxTime.getMonth();
            var maxTimeDay=maxTime.getDate();
            var maxTimeDayWeek=maxTime.getDay();

            //当月
            var currentDay=currentTime.getDate();
            var currentYear=currentTime.getFullYear();
            var currentMonth=currentTime.getMonth();
            var currentDayWeek=currentTime.getDay();
            var endDay=(new Date(currentTime.getFullYear(),currentTime.getMonth()+1,0)).getDate();
            var endDayWeek=(new Date(currentTime.getFullYear(),currentTime.getMonth()+1,0)).getDay();
            var startDayWeek=(new Date(currentTime.getFullYear(),currentTime.getMonth(),1)).getDay();

            //上一月
            var prevMonthMaxTime=new Date(currentTime.getFullYear(),currentTime.getMonth(),0);//上一月最大天数
            var prevYear=prevMonthMaxTime.getFullYear();
            var prevMonth=prevMonthMaxTime.getMonth();
            var prevEndDay=prevMonthMaxTime.getDate();

            //下一月
            var nextMonthMaxTime=new Date(currentTime.getFullYear(),currentTime.getMonth()+1,1);//下一月最大天数
            var nextYear=nextMonthMaxTime.getFullYear();
            var nextMonth=nextMonthMaxTime.getMonth();
            var nextStartDay=nextMonthMaxTime.getDate();

            //生成每天的数据结构
            var toBuildJsonData=function(year,month,day){
                var tmpDateObj=new Date(year,month,day);
                var tmpYear=tmpDateObj.getFullYear();
                var tmpMonth=tmpDateObj.getMonth();
                var tmpDay=tmpDateObj.getDate();

                var tmpDate={
                    key:tmpDay,
                    year:tmpYear,
                    month:tmpMonth,
                    day:tmpDay,
                    title:tmpDay,
                    time:tmpDateObj.getTime(),
                    style:'disabled',
                    full:false,
                    checked:false,
                    reversed:false,
                    disabled:true,
                    isNow:false,
                    isCurrentMonth:false,
                    isNextMonth:false
                };
                if(currentYear==tmpYear && currentMonth==tmpMonth && currentDay==tmpDay && (new Date()).getMonth()==currentMonth){
                    tmpDate.isNow=true;
                    tmpDate.title='今';
                }
                if(currentYear==tmpYear && currentMonth==tmpMonth){
                    tmpDate.isCurrentMonth=true;
                }
                if((currentYear+1==tmpYear || currentYear==tmpYear) && ((currentMonth+1)>11?0:(currentMonth+1))==tmpMonth){
                    tmpDate.isNextMonth=true;
                }
                return tmpDate;
            };

            if(st.type){
                for(var i=1;i<=endDay;i++){
                    result.push(toBuildJsonData(currentYear,currentMonth,i));
                }
                if(startDayWeek<6){
                    for(var i=0;i<startDayWeek;i++){
                        result.unshift(toBuildJsonData(prevYear,prevMonth,prevEndDay-i));
                    }
                }
                if(endDayWeek<6){
                    for(var i=0;i<6-endDayWeek;i++){
                        result.push(toBuildJsonData(nextYear,nextMonth,nextStartDay+i));
                    }
                }
            }else{
                //跨月份情况计算
                if(currentMonth==maxTimeMonth){
                    //当天到最大的时间
                    for(var i=currentDay;i<=maxTimeDay;i++){
                        result.push(toBuildJsonData(currentYear,currentMonth,i));
                    }
                    //末尾填充
                    if(maxTimeDayWeek<6){
                        //补充最大限制日期
                        for(var i=1;i<=6-maxTimeDayWeek;i++){
                            if(maxTimeDay+i<=endDay){
                                result.push(toBuildJsonData(currentYear,currentMonth,maxTimeDay+i));
                            }
                        }
                        //如果不够填充 补充下一月的数据
                        if(6-maxTimeDayWeek>endDay-maxTimeDay){
                            for(var i=0;i<6-endDayWeek;i++){
                                result.push(toBuildJsonData(nextYear,nextMonth,nextStartDay+i));
                            }
                        }
                    }

                }else{
                    for(var i=currentDay;i<=endDay;i++){
                        result.push(toBuildJsonData(currentYear,currentMonth,i));
                    }
                    //末尾填充
                    var nextMonthIndex=0;//出去这月填充的数据 下月开始填充的起始点
                    if(endDayWeek>0){
                        for(var i=0;i<6-endDayWeek;i++){
                            nextMonthIndex=nextStartDay+i;
                            result.push(toBuildJsonData(nextYear,nextMonth,nextStartDay+i));
                        }
                    }
                    //如果下月的最大日期 不在 上一月末尾填充的下月日期，则继续填充
                    if(maxTimeDay>nextMonthIndex){
                        for(var i=1;i<=maxTimeDay-nextMonthIndex;i++){
                            result.push(toBuildJsonData(nextYear,nextMonth,nextMonthIndex+i));
                        }
                        if(maxTimeDayWeek<6){
                            for(var i=1;i<=6-maxTimeDayWeek;i++){
                                result.push(toBuildJsonData(nextYear,nextMonth,maxTimeDay+i));
                            }
                        }
                    }

                }
                //开始部分 填充
                if(currentDayWeek!=0){
                    for(var i=1;i<=currentDayWeek;i++){
                        if(currentDay-i>0){
                            result.unshift(toBuildJsonData(currentYear,currentMonth,currentDay-i));
                        }
                    }
                    //月初不够填充的话 填充上一月
                    if(currentDayWeek>=currentDay){
                        for(var i=0;i<currentDayWeek-currentDay+1;i++){
                            result.unshift(toBuildJsonData(prevYear,prevMonth,prevEndDay-i));
                        }
                    }
                }

            }
            //添加购票信息数据
            if(result.length){
                for(var i=0;i<result.length;i++){
                    if(st.options && st.options.length){
                        for(var j=0;j<st.options.length;j++){
                            var tmpScheduleTime=st.options[j].line_schedule_date*1000;
                            if(tmpScheduleTime >=result[i].time && tmpScheduleTime<(result[i].time+60*60*24*1000)){
                                var scheduleStatus=parseInt(st.options[j].line_schedule_status);
                                result[i].id=st.options[j].line_schedule_id;
                                result[i].status=scheduleStatus;
                                switch (scheduleStatus){
                                    case 0://可预约
                                        result[i].disabled=false;
                                        result[i].style='normal';
                                        break;
                                    case 1://已预约
                                        result[i].reversed=true;
                                        result[i].style='reversed';
                                        break;
                                    case 2://满员
                                        result[i].full=true;
                                        result[i].style='full';
                                        break;
                                    case 3://即将开放
                                        break;
                                }
                                break;
                            }
                        }
                    }
                }
            }
            //转换数据结构格式 按组分
            if(result.length){
                var tmpResult=[];
                for(var i=0;i<result.length;i++){
                    if(i>0 && i%7==0){
                        st.data.push(tmpResult);
                        tmpResult=[];
                    }
                    tmpResult.push(result[i]);
                    if(i>=result.length-1){
                        st.data.push(tmpResult);
                        tmpResult=[];
                    }
                }
            }

        };

        /**
         * 渲染日期html
         */
        $this.render = function () {
            var html = '';
            st.data.forEach(function (subData, key) {
                var subHtml = "";
                subData.forEach(function (item, index) {
                    var classType = item.isNow ? item.style+' now' : item.style;
                    if (st.cacheResultDate.indexOf(item.time) !== -1 && classType.indexOf('disabled') === -1 && classType.indexOf('reversed') === -1 &&  classType.indexOf('full') === -1) {
                        classType += " checked";
                    }
                    subHtml+= st.template.format(classType, JSON.stringify(item),item.title);
                });
                html+='<tr>'+subHtml+'</tr>';
            });
            $this.html(html);
            
            //处理默认选中
            var checked = $('td.checked', $this);
            if (checked.length === 0) {
                setTimeout(function () {
                    $('td.normal').eq(0).trigger('click');
                }, 0);
            }
        };

        /**
         * 选择时间事件
         */
        $this.on('click', 'td', function () {
            var target = $(this);
            var item = target.data('info');
            if(!item || item.disabled || item.full || item.reversed){
                return false;
            }
            target.toggleClass('checked');
            if ($.isFunction(st.changeFunc)) {
                var res = $this.getVal();
                st.changeFunc(res);
            }
        });

        /**
         * 获取结果集 （最终的结构）
         * @param status   true 返回原始数据 false 返回 提交数据结构
         * @returns {Array}
         */
        $this.getVal = function(status){
            var result = $('td.checked', $this);
            st.result = [];
            st.cacheResultDate = [];
            result.each(function (key, item) {
                var data = $(item).data('info');
                if (status) {
                    st.result.push(data);
                } else {
                    st.result.push({line_schedule_id:data.id});
                }
                st.cacheResultDate.push(data.time);
            });
            console.log(st.cacheResultDate);
            return st.result;
        };

        /**
         * 初始化
         */
        $this.draw = function () {
            $this.availableTime();
            $this.initData();
            $this.render();
        };
        /**
         * 设置选项
         * @param data
         */
        $this.setOptions = function (data) {
            st.options = data;
            st.data = [];
        };
        $this.draw();
        return $this;
    }

})($);