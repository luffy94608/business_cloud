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
                    type: 'area'
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    type: 'category',
                    // categories: ['品牌', '资源', '技能', '注册资本'],
                    lineWidth: 0,
                    tickWidth: 0,
                    gridLineWidth: 0,
                    labels: {
                        format: '{value}'
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
                tooltip: {
                    headerFormat:'',
                    pointFormat: '<span style="color: #95E4DA;"> {point.y}分</span>'
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    area: {
                        pointStart: 1940,
                        color: '#95E4DA',
                        marker: {
                            enabled: false,
                            symbol: 'circle',
                            radius: 2,
                            states: {
                                hover: {
                                    enabled: true
                                }
                            }
                        }
                    }
                },
                series: [{
                    name: '中标',
                    data: [
                        {name:'1',   y: 70},
                        {name:'2',   y: 80},
                        {name:'3',   y: 90},
                        {name:'4',   y: 70}
                        ]
                }]
            });

            $('#js_chart_2').highcharts({
                title: {
                    text: ''
                },
                chart: {
                    type:'spline'
                },
                tooltip: {
                    headerFormat:'',
                    pointFormat: '<span style="color: #FFC076;">{series.name}: {point.y}</span>'
                },
                legend: {
                    enabled: false
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
                    }
                },
                yAxis: {
                    title: {
                        text: null
                    }
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    spline: {
                        lineWidth: 2,
                        marker: {
                            enabled: false
                        },
                        color:'#FFC076'
                    }
                },
                series: [{
                    type: 'spline',
                    // innerSize: '80%',
                    name: '活跃度',
                    data: [
                        {name: '7月1日', y: 10},
                        {name: '7月2日', y: 40},
                        {name: '7月3日', y: 60},
                        {name: '7月4日', y: 50},
                        {name: '7月5日', y: 90},
                        {name: '7月6日', y: 60}
                    ]
                }]
            });

            $('#js_chart_3').highcharts({
                title: {
                    text: ''
                },
                tooltip: {
                    headerFormat:'',
                    pointFormat: '<span style="color: #FF8D8C;">{series.name}: {point.y}</span>'
                },
                legend: {
                    enabled: false
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
                    }
                },
                yAxis: {
                    title: {
                        text: null
                    }
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    line: {
                        lineWidth: 2,
                        marker: {
                            enabled: true
                        },
                        color:'#FF8D8C'
                    }
                },
                series: [{
                    name: '金额',
                    data: [
                        {name: '7月1日', y: 10},
                        {name: '7月2日', y: 40},
                        {name: '7月3日', y: 60},
                        {name: '7月4日', y: 50},
                        {name: '7月5日', y: 90},
                        {name: '7月6日', y: 60}
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
