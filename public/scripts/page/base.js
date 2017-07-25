/**
 * Created by luffy on 16/1/28.
 *  当页面ready的时候，执行回调:
 */
$(document).ready(function () {

    // .container 设置了 overflow 属性, 导致 Android 手机下输入框获取焦点时, 输入法挡住输入框的 bug
    // 相关 issue: https://github.com/weui/weui/issues/15
    // 解决方法:
    // 0. .container 去掉 overflow 属性, 但此 demo 下会引发别的问题
    // 1. 参考 http://stackoverflow.com/questions/23757345/android-does-not-correctly-scroll-on-input-focus-if-not-body-element
    //    Android 手机下, input 或 textarea 元素聚焦时, 主动滚一把
    if (/Android/gi.test(navigator.userAgent)) {
        window.addEventListener('resize', function () {
            if (document.activeElement.tagName == 'INPUT' || document.activeElement.tagName == 'TEXTAREA') {
                window.setTimeout(function () {
                    document.activeElement.scrollIntoViewIfNeeded();
                }, 0);
            }
        });
    }
    /**
     * 微信分享
     */
    $.initWxShareConfigWithData();

    /**
     * 全局调转事件
     */
    $.ALocationUrlEvent();

    /**
     * 错误捕获
     * @param msg
     * @param file
     * @param line
     * @param col
     * @param error
     */
    window.onerror = function(msg, file, line, col, error) {
        // callback is called with an Array[StackFrame]
        var arr = [];
        var data = {
            file:file,
            msg:msg,
            position:'line:{0}  col:{1}'.format(line,col),
            error:error,
            message:error?error.message:'',
            track:error?error.stack:'',
        };
        for (var key in data){
            arr.push(key+':'+data[key]);
        }

        var params = {
            msg:"\n\n"+arr.join("\n")+"\n\n"
        };
        $.wpost($.httpProtocol.ERROR_TRACK,params,function () {

        },function () {

        },true);
        // 必须返回 true，否则 Error 还是会触发阻塞程序 return true;著作权归作者所有。
        // return true;
    };

    /**
     * 跳转指定项目
     */
    setTimeout(function () {
        var scrollTop = $.getRecordPageScrollTopHeight();
        var tHeight=$(document).height();
        if(scrollTop<tHeight){
            $(window).scrollTop(scrollTop);
        }
    }, 500);

    /**
     * safari 返回不刷新bug
     */
    if ($.browser.iPhone) {
        if(!!window.performance && window.performance.navigation.type === 2)
        {
            window.location.reload();
        } else {
            window.onpageshow = function(event) {
                if (event.persisted) {
                    window.location.reload()
                }
            };
        }
    }


    /**
     * 自定义class 阻止冒泡事件
     */
    $(document).on('click', '.js_disabled_btn', function (e) {
        e.stopPropagation();
    });

    /**
     * 红包检测
     */
    $.checkRedPacketBonusEvent();

    /**
     * 离线验票有网络处理
     */
    var checkOffLineTicketEvent = function () {
        var cacheData = $.localCache.get($.offLineCheckedTicketKey);
        if (navigator.onLine && cacheData) {//有网络
            $.wpost($.httpProtocol.CHECK_OFF_LINE_TICKET,cacheData,function (data) {
                $.localCache.remove($.offLineCheckedTicketKey);
                setTimeout(function () {
                    checkOffLineTicketEvent();
                },2000);
            },function () {
            },true);
        } else {
            setTimeout(function () {
                checkOffLineTicketEvent();
            },2000);
        }
    };
    checkOffLineTicketEvent();


    

});
