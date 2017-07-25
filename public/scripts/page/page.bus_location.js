/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        stationData : $('#js_station_data').data('info'),
        lineId : $('#js_line_id_data').data('info'),
        waitingCancelIntervalStartTagKey:'js_bus_location_interval_time',
        waitingCancelInterval:'',
        loading :false,
        firstStatus :true,
        map:'',
        linePoints:[],
        currentBusId:'',
        busMarkerMap:{},
        busMarkerInfoBoxMap:{},
        markerMap:{},
        markerInfoMap:{},
        stationInfoMap:{},
        showTitleTarget:$('#js_position_section'),
        showRealPicBtnNode:'.js_show_real_pic',
        circleTime:5000,

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
                    if (id == item.station_id) {
                        info =  item;
                    }
                }
            }
            return info;
        },
        /**
         * 初始化地图
         */
        initMapAction : function(){
            var map = new BMap.Map('map-container',{enableMapClick:false});
            init.map = map;
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
                    if(markerType == '[object Marker]' && overlays[j].Xc.innerHTML.indexOf('/images/icons/wo@2x.png')!==-1){
                        delMarkers.push(tmpMarker);
                    }
                }
                if(delMarkers.length>1){
                    delMarkers.pop();
                    for (var i=0;i<delMarkers.length;i++){
                        map.removeOverlay(delMarkers[i]);
                    }
                }
                return false;
            });


            //监听定位失败事件
            geoCtrl.addEventListener("locationError",function(e){
                console.log(e);
            });
            //拖动时汽车动画bug
            map.addEventListener("zoomstart", function () {
                $('.animation').addClass('disabled');
            });
            map.addEventListener("zoomend", function () {
                setTimeout(function () {
                    $('.animation').removeClass('disabled');
                },300)
            });
            map.addEventListener("touchmove", function () {
                $('.animation').addClass('disabled');
            });
            map.addEventListener("touchend", function () {
                setTimeout(function () {
                    $('.animation').removeClass('disabled');
                },300)
            });
            // 将定位控件添加到地图
            map.addControl(geoCtrl);
            //添加控件和比例尺
            // map.addControl(new BMap.NavigationControl());

            //路线导航
            init.drawRouteLine();

            init.map.addEventListener("tilesloaded",function(){
                $('.anchorBL img').css('visibility','hidden');
                $('.BMap_cpyCtrl.BMap_noprint.anchorBL').css('visibility','hidden');
            });

            //点击隐藏显示的站点marker
            map.addEventListener("click", function (e) {
                init.clearAllMarker();
                init.clearAllBusMarker();
            }, false);
        },
        
        /**
         * 清除所有的站点marker info box
         */
        clearAllMarker : function () {
            if(init.markerInfoMap){
                for  (var key in init.markerInfoMap) {
                    init.markerInfoMap[key].close();

                }
            }
        },
        /**
         * 清除所有的车辆marker info box
         */
        clearAllBusMarker : function () {
            if(init.busMarkerInfoBoxMap){
                for  (var key in init.busMarkerInfoBoxMap) {
                    init.busMarkerInfoBoxMap[key].close();

                }
            }
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
         * @param type 0 站点 1上车点 2下车点
         * @returns {BMap.Icon}
         */
        stationIcon : function (type) {
            var map = {
                0:{
                   w:24, h:24,wp:2, hp:2, path:'/images/makers/mid_station.png'
                },
                1:{
                    w:33, h:40.5,wp:2, hp:1, path:'/images/makers/start_station.png'
                },
                2:{
                    w:33, h:40.5,wp:2, hp:1,  path:'/images/makers/end_station.png'
                },
                3:{
                    w:36, h:36,wp:2, hp:2,  path:'/images/makers/bus.png'
                },
                4:{
                    w:36, h:36,wp:2, hp:2,  path:'/images/makers/bus_active.png'
                }
            };
            var item = map[type];
            return new BMap.Icon(item.path, new BMap.Size(item.w, item.h), {
                imageSize: new BMap.Size(item.w, item.h),
                anchor: new BMap.Size((item.w/item.wp),item.h/item.hp) // 设置图片偏移
            });
        },
        
        /**
         * 导航路线
         */
        drawRouteLine : function(){
            var map = init.map;
            var stations = init.stationData;
            var points = [];

            var stationLen=stations.length;
            for(var i=0;i<stationLen;i++){
                var dItem = stations[i];
                var stationId = dItem.station_id;
                var tmpPoint = new BMap.Point( dItem.location.lng,dItem.location.lat);
                points.push(tmpPoint);

                var iconType = 0;
                if (i == 0) {
                    iconType = 1;
                } else if(i == stationLen-1){
                    iconType = 2;
                }
                var marker = new BMap.Marker(tmpPoint,{icon:init.stationIcon(iconType)});  // 创建标注
                marker.id = stationId;

                map.addOverlay(marker);               // 将标注添加到地图中
                init.addMarkerEvent(marker);
                init.markerMap[stationId] = marker;
                var infoBox = init.createMarkerInfoBox(dItem);
                init.markerInfoMap[marker.id] = infoBox;
                init.stationInfoMap[stationId] = infoBox;
            }
            init.linePoints = points;
            //三种驾车策略：最少时间，最短距离，避开高速
            var routePolicy = [BMAP_DRIVING_POLICY_LEAST_TIME,BMAP_DRIVING_POLICY_LEAST_DISTANCE,BMAP_DRIVING_POLICY_AVOID_HIGHWAYS];
            var driving = new BMap.DrivingRoute(map, {
                renderOptions:{policy: routePolicy[1]},
                //renderOptions:{map: map, autoViewport: true},
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
                    var polyline=new BMap.Polyline(arrPois, {strokeColor: '#4ccc7f',strokeStyle:'solid',strokeOpacity:1,strokeWeight:5});
                    map.addOverlay(polyline);
                    // map.setViewport(arrPois);
                }
            });
            var startPoint = points.shift();
            var endPint = points.pop();
            driving.search(startPoint, endPint,{waypoints:points});//waypoints表示途经点
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
                init.clearAllBusMarker();
                if (infoBox) {
                    for  (var key in init.markerInfoMap) {
                        if (key != mId) {
                            init.markerInfoMap[key].close();
                        }
                    }
                    infoBox.open(p);
                }

            });
        },
        /**
         * 地图 点击 实时位置marker 事件
         * @param marker
         */
        addBusMarkerEvent : function (marker){
            marker.addEventListener("click",function(e){
                if (e.domEvent) {
                    e.domEvent.stopPropagation();
                }
                var target = $(this)[0];
                var mId = target.id;
                var p = e.target;
                init.clearAllMarker();
                var infoBox = init.busMarkerInfoBoxMap[mId];
                if (infoBox) {
                    for  (var key in init.busMarkerInfoBoxMap) {
                        if (key != mId) {
                            init.busMarkerInfoBoxMap[key].close();
                        }
                    }
                    infoBox.open(p);
                }
                //显示实时位置title
                init.showPositionTitle(target.info, true);
            });
        },

        /**
         * 创建marker infobox
         * @param station
         * @returns {BMapLib.InfoBox}
         */
        createMarkerInfoBox : function (station){
            var contentTpl="" +
                "<div class='marker-station'>" +
                "   <div class='ms-left'>" +
                "       <p class='msl-name'>{0}</p>" +
                "       <p class='msl-content'>" +
                "           <span class='msl-time gone'>预计 {1} 出发</span>" +
                "           <span class='msl-btn js_show_real_pic' data-id='{2}'>查看实景</span>" +
                "       </p>" +
                "   </div>" +
                "   <div class='ms-right'>" +
                "       <a href='{3}' class='js_go_here'>" +
                "           <p class='msr-go'></p>" +
                "           <p class='msr-txt'>去这里</p>" +
                "       </a>" +
                "   </div>" +
                "</div>";

            var dhUrl = 'http://api.map.baidu.com/marker?location={0},{1}&title={2}&content={3}&output=html';
            dhUrl  = dhUrl.format(station.location.lat, station.location.lng, station.short_name, station.verbose);
            var stationId = station.station_id;

            var content = contentTpl.format(station.short_name, station.arrived_at, stationId, dhUrl);
            return  new BMapLib.InfoBox(init.map,content,{
                offset:new BMap.Size(10, 35),
                boxClass:'custom-map-infobox'
                ,closeIconMargin: "0 6px"
                ,enableAutoPan: true
                ,closeIconUrl: '/images/marker-delete.svg'
                ,align: INFOBOX_AT_TOP
            });
        },
        /**
         * 创建 bus marker infobox
         * @param data
         * @returns {BMapLib.InfoBox}
         */
        createBusMarkerInfoBox : function (data){
            var html = "<div class='sm-info-window'>{0}</div>";
            var content = html.format(data.cur_pos);
            var infoBox =  new BMapLib.InfoBox(init.map,content,{
                offset:new BMap.Size(10, 17),
                boxClass:'shuttle-map-infobox'
                ,closeIconMargin: "0 6px"
                ,enableAutoPan: false
                ,closeIconUrl: '/images/marker-delete.svg'
                ,align: INFOBOX_AT_TOP
            });
            return infoBox;
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
                var car = item.car;
                var busId= car.id;
                var location= item.cur_loc;
                var point =  new BMap.Point( location.lng,location.lat);
                var busAngleIcon = init.stationIcon(3);
                busIds.push(busId);
                
                if (init.busMarkerMap[busId]) {
                    // init.busMarkerMap[busId].setIcon(busAngleIcon);
                    // $(init.busMarkerMap[busId].yc).addClass('animation');
                    if (item.cur_pos !== init.busMarkerMap[busId].info.cur_pos) {
                        init.busMarkerInfoBoxMap[busId].close();
                        init.busMarkerInfoBoxMap[busId] = init.createBusMarkerInfoBox(item);
                    }
                    init.busMarkerMap[busId].info = item;

                } else {
                    var busMarker = new BMap.Marker(point,{icon:busAngleIcon});  // 创建标注
                    busMarker.id = busId;
                    busMarker.info = item;
                    map.addOverlay(busMarker);
                    $(busMarker.yc).addClass('animation');
                    init.busMarkerMap[busId] = busMarker;
                    init.addBusMarkerEvent(busMarker);
                    init.busMarkerInfoBoxMap[busId] = init.createBusMarkerInfoBox(item);
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
         * 显示实时位置
         * @param info
         */
        showPositionTitle:function (info, status) {
            var emptyTpl = "<div class='fm-empty '> 暂未运营 </div>";
            var tpl = " <div class='fm-item '> " +
                "<div class='fmo-left'> " +
                "   <p class='title'>{0} * {1}</p> " +
                "   <p class='sub-title'>正在发往下一站 {2}</p> " +
                "   <p class='color-orange'>车辆位置：{3}</p> " +
                "</div> " +
                "<div class='fmo-right'> " +
                "   <a href='tel:{4}'  class='js_driver_mobile'> " +
                "   <img width='40px' height='40px' src='/images/icons/tel@3x.png'> " +
                "   </a> " +
                "</div> " +
                "</div>";
            var html = '';
            if (info) {
                var car = info.car;
                init.currentBusId = car.id;
                html  = tpl.format(car.driver, car.plate, info.next_station_name, info.cur_pos, car.mobile);
            } else {
                html = emptyTpl;
                init.currentBusId = '';
            }
            
            init.showTitleTarget.html(html);
            init.refreshCarMarkerIconEvent();
            var busMarker = init.busMarkerMap[init.currentBusId];
            if (busMarker && status) {
                init.map.panTo(busMarker.point);
            }
        },
        /**
         * 修改选中车辆icon
         */
        refreshCarMarkerIconEvent : function () {
            if (init.busMarkerMap) {
                for (var key in init.busMarkerMap) {
                    var iconType = 4;
                    if (key === init.currentBusId) {
                        iconType = 3;
                    }
                    var icon = init.stationIcon(iconType);
                    if (init.busMarkerMap[key].getIcon().imageUrl !==  icon.imageUrl) {
                        init.busMarkerMap[key].setIcon(icon);
                    }
                }
            }
        },

        /**
         * 轮询订单
         */
        setIntervalEvent : function () {
            var lineId = init.lineId;
            if(init.loading || !lineId){
                return false;
            }
            init.loading = true;
            $.wpost($.httpProtocol.BUS_REAL_POSITION,{line_id:lineId},function (data) {
                init.loading = false;
                init.initCarsMoveMarkers(data.real_locs);
                var locArr = data.real_locs ? data.real_locs : [];
                locArr.forEach(function (item, i) {
                    var car = item.car;
                    var busId= car.id;
                    if (busId === init.currentBusId || (!init.currentBusId && i === 0 )) {
                        init.showPositionTitle(item, init.firstStatus);
                        if (init.firstStatus) {
                            init.firstStatus = false;
                        }
                    }
                });
                setTimeout(init.setIntervalEvent,init.circleTime);
            },function (data) {
                var msg = data.msg ? data.msg : '暂未运营';
                var emptyTpl = "<div class='fm-empty '> {0} </div>".format(msg);
                init.showTitleTarget.html(emptyTpl);
                init.map.setViewport(init.linePoints);
                init.loading = false;
            },true, true);
        },

        /**
         * 事件初始化
         */
        initBtnEvent : function () {
            /**
             * 点击查看实景图
             */
            $(document).on('touchend', init.showRealPicBtnNode,function () {
                var $this = $(this);
                var id = $this.data('id');
                var station = init.getStationInfoById(id);
                var pics = station.photos;
                var images = [];
                var upYunHost = document.global_config_data.upyun_host;
                if(pics && pics.length){
                    for (var i=0;i< pics.length;i++){
                        var url = upYunHost+pics[i];
                        images.push(url)
                    }
                }
                if (images.length){
                    wx.previewImage({
                        current: images[0],
                        urls: images
                    });
                } else {
                    $.showToast($.string.STATION_PIC_EMPTY);
                }
            });

            $(document).on('touchend', '.js_go_here',function (e) {
                e.stopPropagation();
                e.preventDefault();
                var $this = $(this);
                var url = $this.attr('href');
                if (url) {
                    $.locationUrl(url);
                }

            });

            /**
             * 定位班车
             */
            $('#js_location_bus').unbind().bind('click',function () {
                var busMarker = init.busMarkerMap[init.currentBusId];
                if (!busMarker) {
                    for (var key in init.busMarkerMap) {
                        if (init.busMarkerMap[key]) {
                            busMarker = init.busMarkerMap[key];
                        }
                    }
                }
                if (busMarker) {
                    init.map.panTo(busMarker.point);
                } else {
                    $.showToast($.string.BUS_NOT_FOUND);
                }
            });

        },
        run : function () {
            init.initMapAction();
            init.initBtnEvent();
            init.setIntervalEvent();
        }
    };
    init.run();


})($);