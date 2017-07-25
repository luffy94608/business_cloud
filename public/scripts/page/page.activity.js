/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        submitBtn :$('.hl-download'),
        loading :false,
        initBtnEvent : function () {
            var shareOpt = {
                desc:"哈罗同行，新用户注册有礼，微信独享",
                title:'哈罗同行',
                circle:"哈罗同行，新用户注册有礼，微信独享",
                link: $.getLocationUrl(),// 分享链接
                imgUrl:window.location.origin+'/images/logo.png',// 分享图标
            };
            $.initWxShareConfigWithData(shareOpt);

            setTimeout(function(){
                var obj=$('.hl-acb-tree');
                var windowHeight=$(window).height();
                var bodyHeight=$('#page').height();
                if((windowHeight-bodyHeight)>50){
                    obj.css({position:'fixed'});
                }
            },300)

        },
        run : function () {
            init.initBtnEvent();
        }
    };
    init.run();
})($);