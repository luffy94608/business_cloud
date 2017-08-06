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
     * 自定义class 阻止冒泡事件
     */
    $(document).on('click', '.js_disabled_btn', function (e) {
        e.stopPropagation();
    });

    /**
     * 搜索事件
     */
    $('.bcb-search').keypress(function(e) {
        if (e.which == 13) {
            var keyword = $.trim($(this).val());
            if (keyword.length<1) {
                $.showToast('请输入关键字');
                return false;
            }
            var path = location.pathname;
            var src = 'publish';
            switch (path) {
                case '/':
                    src = 'publish';
                    break;
                case '/bid-call':
                    src = 'publish';
                    break;
                case '/bid-winner':
                    src = 'bid';
                    break;
                case '/rival':
                    src = 'competitor';
                    break;
                case '/rival-detail':
                    src = 'competitor';
                    break;
            }
            $.locationUrl('/search-list?src={0}&keyword={1}'.format(src, keyword));
        }
    });



});
