(function ($) {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };

    /**
     * 刚购买的快捷巴士
     * @type {string}
     */
    $.cacheLastShuttleTicketKey = 'js_last_bus_shuttle_ticket_key';

    /**
     * 定位 location
     * @type {string}
     */
    $.cacheLocationKey = 'js_location_cache_key';

    /**
     * 最后一次锁座key
     * @type {string}
     */
    $.lastLockSeatParamsKey = 'js_last_lock_seat_params_key';

    /**
     * 是否弹出票缓存的数据key
     * @type {string}
     */
    $.ticketListIsShowTicket = 'js_ticket_is_show_ticket_key';

    /**
     * 离线验票数据
     * @type {string}
     */
    $.offLineCheckedTicketKey = 'js_off_line_checked_ticket_key';

    /**
     * 红包缓存的数据key
     * @type {string}
     */
    $.bonusKey = 'red_packet_bonus_key';

    /**
     * 红包显示类型
     * @type {string}
     */
    $.bonusShowType = {
        None                     : 0,
        Show                     : 1,
        Pop                      : 2
    };

    /**
     * 座位状态
     * @type {string}
     */
    $.seatStatus = {
        Disabled                    : 0,
        Normal                      : 1,
        Checked                     : 2
    };

    /**
     * 座位状态
     * @type {string}
     */
    $.seatStatusStyle = {
        Normal                      : '',
        Disabled                    : 'out',
        Checked                     : 'active'
    };

    /**
     * 车票类型
     * @type {{Bus: number, Shuttle: number}}
     */
    $.ticketType = {
        Bus                     : 0,
        Shuttle                 : 1
    };

    /**
     * 车票种类 2 日票 月票
     * @type {{Day: string, Month: string}}
     */
    $.ticketCategory = {
        Day                     : 'day',
        Month                   : 'month'
    };

    /**
     * 班车车票状态
     * @type {{UnPaid: number, UnUsed: number, WaitRemark: number, Finished: number, Refund: number}}
     */
    $.ticketStatus = {
         UnPaid               : 1,
         UnUsed               : 2,
         WaitRemark           : 3,
         Finished             : 4,
         Refund               : 5
    };

    /**
     * 快捷巴士车票状态
     * @type {{Expired: number, UnUsed: number, Finished: number, Refund: number}}
     */
    $.shuttleTicketStatus = {
        Expired              : 0,
        UnUsed               : 1,
        Finished             : 2,
        Refund               : 3
    };

    /**
     * 快捷巴士线路状态
     * @type {{Normal: number, Closed: number}}
     */
    $.shuttleLineStatus = {
        Normal               : 0,
        Closed               : 1
    };

    /**
     * 月票状态
     * @type {{normal: number, paid: number, Full: number}}
     */
    $.monthTicketStatus = {
        normal               : 1,
        paid                 : 2,
        Full                 : 3
    };

    /**
     * 提示文字
     */
    $.string =  {
        // === info ===
        version: "1.0.0",
            auth: "web",
            // === common ===
            VERIFY_CODE_NOT_EMPTY : '请输入验证码',
            EXCHANGE_CODE_NOT_EMPTY : '请输入兑换码',
            SRC_PSW_MUST : '请输入原密码',
            SRC_NEW_PSW_MUST : '请输入新密码',
            CONFIRMED_PSW_ERROR : '两次密码不一致',
            AGREEMENT_MUST_CHECKED : '请阅读并同意注册事项',
            NAME_NOY_EMPTY : '请输入真实姓名',
            EMAIL_NOY_EMPTY : '请输入电子邮箱',
            LOCATION_NOY_EMPTY : '请输入常用地址',
            ID_CODE_NOY_EMPTY : '请输入证件号码',
            PEOPLE_NOY_EMPTY : '请输入乘车人数',
            VALUE_MUST_NUMBER : '请输入数字',
            REMARK_SUCCESS : '评价成功',
            TICKET_REFUND : '已退票',
            TICKET_REFUND_HINT : '退票后，您支付的票款将自动返回到您的哈罗账户内。',
            TICKET_REFUND_FORBIDDEN_HINT : '出发时间前{0}分钟内不能再申请退票',
            TICKET_REFUND_CONFIRM_HINT : '您确定要申请退票吗？',

            FEEDBACK_SUCCESS: '提交成功，我们会尽快处理。',
            TICKET_LINE_MUST: '请输入乘车线路！',
            TICKET_DAY_TWO_MUST: '请选择乘车日期！',
            PICK_OR_CONTENT_MUST_ONE: '请选择投诉项目或填写投诉内容！',

            TICKET_DAY_MUST: '请选择乘车日期',
            TICKET_MONTH_MUST: '请选择月票',
            TICKET_MONTH_FULL: '月票已经售罄',
            TICKET_MONTH_BUY: '您已经购买月票',
            DEPT_STATION_MUST: '请选择上车站点',
            DEST_STATION_MUST: '请选择下车站点',
            SEAT_NUMBER_MUST: '请选择座位',

            PAY_SUCCESS : '支付成功',
            WECHAT_PAY_UP_FAILED : '调起微信支付失败',
            PAY_FAILED : '支付失败',
            PAY_ERROR : '支付错误',
            ORDER_CANCEL : '订单已取消',
            COUPON_DISCOUNT : '抵扣 {0} 元',
            COUPON_NOT_USED : '不使用优惠券',
            COUPON_NOT_SUPPORT : '不支持优惠券',
            TICKET_CHECKED_BTN_TITLE:'上车验票',
            TICKET_CHECKED_TITLE:'已验票',
            TICKET_CHECKED_EXPIRED:'已过期',
            TICKET_CHECKED_FINISHED:'已完成',
            TICKET_LINE_CLOSED:'线路已结束运营',
            TICKET_CHECKED_BTN_AHEAD_TITLE:'距离验票 {0}',
            TICKET_CHECKED_BTN_NOT_TITLE:'未到乘车时间无法验票',
            TICKET_CHECKED_BTN_ACTIVE_TITLE:'验票时间：{0}',
            BUS_NOT_OPERATION:'暂未运营',
            BUS_NOT_FOUND:'当前无车辆位置',
            LOGOUT_CONFIRM_HINT:'您确定要退出当前账户吗？',
            TICKET_CHECKED_HINT:'您确定要验票吗？',
            STATION_PIC_EMPTY:'当前站点无实景图信息',
            PAY_DISCOUNT:'抵扣 {0} 元',
            PAY_WECHAT_DISCOUNT:'{0} 元',
            PAY_NOT_USE:'不使用',
            PAY_NOT_AVAILABLE:'无可用',
            TICKET_COUNT_MUST:'请选择车票数量',
            TICKET_EMPTY:'无可用车票',
            PLEASE_BUS_SCORE:'请评价车内环境',
            PLEASE_DRIVER_SCORE:'请评价着装礼仪',
            PLEASE_SCORE:'请评价文明行车',
            REMARK_CONTENT_LIMIT:'评价文字超出限制',
            PLEASE_GET_VERIFY_CODE:'验证码已发送，请查收',
            PLEASE_CHECKED_USER_POLICY:'请阅读并同意用户协议及隐私政策',
            PLEASE_CHECKED_LINE:'请选择要乘坐的线路',
            LINE_CAN_NOT_BUY:'线路未开始运营',
            LINE_SHARE_TITLE:'我乘坐这趟 “{0}”{1}上下班。你也快来看看有没有合适的线路吧！',
            LINE_SHARE_URL:'{0}/share/line/{1}',

            SUBMIT_SUCCESS : '提交成功',
            EDIT_SUCCESS : '修改成功',
            PLEASE_CHECK_NETWORK : '请检查网络连接',
            SUCCESS : '操作成功',
            ERROR : '操作失败'
    };
})($);


