/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        buyNode : '.js_buy_btn',
        disabledNode : '.js_disabled_btn',
        cacheShuttleListTag : 'js_cache_shuttle_list_data',
        template : require('art-template'),
        loading :false,
        map :'',
        lines :'',
        geoLocation :'',
        deferred :'',
        deferred2 :'',
        mePoint :'',
        listSection:$('#list'),
        by : function(name,minor){
            return function(o,p){
                var a,b;
                if(o && p && typeof o === 'object' && typeof p ==='object'){
                    a = o[name];
                    b = p[name];
                    if(a === b){
                        return typeof minor === 'function' ? minor(o,p):0;
                    }
                    if(typeof a === typeof b){
                        return a <b ? -1:1;
                    }
                    return typeof a < typeof b ? -1 : 1;
                }else{
                    throwit("error");
                }
            }
        },
        /**
         * 列表排序
         * @param list
         * @returns {Array}
         */
        sortListData : function (list) {
           var len = list.length;
           if (len && init.mePoint) {
                list.forEach(function (line, key) {
                    var stations = line.stations;
                    list[key].distanceArr = [];
                    if (stations && stations.length){
                        stations.forEach(function (station) {
                            var tmpDistance = $.calcDistanceBetweenPoints(station.location.lat,station.location.lng,init.mePoint.lat,init.mePoint.lng);
                            list[key].distanceArr.push(tmpDistance);
                        })
                    }
                    list[key].distance = Math.min.apply(null, list[key].distanceArr);
                });
               list.sort(init.by('status',init.by('distance')));
           }

           return list;
        },

        /**
         * 获取线路列表
         */
        initLineList:function (timestamp) {
            init.deferred2 = $.Deferred();   //宣告Deferred物件
            var cacheList= $.cache.get(init.cacheShuttleListTag);
            if (cacheList) {
                init.deferred2.resolve();
                return false;
            }

            if (init.loading && !timestamp) {
                return false;
            }
            var params = {
                timestamp : timestamp
            };
            init.loading = true;
            $.wpost($.httpProtocol.SHUTTLE_LIST,params,function (data) {
                init.lines = data;
                init.loading = false;
                init.deferred2.resolve();
            },function () {
                init.deferred2.reject();   //宣告Deferred物件
                init.loading = false;
            });
            return init.deferred2.promise()
        },
        refreshCurrentGps:function (func) {
            var cacheLocation = init.getCacheLocationData();
            if (cacheLocation) {
                init.mePoint = cacheLocation;
                init.deferred.resolve();
                return  init.deferred.promise();
            }
            init.geoLocation.getCurrentPosition(function(r){
                if(this.getStatus() === BMAP_STATUS_SUCCESS){
                    init.mePoint = r.point;
                    init.setCacheLocationData(init.mePoint);
                    if ($.isFunction(func)) {
                        func();
                    }
                    console.log(r.point);
                    init.deferred.resolve();
                }
                else {
                    init.deferred.reject();
                    alert('定位失败'+this.getStatus());
                }
            },{
                enableHighAccuracy: true
            });
            return init.deferred.promise();
        },
        /**
         * 初始化地图
         */
        initMapAction : function(){
            init.geoLocation = new BMap.Geolocation();
            init.deferred = $.Deferred();   //宣告Deferred物件
            // init.refreshCurrentGps().done(function () {
            //     console.log('error')
            // });
        },

        /**
         * 设置cache location缓存
         * @param location
         */
        setCacheLocationData : function (location) {
            var data = {
                location:location,
                time:new Date().getTime()
            };
            $.localCache.set($.cacheLocationKey, data);
        },

        /**
         * 获取cache location缓存
         */
        getCacheLocationData : function () {
            var cacheData = $.localCache.get($.cacheLocationKey);
            var time = new Date().getTime();
            var location = false;
            if (cacheData && time - cacheData.time < 1000 * 60 * 10) {
                location = cacheData.location;
            }
            return location;
        },

        initBtnEvent : function () {
            /**
             * 跳转详情
             */
            $(document).on('click', init.buyNode,function () {
                var $this = $(this);
                var lineId = $this.parents('.shuttle-item').data('id');
                $.locationUrl('/pay-shuttle/'+lineId);
            });

            $(document).on('click', init.disabledNode,function (e) {
                e.stopPropagation();
            });

        },
        run : function () {
            //搜索
            init.initMapAction();
            // init.initLineList();
            init.initBtnEvent();
            
            $.when(init.refreshCurrentGps(), init.initLineList()).then(function () {
                console.log('over');
                var data = init.lines;
                var cacheList= $.cache.get(init.cacheShuttleListTag);
                if (cacheList) {
                    data = cacheList;
                } else {
                    data.lines = init.sortListData(data.lines);
                    $.cache.set(init.cacheShuttleListTag, data);
                }
                var html = init.template('tpl-shuttle-list', data);
                init.listSection.html(html);
            });


        }
    };
    init.run();
})($);