(function($){
    /**
     * 注册fastclick
     */
    var FastClick = require('fastclick');
    FastClick.attach(document.body);
    $.fn.select2.defaults.set("theme", "classic");


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
     * canvas 圆形进度条
     * @param canvas
     * @param process
     * @param color
     */
    $.drawCircleProcess = function(canvas, process, color) {

        if (!color) {
            color = '#FF7C7C';
        }
        // 拿到绘图上下文
        var ctx = canvas.getContext('2d');
        var cWidth = canvas.width;
        var cHeight = canvas.height;
        var circleX = cWidth/2;
        var circleY = cHeight/2;
        var circleR = circleX-2;
        // 将绘图区域清空
        ctx.clearRect(0, 0, cWidth, cHeight);
        //灰色背景
        ctx.beginPath();

        ctx.moveTo(circleX, circleY);

        ctx.arc(circleX, circleY, circleR, 0, Math.PI * 2, false);
        ctx.closePath();
        ctx.fillStyle = '#eee';
        ctx.fill();
        // 画进度
        ctx.beginPath();

        ctx.moveTo(circleX, circleY);

        ctx.arc(circleX, circleY, circleR, -Math.PI/2, Math.PI * (2 * (process-.005) / 100-.5), false);
        ctx.closePath();
        ctx.fillStyle = color;
        ctx.fill();

        // 画内部空白
        ctx.beginPath();
        ctx.moveTo(circleX, circleY);
        ctx.arc(circleX, circleY, circleR-2, 0, Math.PI * 2, true);
        ctx.closePath();
        ctx.fillStyle = '#fff';
        ctx.fill();

        if(process !== 100){
            //终端原点
            // ctx.beginPath();
            // //移动到终端位置
            // var x1 = circleX + (circleR-1) * Math.sin(Math.PI * 2 * process / 100);
            // var y1 = circleY - (circleR-1) * Math.cos(Math.PI * 2 * process / 100);
            // ctx.moveTo(x1, y1);
            // ctx.arc(x1, y1, 3, 0, Math.PI * 2, true);
            // ctx.closePath();
            // ctx.fillStyle = color;
            // ctx.fill();
        }
    };

})($);