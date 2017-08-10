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
            'jquery': '/bower_components/jquery/jquery.min',
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
            'page-rival-detail':'page/page.rival_detail'

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
            'base': ['widget', 'cookie'],

            'page-index': ['base','cookie'],
            'page-search-list': ['base'],
            'page-reset': ['base'],
            'page-login': ['base'],
            'page-register': ['base'],
            'page-profile': ['base'],
            'page-company': ['base'],
            'page-business': ['base'],
            'page-bid-call': ['base'],
            'page-bid-winner': ['base'],
            'page-rival': ['base', 'highcharts'],
            'page-rival-detail': ['base', 'highcharts']


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
