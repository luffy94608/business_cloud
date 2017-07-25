(function ($) {

    $.httpProtocol =  {
        // === info ===
        version: "1.0.0",
        auth: "web",
        // === common ===
        GET_VERIFY_CODE                 : '/api/get-verify-code',
        GET_PROFILE_INFO                : '/api/profile',
        LOGIN                           : '/api/user/login',
        LOGOUT                          : '/api/user/logout',
        REGISTER                        : '/api/user/register',
        RESET                           : '/api/user/reset',
        EDIT_PSW                        : '/api/user/edit',
        FEEDBACK                        : '/api/user/feedback',
        GET_PROFILE                     : '/api/user/get-profile',
        UPDATE_PROFILE                  : '/api/user/update',
        GET_BILL_LIST                   : '/api/get-bill-list',
        GET_BONUS_LIST                  : '/api/get-bonus-list',
        GET_BONUS_DETAIL                : '/api/get-bonus-detail',
        GET_COUPON_LIST                 : '/api/get-coupon-list',
        GET_BUS_LIST                    : '/api/bus/get-bus-list',
        EXCHANGE_SHARE_CODE             : '/api/exchange-share-code',
        ERROR_TRACK                     : '/api/other/track-error',

        CREATE_ORDER                    : '/api/pay/create-order',
        GET_PAID_BUS_TICKET             : '/api/pay/get-paid-bus-ticket',
        GET_PAID_SHUTTLE_TICKET         : '/api/pay/get-paid-shuttle-ticket',
        PAY                             : '/api/pay/pay-order',
        CANCEL_ORDER                    : '/api/pay/cancel-order',
        PAY_NOTIFY                      : '/api/pay/notify',
        GET_TICKET_LIST_BY_DATE         : '/api/bus/get-date-ticket-list',
        GET_TICKET_MONTH_MAP            : '/api/bus/ticket-month-map',
        REFUND_BUS_TICKET               : '/api/bus/refund',
        BUS_REMARK                      : '/api/bus/remark',
        CHECK_BUS_TICKET                : '/api/bus/check-ticket',
        CHECK_OFF_LINE_TICKET           : '/api/bus/check-off-line-ticket',
        GET_ALL_LINE                    : '/api/bus/get-all-line',
        BUS_REAL_POSITION               : '/api/bus/bus-real-position',
        QUICK_SHOW_TICKET               : '/api/bus/quick-show-ticket',
        STATUS_BY_DAY                   : '/api/seat/status-by-day',
        STATUS_BY_Month                 : '/api/seat/status-by-month',
        LOCK_OR_UNLOCK_BY_DAY           : '/api/seat/lock-or-unlock-by-day',
        LOCK_OR_UNLOCK_BY_Month         : '/api/seat/lock-or-unlock-by-month',
        SHUTTLE_LIST                    : '/api/shuttle/shuttle-list',
        PAY_SHUTTLE                     : '/api/shuttle/pay-shuttle',
        CHECK_SHUTTLE_TICKET            : '/api/shuttle/check-ticket',
        REFUND_SHUTTLE_TICKET           : '/api/shuttle/refund',
        LOTTERY_DRAW                    : '/api/activity/lottery-draw',

        SHUTTLE_REAL_POSITION           : '/api/shuttle/shuttle-real-position',
        TEST : ''
    };
})($);


