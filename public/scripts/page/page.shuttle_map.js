/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        waitingCancelIntervalStartTagKey:'js_shuttle_map_interval_time',
        waitingCancelInterval:'',
        loading :false,
        ticketLoading :false,
        initFirstMap :true,
        swiper:'',
        quickShowBtnNode:'.js_show_all_ticket_btn',
        shuttleBuyWrapTarget:$('.js_shuttle_buy_wrap_sec'),
        map:'',
        lines:$('#js_shuttle_lines_data').data('info'),
        mePoint: '',
        geoLocation: '',
        currentId: '',
        clusterZoomLevel: 13,
        clusterNearbyDistance: 500,
        nearMeStation: '',
        lineList:[],
        lineListMap:[],
        stations:[],//所有站点
        stationIdToLineId:[],//所有站点
        template : require('art-template'),
        lineMap :{},
        busMarkerMap:{},
        markerMap:{},
        lineMarkerMap:{},
        lineMarkerLabelMap:{},
        markerInfoMap:{},
        stationIdToInfoBoxMap:{},
        markerToLineMap:{},
        lineIdToPolyLineMap:{},
        circleTime:5000,
        showTicketFirstPlugin: null,
        showTicketPlugin: null,
        distance : 10,//相邻站点距离
        filterCommonStationStatus : false,//是否去除临近点

        /**
         * 获取站点详情
         * @param id
         * @returns {*}
         */
        getStationInfoById:function (id) {
            var info ;
            var len = init.stationData.length;
            if (len){
                for (var i=0;i<len;i++) {
                    var item = init.stationData[i];
                    if (id === item.station_id) {
                        info =  item;
                    }
                }
            }
            return info;
        },
        /**
         * 刷新地理位置
         * @param func
         */
        refreshCurrentGps:function (func) {
            init.geoLocation.getCurrentPosition(function(r){
                if(this.getStatus() === BMAP_STATUS_SUCCESS){
                    init.mePoint = r.point;
                    init.setCacheLocationData(init.mePoint);
                    if ($.isFunction(func)) {
                        console.log(1212);
                        func();
                    }
                }
                else {
                    alert('定位失败'+this.getStatus());
                }
            },{
                enableHighAccuracy: true
            });
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

        /**
         * 初始化地图
         */
        initMapAction : function(){
            var map = new BMap.Map('map-container',{enableMapClick:false});
            init.map = map;
            init.geoLocation = new BMap.Geolocation();
            map.centerAndZoom(new BMap.Point(116.404, 39.915), 12);  // 初始化

            // 添加定位控件
            var myIcon = new BMap.Icon("/images/icons/wo@2x.png", new BMap.Size(30, 30), {
                // 指定定位位置。
                // 当标注显示在地图上时,其所指向的地理位置距离图标左上
                // 角各偏移10像素和25像素。您可以看到在本例中该位置即是 // 图标中央下端的尖角位置。
                offset: new BMap.Size(50, 50),
                // 设置图片偏移。
                // 当您需要从一幅较大的图片中截取某部分作为标注图标时,您
                // 需要指定大图的偏移位置,此做法与 css sprites 技术类似。 imageOffset: new BMap.Size(0, 0 - index * 25) // 设置图片偏移
            });
            var geoCtrl = new BMap.GeolocationControl({
                showAddressBar       : false //是否显示
                , enableAutoLocation : false //首次是否进行自动定位
                , offset             : new BMap.Size(0,25)
                , locationIcon     : myIcon //定位的icon图标
            });

            //监听定位成功事件
            geoCtrl.addEventListener("locationSuccess",function(e){
                var overlays = map.getOverlays();
                var delMarkers = [];
                for(var j = 0; j< overlays.length; j++){
                    var tmpMarker = overlays[j];
                    var markerType = overlays[j].toString();
                    if(markerType == '[object Marker]' && overlays[j].getIcon().imageUrl.indexOf('/images/icons/wo@2x.png')!==-1){
                        delMarkers.push(tmpMarker);
                    }
                }
                if(delMarkers.length>1){
                    delMarkers.pop();
                    for (var i=0;i<delMarkers.length;i++){
                        map.removeOverlay(delMarkers[i]);
                    }
                }
                init.mePoint = e.point;
                init.setCacheLocationData(init.mePoint);
                // if(init.initFirstMap){
                //     init.initWithData(init.lines);
                //     init.locationNearByLine();
                // }
                init.initFirstMap = false;
            });

            //监听定位失败事件
            geoCtrl.addEventListener("locationError",function(e){
                console.log(e);
            });
            //缩放事件
            map.addEventListener("zoomstart", function () {
                $('.animation').addClass('disabled');
            });
            map.addEventListener("zoomend", function (e) {
                var zLevel = e.target.getZoom();
                console.log(e.target.getZoom());
                if (zLevel<=13){
                    init.addMapLineMarkers();
                } else {
                    init.addMapStations();
                }

                setTimeout(function () {
                    $('.animation').removeClass('disabled');
                },300)
            });
            //移动相关事件
            map.addEventListener("movestart", function () {
            });
            map.addEventListener("moving", function () {
                $('.animation').addClass('disabled');
            });
            map.addEventListener("moveend", function (e) {
                setTimeout(function () {
                    $('.animation').removeClass('disabled');
                },300)
            });
            //点击隐藏显示的站点marker
            map.addEventListener("click", function (e) {
                if(init.markerInfoMap){
                    for  (var key in init.markerInfoMap) {
                        init.markerInfoMap[key].infoBox.close();

                    }
                }
            }, false);
            // 将定位控件添加到地图
            map.addControl(geoCtrl);
            // geoCtrl.location();
            //添加控件和比例尺
            // map.addControl(new BMap.NavigationControl());
            /**
             * 加载完成事件
             */
            init.map.addEventListener("tilesloaded",function(){
                $('.anchorBL img').css('visibility','hidden');
                $('.BMap_cpyCtrl.BMap_noprint.anchorBL').css('visibility','hidden');
            });
            /**
             * 聚合物点击bug
             */
            map.addEventListener('touchstart',function(e){
                $(e.domEvent.srcElement).click()
            });

        },

        /**
         * 获取不同角度的图片
         */
        getCompassCarImage : function(angle){
            var result;
            var index=0;
            var len = 8;
            var gap = 360/len;
            var range = 22.5;
            for(var i=0;i<len;i++){
                var minAngle=i*gap-range;
                var maxAngle=i*gap+range;
                if(i==0){
                    minAngle=360-range
                }
                if(angle>minAngle && angle<=maxAngle){
                    index=i;
                    break;
                }
            }
            result='/images/cars/'+(index+1)+'.png';
            return result;
        },
        /**
         * 获取上下点 icon
         * @param type 0 站点 1上车点 2下车点   3 线路运营聚合 4线路未运营聚合 5 多线路运营聚合 6 多线路未运营聚合
         * @returns {BMap.Icon}
         */
        stationIcon : function (type) {
            var map = {
                0:{
                   w:28, h:34,wp:2, hp:1, path:'/images/icons/station-new@2x.png'
                },
                1:{
                    w:44, h:54,wp:2, hp:1, path:'/images/makers/start_station.png'
                },
                2:{
                    w:44, h:54,wp:2, hp:1,  path:'/images/makers/end_station.png'
                },
                3:{
                    w:47, h:36,wp:2, hp:1,  path:'/images/icons/station-line-active@2x.png'
                },
                4:{
                    w:47, h:36,wp:2, hp:1,  path:'/images/icons/station-line-active@2x.png'
                },
                5:{
                    w:34, h:39,wp:2, hp:1,  path:'/images/icons/station-multi-line-active@2x.png'
                },
                6:{
                    w:34, h:39,wp:2, hp:1,  path:'/images/icons/station-multi-line-active@2x.png'
                },
                7:{
                    w:28, h:34,wp:2, hp:1, path:'/images/icons/station-new-active@2x.png'
                },
            };
            var item = map[type];
            return new BMap.Icon(item.path, new BMap.Size(item.w, item.h), {
                imageSize: new BMap.Size(item.w, item.h),
                anchor: new BMap.Size((item.w/item.wp),item.h/item.hp) // 设置图片偏移
            });
        },
        /**
         * 绘制矢量图形
         * @param weight
         * @returns {BMap.IconSequence}
         */
        draw_line_direction : function (weight) {
            return new BMap.IconSequence(
                new BMap.Symbol('M0 -5 L-5 0 L0 -5 L5 0 Z', {
                    scale: 0.5,
                    strokeWeight: 1,
                    rotation: 0,
                    fillColor: 'white',
                    fillOpacity: 0.6,
                    strokeColor:'white'
                }),'100%','5%',false);
        },
        
        /**
         * 导航路线
         */
        drawRouteLine : function(line, viewStatus){
            var map = init.map;
            var stations = line.stations;
            var points = [];
            var lineId = line.line_id;
            //站点变色
            for  (var key2 in init.stations) {
                var tmpStationInfo = init.stations[key2];
                if (tmpStationInfo && tmpStationInfo.station) {
                    var stationTmpId = tmpStationInfo.station.station_id;
                    var stationLineId = tmpStationInfo.line_id;
                    if (init.markerMap[stationTmpId] && stationLineId === lineId) {
                        init.markerMap[stationTmpId].setIcon(init.stationIcon(7))
                    } else {
                        init.markerMap[stationTmpId].setIcon(init.stationIcon(0))
                    }
                }
            }


            //clear other line
            for  (var key in init.lineIdToPolyLineMap) {
                if (lineId != key) {
                    map.removeOverlay(init.lineIdToPolyLineMap[key]);
                }
            }
            if (init.lineIdToPolyLineMap[lineId]) {
                var polyLine =  init.lineIdToPolyLineMap[lineId];
                if (viewStatus) {
                    map.setViewport(polyLine.getPath());
                }
                map.addOverlay(polyLine);
                return false;
            }
            var stationLen=stations.length;
            for(var i=0;i<stationLen;i++){
                var dItem = stations[i];
                var tmpPoint = new BMap.Point( dItem.location.lng,dItem.location.lat);
                points.push(tmpPoint);
            }

            //三种驾车策略：最少时间，最短距离，避开高速
            var routePolicy = [BMAP_DRIVING_POLICY_LEAST_TIME,BMAP_DRIVING_POLICY_LEAST_DISTANCE,BMAP_DRIVING_POLICY_AVOID_HIGHWAYS];
            var driving = new BMap.DrivingRoute(map, {
                renderOptions:{policy: routePolicy[1]},
                onSearchComplete:function(res){
                    if(!res){
                        return false;
                    }
                    var planRoute=res.getPlan(0);
                    if(!planRoute){
                        return false;
                    }
                    var maxPlan = planRoute.getNumRoutes();
                    var arrPois=[];
                    for(var i=0;i<maxPlan;i++){
                        var tmpItemPois=planRoute.getRoute(i).getPath();
                        if(tmpItemPois && tmpItemPois.length){
                            arrPois = arrPois.concat(tmpItemPois);
                        }
                    }
                    var polyline=new BMap.Polyline(arrPois, {strokeColor: '#6be49c',strokeStyle:'solid',strokeOpacity:1,strokeWeight:7});
                    map.addOverlay(polyline);
                    init.lineIdToPolyLineMap[lineId] = polyline;
                    if (viewStatus) {
                        map.setViewport(polyline.getPath());
                    }
                }
            });
            var startPoint = points.shift();
            var endPint = points.pop();
            points.push(endPint);
            driving.search(startPoint, endPint,{waypoints:points});//waypoints表示途经点
        },

        
        /**
         * 获取理我最近的线路
         */
        locationNearByLine:function () {
            var len = init.stations.length;
            var mePoint = init.mePoint;
            if(len){
                var distanceMile = '';
                var nearLineId = '';
                var nearStation = '';
                for (var i=0;i<len;i++){
                    var stationA = init.stations[i];
                    var lineId = stationA.line_id;
                    var distance = $.calcDistanceBetweenPoints(stationA.location.lat,stationA.location.lng,mePoint.lat,mePoint.lng);
                    var status = false;
                    // TODO START 添加状态权重 优先定位最近的运营线路
                    var lineStatus = (parseInt(stationA.status)+1) * 100000;
                    distance = distance * lineStatus;
                    // TODO OVER
                    if(distanceMile === ''){
                        status = true;
                    }else{
                        if(distance<distanceMile){
                            status = true;
                        }
                    }
                    if(status){
                        distanceMile = distance;
                        nearLineId = lineId;
                        nearStation = stationA;
                    }
                }
                if(nearLineId){
                    var line = init.lineMap[nearLineId];
                    init.drawRouteLine(line, true);
                    init.currentId  = nearLineId;
                    init.nearMeStation  = nearStation;
                    var stationId = nearStation.station.station_id;


                    // var infoBox = init.stationIdToInfoBoxMap[stationId];
                    var point=new BMap.Point(nearStation.location.lng,nearStation.location.lat);
                    // init.map.centerAndZoom(point, 12);

                    setTimeout(function () {
                        // infoBox.open(point);
                        // init.map.panTo(point);
                        init.markerMap[stationId].dispatchEvent("click");
                        // init.initSwiperEvent();
                        init.setIntervalEvent();
                    },600);
                }

            }
        },
    
        /**
         *绘制车辆位置
         * @param data
         */
        initCarsMoveMarkers : function (data) {
            var map = init.map;
            data = data ? data : [];
            var busIds = [];
            data.forEach(function (item, i) {
                var busId= item.bus_id;
                var location= item.loc;
                var point =  new BMap.Point( location.lng,location.lat);
                var busAngleIcon = new BMap.Icon(init.getCompassCarImage(item.angle), new BMap.Size(45, 45), {
                    imageSize: new BMap.Size(45, 45),
                });
                busIds.push(busId);
                if (init.busMarkerMap[busId]) {
                    init.busMarkerMap[busId].setIcon(busAngleIcon);
                    // $(init.busMarkerMap[busId].yc).addClass('animation');
                    init.busMarkerMap[busId].setPosition(point);
                } else {
                    var busMarker = new BMap.Marker(point,{icon:busAngleIcon});  // 创建标注
                    map.addOverlay(busMarker);
                    $(busMarker.zc).addClass('animation');
                    init.busMarkerMap[busId] = busMarker;
                }
            });

            for (var key in init.busMarkerMap){
                if(busIds.indexOf(key) === -1){
                    map.removeOverlay(init.busMarkerMap[key]);
                    init.busMarkerMap[key] = null;
                }
            }
        },

        /**
         * 轮询订单
         */
        setIntervalEvent : function () {
            var lineId = init.currentId;
            if(init.loading || !lineId){
                return false;
            }
            init.loading = true;
            $.wpost($.httpProtocol.SHUTTLE_REAL_POSITION,{line_id:lineId},function (data) {
                init.loading = false;
                init.initCarsMoveMarkers(data.bus_reals);
                setTimeout(init.setIntervalEvent,init.circleTime);
            },function () {
                init.loading = false;
            },true, true);
        },

        /**
         * 获取线路列表
         */
        initTicKetDate:function () {
            if (init.loading) {
                return false;
            }
            var params = {
                timestamp : Math.floor(new Date().getTime()/1000)
            };
            init.loading = true;
            $.wpost($.httpProtocol.SHUTTLE_LIST,params,function (data) {
                init.initWithData(data.lines);
                init.locationNearByLine();
                init.loading = false;
            },function () {
                init.loading = false;
            });
        },
        /**
         * 获取线路离我最近的线路
         * @param line
         * @returns {string}
         */
        calcLineNearMeStationId:function (line) {
            var stationId = '';
            var stations = line.stations;
            var count = 0;
            var distance = 0;
            stations.forEach(function (station,j) {
                var stationInfo = station.station;
                if(stationInfo){
                    var location = station.location;
                    var sId = stationInfo.station_id;
                    if (count === 0) {
                        stationId = sId;
                    }
                    if (init.mePoint && init.mePoint.lat) {
                        var md = $.calcDistanceBetweenPoints(location.lat,location.lng,init.mePoint.lat,init.mePoint.lng);
                        if (md<distance || count === 0) {
                            distance = md;
                            stationId = sId;
                        }
                    }
                    count++;
                }
            });
            return stationId;
        },

        /**
         * 数据处理
         * @param lines
         */
        initWithData:function (lines) {
            if (!(lines && lines.length)) {
                    return false;
            }
            init.lineList = lines;
            lines.forEach(function (item,i) {
                //TODO 显示未运营线路
                if(item.status === $.shuttleLineStatus.Closed){
                    // return false;
                }
                var lineId= item.line_id;
                var lineStatus= item.status;
                var stations= item.stations;
                init.lineMap[lineId] = item;
                if(stations && stations.length>1){
                    var maxLng = stations[0].location.lng;
                    var minLng = stations[0].location.lng;
                    var maxLat = stations[0].location.lat;
                    var minLat = stations[0].location.lat;
                    stations.forEach(function (subItem,j) {
                        if(subItem.station){
                            subItem.line_id =  lineId;
                            subItem.status =  lineStatus;
                            init.stationIdToLineId[subItem.station.station_id] = lineId;
                            init.stations.push(subItem);
                            var res = subItem.location;
                            if(res.lng > maxLng) maxLng =res.lng;
                            if(res.lng < minLng) minLng =res.lng;
                            if(res.lat > maxLat) maxLat =res.lat;
                            if(res.lat < minLat) minLat =res.lat;
                        }
                    });
                    //计算线路中心点坐标
                    init.lineList[i].cenLng =(parseFloat(maxLng)+parseFloat(minLng))/2;
                    init.lineList[i].cenLat = (parseFloat(maxLat)+parseFloat(minLat))/2;
                }
            });
            //计算临近线路
            var lineLen = init.lineList.length;
            for (var m=0;m<lineLen;m++){
                var lineM = init.lineList[m];
                var lineIdM = lineM.line_id;
                lineM.line_ids = [];
                lineM.line_status_arr = [];
                lineM.line_ids.push(lineIdM);
                lineM.line_status_arr.push(lineM.status);
                for (var n=0;n<lineLen;n++){
                    if(m !== n){
                        var lineN = init.lineList[n];
                        var lineIdN = lineN.line_id;
                        var td = $.calcDistanceBetweenPoints(lineM.cenLat,lineM.cenLng,lineN.cenLat,lineN.cenLng);
                        if(td<init.clusterNearbyDistance){
                            if(lineM.line_ids.indexOf(lineIdN) === -1){
                                lineM.line_ids.push(lineIdN);
                                lineM.line_status_arr.push(lineN.status);
                            }
                            
                        }

                    }
                }
            }
            //相邻线路保留一个
            var existLineIds = [];
            var filterLinesArr = [];
            init.lineList.forEach(function(item ,k){
                init.lineListMap[item.line_id] = item;
                if (existLineIds.indexOf(item.line_id) === -1) {
                    filterLinesArr.push(item);
                    existLineIds = existLineIds.concat(item.line_ids);
                }
            });
            init.lineList = filterLinesArr;
            //计算临近的点
            var len = init.stations.length;
            if(len){
                var filterCommonStationArr = [];//相同的点进行分组
                for (var i=0;i<len;i++){
                    var stationA = init.stations[i];
                    var lineIdA = stationA.line_id;
                    stationA.line_ids = [];
                    stationA.line_ids.push(lineIdA);
                    for (var j=0;j<len;j++){
                        if(i != j){
                            var stationB = init.stations[j];
                            var lineIdB = stationB.line_id;
                            var distance = $.calcDistanceBetweenPoints(stationA.location.lat,stationA.location.lng,stationB.location.lat,stationB.location.lng);
                            if(distance<init.distance){
                                if(stationA.line_ids.indexOf(lineIdB) === -1){
                                    stationA.line_ids.push(lineIdB);
                                }
                                //筛选除需要删除的临近点
                                if(i<j && filterCommonStationArr.indexOf(j) === -1){
                                    filterCommonStationArr.push(j)
                                }
                            }

                        }
                    }
                }
                var filterArr = [];
                if(init.filterCommonStationStatus && filterCommonStationArr.length){
                    init.stations.forEach(function(item,idx){
                        if(filterCommonStationArr.indexOf(idx)===-1){
                            filterArr.push(item);
                        }
                    });
                    init.stations = filterArr;
                }
            }

            var existLineTmpIds = [];
            var filterLinesTmpArr = [];
            init.lineList.forEach(function(item ,k){
                init.lineListMap[item.line_id] = item;
                var subArr = [];
                item.stations.forEach(function (subItem) {
                    subArr = subArr.concat(subItem.line_ids);
                });
                if (existLineTmpIds.indexOf(item.line_id) === -1) {
                    filterLinesTmpArr.push(item);
                    existLineTmpIds = existLineTmpIds.concat(item.line_ids, subArr);
                }
            });
            init.lineList = filterLinesTmpArr;

            //地图添加站点
            init.addMapStations();
        },
        
        /**
         * 清除marker map
         * @param target
         */
        clearMapMarkersTarget : function (target) {
            for (var key in target){
                init.map.removeOverlay(target[key]);
            }
        },
        /**
         * 添加marker map
         * @param target
         * @returns {boolean}
         */
        addMapMarkersTarget : function (target) {
            if (!$.isEmptyObject(target)) {
                for (var key in target){
                    init.map.addOverlay(target[key]);
                    //TODO 缩放label 消失bug 处理
                    if (init.lineMarkerLabelMap[key]) {
                        target[key].setLabel(init.lineMarkerLabelMap[key]);
                    }
                }
                return true;
            } else {
                return false;
            }
        },
        /**
         * 地图添加聚合站点
         */
        addMapStations:function () {
            init.clearMapMarkersTarget(init.lineMarkerMap);
            if (init.addMapMarkersTarget(init.markerMap)) {
                return false;
            }

            var map = init.map;
            var list = init.stations;
            if(list && list.length){
                var allPoint = [] ;
                var markers = [] ;
                list.forEach(function (item,i) {
                    if(item.location.lng && item.location.lat){
                        var station = item.station;
                        var stationId = station.station_id;
                        var point = new BMap.Point( item.location.lng,item.location.lat);
                        var marker = new BMap.Marker(point,{icon:init.stationIcon(0)});
                        marker.id = stationId;
                        marker.setZIndex(-10000);
                        allPoint.push(point);
                        markers.push(marker);

                        init.addMarkerEvent(marker);
                        init.markerMap[stationId] = marker;
                        var infoBox = init.createMarkerInfoBox(item);
                        init.markerInfoMap[stationId] = infoBox;
                        // init.stationIdToInfoBoxMap[stationId] = infoBox;
                        init.markerToLineMap[stationId] = init.lineMap[item.line_id];
                        init.map.addOverlay(marker);
                    }
                });
            }
        },
        /**
         * 地图添加线路聚合站点
         */
        addMapLineMarkers:function () {
            init.clearMapMarkersTarget(init.markerMap);
            if (init.addMapMarkersTarget(init.lineMarkerMap)) {
                return false;
            }
            var list = init.lineList;
            if(list && list.length){
                list.forEach(function (item,i) {
                    if(item.cenLng && item.cenLat){
                        var id = item.line_id;
                        var point = new BMap.Point( item.cenLng,item.cenLat);
                        var lineLen = item.line_ids.length;
                        var activeVal = lineLen>1 ? 5 : 3;
                        var disableVal = lineLen>1 ? 6 : 4;
                        var iconType = item.line_status_arr.indexOf($.shuttleLineStatus.Normal)!==-1 ? activeVal : disableVal;

                        var marker = new BMap.Marker(point,{icon:init.stationIcon(iconType)});
                        if (lineLen === 1) {
                            var label = new BMap.Label(item.line_code,{offset:new BMap.Size(3,3),enableMassClear:false});
                            label.setStyle({
                                color: 'white',
                                backgroundColor: 'transparent',
                                border: '0',
                                textAlign: 'center',
                                width: '40px',
                                overflow: 'hidden',
                                textOverflow: 'ellipsis'
                            });
                            marker.setLabel(label);
                            init.lineMarkerLabelMap[id] = label;
                        }
                        marker.id = id;
                        marker.addEventListener("click",function(e){
                            if (e.domEvent) {
                                e.domEvent.stopPropagation();
                            }
                            var target = $(this)[0];
                            var lineId = target.id;
                            var p = e.target;
                            init.map.setZoom(14);
                            var line = init.lineMap[lineId];
                            var nearMeStationId = init.calcLineNearMeStationId(line);
                            var targetMarker = init.markerMap[nearMeStationId];
                            if (targetMarker) {
                                setTimeout(function () {
                                    targetMarker.dispatchEvent("click");
                                },300);
                            }

                        }, false);
                        init.lineMarkerMap[id] = marker;
                        init.map.addOverlay(marker);
                    }
                });

            }
        },
        /**
         * 地图 点击marker 事件
         * @param marker
         */
        addMarkerEvent : function (marker){
            marker.addEventListener("click",function(e){
                if (e.domEvent) {
                    e.domEvent.stopPropagation();
                }
                var target = $(this)[0];
                var mId = target.id;
                var p = e.target;
                var infoBox = init.markerInfoMap[mId];
                var line = init.markerToLineMap[mId];
                init.drawRouteLine(line);
                init.currentId = line.line_id;
                if (infoBox) {
                    for  (var key in init.markerInfoMap) {
                        var tMarker = init.markerMap[key];
                        if (key !== mId) {
                            init.markerInfoMap[key].infoBox.close();
                        }
                        
                        // var stationLineId = init.stationIdToLineId[key]
                        // if (stationLineId && stationLineId===init.currentId) {
                        //     tMarker.setIcon(init.stationIcon(7));
                        // } else {
                        //     tMarker.setIcon(init.stationIcon(0));
                        // }
                    }
                    infoBox.infoBox.open(p);
                }
                init.map.panTo(p);
                init.shuttleBuyWrapTarget.html(infoBox.html);
                if (init.shuttleBuyWrapTarget.hasClass('gone')) {
                    init.shuttleBuyWrapTarget.removeClass('gone')
                }
                init.initSwiperEvent();

            }, false);
        },
        /**
         * 创建marker infobox
         * @param station
         * @returns {BMapLib.InfoBox}
         */
        createMarkerInfoBox : function (station){
            station.lines = [];
            var currentLine = init.lineListMap[station.line_id];
            if (currentLine) {
                var lineIdsArr = [];
                var currentStations = currentLine.stations;
                currentStations.forEach(function (item) {
                    lineIdsArr = lineIdsArr.concat(item.line_ids);
                });
                // var lineIds = init.lineListMap[station.line_id].line_ids;
                // lineIdsArr = station.line_ids.concat(lineIds);
                // if (lineIds) {
                //     lineIds.forEach(function (item) {
                //         var subLineIds = init.lineListMap[item].line_ids;
                //         lineIdsArr = lineIdsArr.concat(subLineIds);
                //     })
                // }
                station.line_ids = lineIdsArr;
            }
            var existsLineIds = [];
            for (var i=0;i<station.line_ids.length;i++) {
                var lineId = station.line_ids[i];
                if (existsLineIds.indexOf(lineId) === -1) {
                    existsLineIds.push(lineId);
                    var line = init.lineMap[station.line_ids[i]];
                    station.lines.push(line);
                }
            }
            var locA = station.location;
            var locB = init.mePoint;
            var distance = 0;
            if (locB) {
                distance = $.calcDistanceBetweenPoints(locA.lat, locA.lng, locB.lat, locB.lng);
            }
            station.distance = parseInt(distance);
            station.distanceTitle = $.distanceFormat(distance);
            var html = init.template('shuttle_buy_wrap_content', station);
            var content = init.template('shuttle_map_info_window', station);
            var infoBox =  new BMapLib.InfoBox(init.map,content,{
                offset:new BMap.Size(10, 35),
                boxClass:'shuttle-map-infobox'
                ,closeIconMargin: "0 6px"
                ,enableAutoPan: true
                ,closeIconUrl: '/images/marker-delete.svg'
                ,align: INFOBOX_AT_TOP
            });
            return {
                infoBox:infoBox,
                html:html
            };
        },

        /**
         * 初始化swiper
         */
        initSwiperEvent : function () {
            if (init.swiper) {
                init.swiper.slideTo(0);
                init.swiper.destroy();
            }
            init.swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                slidesPerView: 4,
                // initialSlide :slideIndex,
                spaceBetween: 0
                // hashnav:true,
            });
        },

        /**
         * 事件初始化
         */
        initBtnEvent : function () {
   
            /**
             * 快捷出示车票
             */
            $(document).on('click', init.quickShowBtnNode,function (e, src) {
                if (init.showTicketFirstPlugin) {
                    init.showTicketFirstPlugin.clear();
                    init.showTicketFirstPlugin = false;
                }

                if (!$(this).hasClass('active')){
                    // $.locationUrl('/pay-shuttle/-1');
                    // return false;
                }

                if (init.ticketLoading) {
                    return false;
                }

                if (init.showTicketPlugin) {
                    init.showTicketPlugin.show();
                    return false;
                }

                var params = {
                    type : $.ticketType.Shuttle
                };
                init.ticketLoading = true;
                $.wpost($.httpProtocol.QUICK_SHOW_TICKET,params,function (data) {
                    if (data && data.tickets.length) {
                        $(init.quickShowBtnNode).removeClass('js_buy_btn gone').addClass('js_show_all_ticket_btn active').html('车票');
                        init.showTicketPlugin = $.showTicket({
                            ticketList : data.tickets,
                            ticketType:$.ticketType.Shuttle,
                            allCheckedFunc:function () {
                                // init.quickShowBtnNode.removeClass('active').html('购票');
                            }
                        });
                    } else {
                        if (typeof src === 'undefined') {
                            $.showToast($.string.TICKET_EMPTY);
                        } else {
                            $(init.quickShowBtnNode).removeClass('active js_show_all_ticket_btn gone').addClass('js_buy_btn').html('购票');
                        }
                    }
                    init.ticketLoading = false;
                },function () {
                    init.ticketLoading = false;
                });
            });
            var lastTickets = $.cache.get($.cacheLastShuttleTicketKey);
            if (lastTickets && lastTickets.length) {
                $(init.quickShowBtnNode).removeClass('js_buy_btn gone').addClass('js_show_all_ticket_btn active').html('车票');
                init.showTicketFirstPlugin = $.showTicket({
                    ticketList : lastTickets,
                    ticketType:$.ticketType.Shuttle,
                    allCheckedFunc:function () {
                        // init.quickShowBtnNode.removeClass('active').html('购票');
                    }
                });
                $.cache.remove($.cacheLastShuttleTicketKey);
            } else {
                $(init.quickShowBtnNode).trigger('click', ['trigger']);
            }
            /**
             * code 点击
             */
            $(document).on('click', '.smh-code', function () {
                var $this = $(this);
                var section = $this.parents('.sbw-content');
                $this.siblings().removeClass('active');
                $this.addClass('active');
                var slideNode = $('.bg-slide');
                slideNode.css('left',slideNode.width()*$this.index());
                var lineId = $this.data('id');
                init.currentId = lineId;
                $('.js_line_item',section).addClass('gone');
                $('.js_item_'+lineId, section).removeClass('gone');
                var line = init.lineMap[lineId];
                init.drawRouteLine(line);
                // $('.js_line_price_item',section).addClass('gone');
                // $('.js_line_price_'+lineId, section).removeClass('gone');
            });
            
            /**
             * 购买
             */
            $(document).on('click', '.js_buy_btn', function () {
                var target = init.shuttleBuyWrapTarget.find('.swiper-slide.active');
                var lineId = target.data('id');
                var status = target.data('status');
                var lineCode = target.data('line_code');
                if (!lineId) {
                    $.showToast($.string.PLEASE_CHECKED_LINE);
                    return false;
                }
                if (status === $.shuttleLineStatus.Closed) {
                    $.showToast($.string.LINE_CAN_NOT_BUY);
                    return false;
                }

                $.locationUrl('/pay-shuttle/{0}?src=map&t={1}'.format(lineId, new Date().getTime()));
            });

            $(document).on('click', '.shuttle-map-infobox', function (e) {
                e.stopPropagation();
            });

        },

        initData : function () {
            init.initWithData(init.lines);
            init.locationNearByLine();
        },

        run : function () {
            /**
             * 初始化地图
             */
            init.initMapAction();
            
            /**
             * 初始化数据
             */
            // init.initTicKetDate();
            
            /**
             * 初始化相关事件
             */
            init.initBtnEvent();

            /**
             * 数据初始化
             */
            if (init.getCacheLocationData()) {
                init.mePoint = init.getCacheLocationData();
                init.initData();
            } else {
                init.refreshCurrentGps(init.initData);
            }
        }
    };
    init.run();


})($);