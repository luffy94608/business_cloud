/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        submitBtn :$('.hl-download'),
        loading :false,
        initBtnEvent : function () {
            /**
             * 下载按钮
             */
            init.submitBtn.unbind().bind('click', function () {
                var url='';
                if ($.browser.inWeChat) {
                    $.showShareRemindModal('请点击这里<br/>通过浏览器打开进行下载');
                    return false;
                }

                if($.browser.iPhone){
                    url='https://appsto.re/cn/ueEkW.i';
                }else{
                    url='http://a.app.qq.com/o/simple.jsp?pkgname=cn.com.haoluo.www';
                }
                window.location.href=url;
            });

            var shareOpt = {
                title:'哈罗同行',// 分享标题
                desc:'社区互助，一路同行',// 分享描述
                link: $.getLocationUrl(),// 分享链接
                imgUrl:window.location.origin+'/images/logo.png',// 分享图标
            };
            $.initWxShareConfigWithData(shareOpt);

        },
        run : function () {
            init.initBtnEvent();
        }
    };
    init.run();
})($);