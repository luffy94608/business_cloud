/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        searchInputMask : $('.input-mask'),
        searchInputNode : '.bcb-search',
        loading :false,
        /**
         * 图表
         */
        createChartEvent : function () {
            $('#js_chart_1').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                    // spacing : [100, 0 , 40, 0]
                },
                title: {
                    floating:true,
                    text: ''
                },
                tooltip: {
                    // headerFormat:'企业数量',
                    pointFormat: '{series.name}: <b>{point.y}</b>'
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        labels: {
                            format: '{value}'
                            // style: {
                            //     color: '{point.color}'
                            // }
                        },
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y}',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    innerSize: '80%',
                    name: '数量',
                    data: [
                        {name:'大型企业',   y: 15},
                        {name:'中型企业',   y: 25},
                        {name:'小型企业',   y: 35},
                        {name:'外资企业',   y: 25},
                        {name:'国有企业',   y: 45}
                    ]
                }]
            });

            $('#js_chart_2').highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    // visible:false,
                    type: 'category',
                    // categories: ['品牌', '资源', '技能', '注册资本'],
                    lineWidth: 0,
                    tickWidth: 0,
                    gridLineWidth: 0,
                    labels: {
                        format: '{value}'
                        // style: {
                        //     color: '{point.color}'
                        // }
                    }
                },
                yAxis: {
                    labels : false,
                    title: {
                        text: null
                    },
                    gridLineWidth: 0
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format:'<span style="color:{color}">{y}</span>'
                        } ,
                        colorByPoint:false

                    }
                },
                tooltip: {
                    headerFormat:'',
                    pointFormat: '<span style="color:{point.color}">竞争力：{point.y}</span>'
                },
                series: [{
                    name: '',
                    colorByPoint: true,
                    data:[{
                        name: '品牌',
                        color : "#61D7C7",
                        y: 60
                    }, {
                        name: '资源',
                        color : "#9898F8",
                        y: 80
                    }, {
                        name: '技能',
                        color : "#FF9191",
                        y: 40
                    }, {
                        name: '注册资本',
                        color : "#47CAFE",
                        y: 22
                    }
                    ]
                }]
            });

        },
        /**
         * 首页内容区域和 side 高度一直
         */
        fixBodyHeight:function () {
            if ($(window).width()<768) {
                return false;
            }
            var sideSection = $('.bc-side-section');
            var bodySection = $('.bc-stat-section');
            var wrapSection = $('.bc-body-section');
            var bodyHeadSection = $('.bc-section-title', wrapSection);
            var bodyContentSection = $('.d-table', wrapSection);
            var sh = sideSection.height();
            var bh = bodySection.height();
            var bhh = bodyHeadSection.height();
            if (bh < sh) {
                bodySection.height(sh);
                bodyContentSection.height(sh-bhh);
            } else {
                sideSection.height(bh);
                bodyContentSection.height(bh-bhh);
            }


        },

        initBtnEvent : function () {

            /**
             * 跳转详情
             */
            $(document).on('focus', init.searchInputNode,function () {
                init.searchInputMask.hide();
            });
            $(document).on('blur', init.searchInputNode,function () {
                init.searchInputMask.fadeIn();
            });

            /**
             * tab切换
             */
            $(document).on('click', '.tab-item',function () {
                
            });


        },
        run : function () {
            //
            init.initBtnEvent();
            init.createChartEvent();
            init.fixBodyHeight();
        }
    };
    init.run();



})($);
