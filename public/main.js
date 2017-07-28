(function () {
    //全局可以host
    var configData = document.global_config_data;
    var version = configData.version;
    requirejs.config({
        baseUrl: configData.resource_root + '/scripts/',
        urlArgs: 'v=' + version,
        waitSeconds: 0,
        paths: {
            //core js
            'jquery': '/bower_components/jquery/dist/jquery.min',
            'zepto': '/bower_components/zepto/zepto.min',
            'cookie': '/bower_components/jquery-cookie/jquery.cookie',
            'mobiscroll': '/bower_components/datepicker/js/mobiscroll.custom-3.0.0-beta5.min',
            'fastclick': 'libs/fastclick',
            'dropload': '/bower_components/dropload/dist/dropload.min',
            'art-template': '/bower_components/art-template/dist/template',
            'swiper': '/bower_components/swiper/dist/js/swiper.jquery.min',
            'bootstrap': '/sass/bootstrap-sass-3.3.7/assets/javascripts/bootstrap.min',
            'widget': 'widget/widget',
            'ticket': 'widget/ticket',
            'new-ticket': 'widget/new_ticket',
            'seat': 'widget/seat',
            'bonus': 'widget/red_packet',
            'string': 'widget/string',
            'http': 'widget/http',
            'cache': 'widget/cache',
            'canvas': 'widget/canvas',

            'base': 'page/base',
            'page-index':'page/page.index',
            // 'page-edit':'page/page.edit',
            // 'page-login':'page/page.login',
            // 'page-psw':'page/page.psw',
            // 'page-register':'page/page.register',
            // 'page-account':'page/page.account',
            // 'page-coupon':'page/page.coupon',
            // 'page-cash':'page/page.cash',
            // 'page-lines':'page/page.lines',
            // 'page-pay':'page/page.pay',
            // 'page-my-order':'page/page.my_order',
            // 'page-order-detail':'page/page.order_detail',
            // 'page-bus-location':'page/page.bus_location',
            // 'page-bus-map':'page/page.bus_map',
            // 'page-shuttle-list':'page/page.shuttle_list',
            // 'page-shuttle-map':'page/page.shuttle_map',
            // 'page-pay-shuttle':'page/page.pay_shuttle',
            // 'page-ticket-detail':'page/page.ticket_detail',
            // 'page-remark':'page/page.remark',
            // 'page-download':'page/page.download',
            // 'page-activity':'page/page.activity',
            // 'page-feedback':'page/page.feedback',
            // 'page-bonus':'page/page.bonus',
            // 'page-lottery':'page/page.lottery'

        },
        // Use shim for plugins that does not support ADM
        shim: {
            'string': ['jquery'],
            'http': ['jquery'],
            'cache': ['jquery'],
            // 'mobiscroll': ['jquery'],
            'cookie': ['jquery'],
            'lib': ['jquery'],
            'bootstrap': ['jquery'],
            'widget': ['jquery','string','http','cache','fastclick', 'bootstrap'],
            'base': ['widget'],
            'canvas': ['jquery'],

            'page-index': ['base','cookie'],
            // 'page-edit': ['base','cookie'],
            // 'page-login': ['base', 'cookie'],
            // 'page-psw': ['base'],
            // 'page-register': ['base','cookie'],
            // 'page-account': ['base'],
            // 'page-coupon': ['base'],
            // 'page-cash': ['base'],
            // 'page-lines': ['base'],
            // 'page-pay': ['base', 'mobiscroll','cookie', 'seat'],
            // 'page-my-order': ['swiper' ,'mobiscroll','ticket'],
            // 'page-order-detail': ['base'],
            // 'page-bus-location': ['base'],
            // 'page-bus-map': ['base'],
            // 'page-shuttle-list': ['base'],
            // 'page-shuttle-map': ['base','swiper', 'new-ticket'],
            // 'page-pay-shuttle': ['base'],
            // 'page-ticket-detail': ['base'],
            // 'page-remark': ['base'],
            // 'page-download': ['base'],
            // 'page-activity': ['base'],
            // 'page-feedback': ['base', 'mobiscroll'],
            // 'page-bonus': ['base'],
            // 'page-lottery': ['base'],

        }

    });
    var page = configData.page;

    var modules = [];
    if (page) {
        modules.push(page);
    }

    if (modules.length) {
        require(modules, function () {
        });
    }

})();
