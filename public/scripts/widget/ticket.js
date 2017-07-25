(function ($) {
   //车票插件

    $.fn.addShowTicketEvent = function (opts) {
      var st = {
          currentTarget : '',
          ticketList: [],
          timer:null,
          uniqueId:null,
          loading:false,
          itemPrefix:'#js_ticket_',
          checkBtnNode:'.js_ticket_check_btn',
          modalNode:'#js_ticket_modal',
          modal:$('#js_ticket_modal'),
          styleSecTargetNode:'.js_style_section',
          ticketListSecNode:'#js_ticket_list',
          ticketTpl:"<li class='stl-item' id='js_ticket_{0}'><div class='js_style_section'></div> " +
          "<header class='tk-header'> " +
          " <div class='thh-content'> " +
          "     <p class='code'>{1}</p> " +
          "     <p class='name'>{2}</p> " +
          "</div> " +
          "<span class='tk-checked js_bg_checked_color' data-title='已验票'></span> " +
          "</header> " +
          "<article class='tk-body js_bg_color '> " +
          " <p class='date'>{3}</p> " +
          " <p class='info'>乘车时间：{4}</p> " +
          " <p class='info'>车票价格：{5}元</p> " +
          "</article> " +
          "<p class='tk-gap js_bg_color js_before_after'> <span class='line'></span> </p>" +
          " <footer class='tk-footer js_bg_color'> " +
          " <div class='tkf-content'> " +
          "     <div> " +
          "         <p class='info'>车牌号</p> " +
          "         <p class='title'>{6}</p> " +
          "     </div> " +
          "     <div> " +
          "         <p class='info'>座位号码</p> " +
          "         <p class='title'>{7}</p> " +
          "     </div> " +
          "</div> " +
          "<button class='btn btn-primary tk-btn text-center full-width js_ticket_check_btn disabled' data-id='{0}'> 上车验票 </button>" +
          "</footer> " +
          "</li>",
          shuttleTicketTpl:"<li class='stl-item' id='js_ticket_{0}'><div class='js_style_section'></div> " +
          "<header class='tk-header'> " +
          " <div class='thh-content'> " +
          "     <p class='code'>{1}</p> " +
          "</div> " +
          "<span class='tk-checked js_bg_checked_color' data-title='已验票'></span> " +
          "</header> " +
          "<article class='tk-body js_bg_color '> " +
          " <p class='info'>有效期</p> " +
          " <p class='date'>{2}</p> " +
          " <p class='info'>{3}</p> " +
          "</article> " +
          "<p class='tk-gap js_bg_color js_before_after'> <span class='line'></span> </p>" +
          " <footer class='tk-footer js_bg_color'> " +
          " <div class='tkf-content'> " +
          " <p class='info'>{4}</p> " +
          " <p class='info'>一票一人</p> " +
          "</div> " +
          "<button class='btn btn-primary tk-btn text-center full-width js_ticket_check_btn disabled' data-id='{0}'> 上车验票 </button>" +
          "</footer> " +
          "</li>"
      };
      if (opts) {
          $.extend(st, opts);
      }
      var $this = $(this);

      //modal 关闭事件
      st.modal.find('.js_close_btn').unbind().bind('click', function () {
          st.modal.removeClass('active');
          if (st.timer) {
              clearInterval(st.timer);
          }
      });

        /**
         * 车票背景颜色变色
         * @param target
         * @param color
         * @param checkedColor
         */
      $this.cgTicketBg = function (target, color, checkedColor) {
          var styleTpl = '<style type="text/css">{0} .js_before_after:after,{0} .js_before_after:before { background: radial-gradient(circle, rgba(0,0,0,0),rgba(0,0,0,0) 50%,{1}  50%,{1} )!important; }</style>';
          var id = '#'+target.attr('id');
          var styleStr = styleTpl.format(id, color);
          $('.js_bg_color', target).css('backgroundColor', color);
          if (checkedColor) {
              $('.js_bg_checked_color', target).css('backgroundColor', checkedColor);
          }
          $(st.styleSecTargetNode, target).html(styleStr);
      };

        /**
         * 车票状态切换
         * @param target  
         * @param step  变化 0 正常状态 1开始验票 2已经验票
         * @param color  1状态下的颜色
         * @param time  验票时间或者开始验票时间 时间戳秒
         */
        $this.ticketStep = function (target ,step, color, time) {
            var prevStep = target.data('step');
            if (typeof  prevStep === 'undefined' || prevStep !== step) {
                target.data('step', step)
            } else {
                return false;
            }

            var targetBtn = target.find(st.checkBtnNode);
            var changeHeaderTarget = target.find('.tk-header');
            color='#'+color.substr(2);
            target.removeClass('active checked expired'); //文字颜色
            switch (step){
                case 0 : //正常状态验票之前
                    // targetBtn.html($.string.TICKET_CHECKED_BTN_AHEAD_TITLE.format($.showEndTime(time)));
                    targetBtn.html($.string.TICKET_CHECKED_BTN_NOT_TITLE);
                    targetBtn.addClass('disabled');
                    changeHeaderTarget.addClass('bg-color-transition');
                    $this.cgTicketBg(target, '#ffffff');
                    break;
                case 1 : //开始验票
                    targetBtn.html($.string.TICKET_CHECKED_BTN_TITLE);
                    $this.cgTicketBg(target, color);
                    targetBtn.removeClass('disabled');
                    changeHeaderTarget.addClass('bg-color-transition');
                    target.addClass('active');
                    break;
                case 2 ://已经验票
                    changeHeaderTarget.removeClass('bg-color-transition');
                    if (time>0) {
                        targetBtn.html($.string.TICKET_CHECKED_BTN_ACTIVE_TITLE.format(new Date(time*1000).Format('hh:mm:ss')));
                    } else {
                        targetBtn.html($.string.TICKET_CHECKED_TITLE);
                    }
                    targetBtn.addClass('disabled');
                    $this.cgTicketBg(target, '#ffffff', color);
                    target.addClass('checked');
                    break;
                case 3 ://已经过验票时间 未验票
                    changeHeaderTarget.removeClass('bg-color-transition');
                    targetBtn.html($.string.TICKET_LINE_CLOSED);
                    $this.cgTicketBg(target, '#ffffff');
                    targetBtn.addClass('disabled');
                    target.addClass('expired');
                    break;
            }
        };

        /**
         * 验票完成后的回调
         * @param id
         * @param checkTime
         */
        $this.checkedTicketCallback = function (id, checkTime) {
            var len = st.ticketList.length;
            if (len){
                for (var i=0; i<len; i++) {
                    var item = st.ticketList[i];
                    if (item.ticket_id === id) {
                        st.ticketList[i].use_status = $.ticketStatus.WaitRemark;
                        st.ticketList[i].check_time = checkTime;
                        var cacheKey = 'js_ticket_list_cache_key_'+new Date(item.dept_at*1000).Format('yyyy_MM_dd');
                        $.cache.remove(cacheKey);
                    }
                }
            }
        };

        /**
         * 验票
         */
        st.modal.on('click', st.checkBtnNode,function () {
            var id = $(this).data('id');
            if (st.loading || !id || $(this).hasClass('disabled')) {
                return false;
            }
            var params = {
                ticket_id : id
            };
            var ticketInfo = $this.getTicketInfoById(id);
            var isBusTicket = ((ticketInfo.ticket_type == $.ticketType.Bus) ? true : false);
            $.confirm({
                content:$.string.TICKET_CHECKED_HINT,
                titleShow:true,
                success:function () {
                    if (!navigator.onLine) {//没有网络
                        var checkTime = Math.floor(new Date().getTime()/1000);
                        var cacheData = $.localCache.get($.offLineCheckedTicketKey);
                        if (!cacheData) {
                            cacheData = {
                                bus_ticket:[],
                                shuttle_ticket:[],
                            }
                        }
                        var checkedTicketItem = {
                            ticket_id : id,
                            check_time :checkTime
                        };

                        if (isBusTicket) {
                            cacheData.bus_ticket.push(checkedTicketItem);
                        } else {
                            cacheData.shuttle_ticket.push(checkedTicketItem);
                        }
                        $.localCache.set($.offLineCheckedTicketKey, cacheData);
                        $this.checkedTicketCallback(id, checkTime);
                        return false;
                    }
                    st.loading = true;
                    var url = isBusTicket ?  $.httpProtocol.CHECK_BUS_TICKET : $.httpProtocol.CHECK_SHUTTLE_TICKET;
                    $.wpost(url,params,function (data) {
                        $this.checkedTicketCallback(id, data.time);
                        st.loading = false;
                    },function () {
                        st.loading = false;
                    },true);
                    
                }
            });

        });

        /**
         * 获取当前车票信息
         * @param id
         * @returns {{}}
         */
        $this.getTicketInfoById = function(id){
            var len = st.ticketList.length;
            var ticket = {};
            if (len){
                for (var i=0; i<len; i++) {
                    var item = st.ticketList[i];
                    if (item.ticket_id == id) {
                        ticket = id;
                        break;
                    }
                }
            }
            return ticket;
        };

    /**
     * 初始化车票html
     */
      $this.initTicketHtml = function () {
          var len = st.ticketList.length;
          if (len) {
              var html = '';
             for (var i=0; i<len; i++) {
                 var item = st.ticketList[i];
                 var ticket_id = item.ticket_id;
                 if (item.ticket_type == $.ticketType.Bus) {
                     var line_code = item.line_code;
                     var line_name = item.line_name;
                     var dept_date = item.dept_date;
                     var dept_at_str = item.dept_at_str;
                     var price = item.price;
                     var plate = item.plate;
                     var seat = item.seat;

                     html += st.ticketTpl.format(ticket_id,line_code,line_name, dept_date, dept_at_str, price, plate, seat);
                 } else {
                     var title = item.title;
                     var time = item.dept_date;
                     var desc = item.desc ? item.desc : '&nbsp;';
                     var operationTime = item.shuttle_line.business_hour;
                     html += st.shuttleTicketTpl.format(ticket_id,title,time,operationTime, desc);
                 }

             }
             $(st.ticketListSecNode).html(html);
          }

      };

        /**
         * 更新车票状态
         */
      $this.updateTicketStat = function () {
          var len = st.ticketList.length;
          if (len){
              var currentTime = new Date().getTime()/1000;
              for (var i=0; i<len; i++) {
                  var item = st.ticketList[i];
                  var id = item.ticket_id;
                  var aheadSeconds = parseInt(item.show_color_ahead_in_seconds);
                  var aheadTime = parseInt(item.dept_at)-aheadSeconds;
                  var destTime = parseInt(item.dest_at);
                  var checkTime = parseInt(item.check_time);
                  var ticketColor = item.ticket_color;
                  var target = $(st.itemPrefix+id);
                  var status = item.use_status;
                  var targetBtn = target.find(st.checkBtnNode);
                  //验票按钮 处理
                  var filterStatus = [$.ticketStatus.WaitRemark,$.ticketStatus.Finished];
                  if (filterStatus.indexOf(status) !==-1) {
                      $this.ticketStep(target,2 ,ticketColor, checkTime);
                      continue;
                  }
                  if (currentTime<=aheadTime) {//验票之前
                      $this.ticketStep(target,0 ,ticketColor, aheadTime);
                  } else if(currentTime>aheadTime && currentTime<destTime) { //开始验票
                      $this.ticketStep(target,1 ,ticketColor);
                  } else {//结束验票 已过期
                      $this.ticketStep(target,3 ,ticketColor);
                  }
              }
          }

      };

        /**
         * 车票状态轮询
         */
      $this.initTimer = function () {
          if (st.timer) {
                clearInterval(st.timer);
          }
          $this.updateTicketStat();
          st.timer = setInterval($this.updateTicketStat, 1000);
      };

        /**
         * 点击出事车票
         */
        $(document).on('click', st.currentTarget ,function (e) {
            e.stopPropagation();
            var ticketInfo = $(this).data('info');
            if ($(this).hasClass('disabled')) {
                return false;
            }
            if (ticketInfo) {
                var ticketId = ticketInfo.ticket_id;
                if (ticketId !== st.uniqueId) {
                    st.ticketList = [];
                    st.uniqueId = ticketInfo.ticket_id;
                    st.ticketList.push(ticketInfo);
                    $this.initTicketHtml();
                }
                $this.initTimer();
                st.modal.addClass('active');
            }

        });

      return $this;
    };

})($);


