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
        UPDATE_PROFILE                  : '/api/user/update',
        RESET                           : '/api/user/reset',
        USER_COMPANY                    : '/api/user/company',
        USER_BUSINESS                   : '/api/user/business',
        GET_BID_LIST                    : '/api/get-bid-list',
        GET_WINNER_LIST                 : '/api/get-winner-list',
        GET_COMPETITOR_LIST             : '/api/get-competitor-list',
        SEARCH_LIST                     : '/api/search-list',
        ERROR_TRACK                     : '/api/other/track-error',
        TEST : ''
    };
})($);


