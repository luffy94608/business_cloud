(function () {
    //全局可以host
    var configData = document.global_config_data;
    var version = configData.version;
    requirejs.config({
        baseUrl: configData.resource_root + '/release/',
        urlArgs: 'v=' + version,
        waitSeconds: 0,
        paths: {
            //core js
            'jquery': '/bower_components/jquery/dist/jquery.min',
            'zepto': '/bower_components/zepto/zepto.min',
            'cookie': '/bower_components/jquery-cookie/jquery.cookie',
            'mobiscroll': '/bower_components/datepicker/js/mobiscroll.custom-3.0.0-beta5.min',
            'fastclick': 'libs/fastclick',
            'toastr': 'libs/toastr.min',
            'dropload': '/bower_components/dropload/dist/dropload.min',
            'art-template': '/bower_components/art-template/dist/template',
            'swiper': '/bower_components/swiper/dist/js/swiper.jquery.min',
            'select-zh-cn': '/bower_components/select2/dist/js/i18n/zh-CN',
            'select2': '/bower_components/select2/dist/js/select2.min',
            'highcharts': '/bower_components/highcharts/highcharts',
            'bootstrap': '/sass/bootstrap-sass-3.3.7/assets/javascripts/bootstrap.min',
            'widget': 'widget/widget',
            // 'ticket': 'widget/ticket',
            // 'new-ticket': 'widget/new_ticket',
            // 'seat': 'widget/seat',
            // 'bonus': 'widget/red_packet',
            'string': 'widget/string',
            'http': 'widget/http',
            'cache': 'widget/cache',

            'base': 'page/base',
            'page-index':'page/page.index',
            'page-search-list':'page/page.search_list',
            'page-reset':'page/page.reset',
            'page-login':'page/page.login',
            'page-register':'page/page.register',                
            'page-profile':'page/page.profile',
            'page-company':'page/page.company',
            'page-business':'page/page.business',
            'page-bid-call':'page/page.bid_call',
            'page-bid-winner':'page/page.bid_winner',
            'page-rival':'page/page.rival',
            'page-rival-detail':'page/page.rival_detail',
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
            'cookie': ['jquery'],
            'lib': ['jquery'],
            'bootstrap': ['jquery'],
            'toastr': ['jquery'],
            'select-zh-cn': ['select2'],
            'select2': ['jquery'],
            'highcharts': ['jquery'],
            'widget': ['jquery','string','http','cache','fastclick', 'bootstrap', 'toastr', 'select-zh-cn'],
            'base': ['widget'],

            'page-index': ['base','cookie'],
            'page-search-list': ['base','cookie'],
            'page-reset': ['base','cookie'],
            'page-login': ['base', 'cookie'],
            'page-register': ['base','cookie'],
            'page-profile': ['base','cookie'],
            'page-company': ['base'],
            'page-business': ['base'],
            'page-bid-call': ['base'],
            'page-bid-winner': ['base'],
            'page-rival': ['base', 'highcharts'],
            'page-rival-detail': ['base', 'highcharts'],
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
