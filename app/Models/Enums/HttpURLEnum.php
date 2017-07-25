<?php

namespace App\Models\Enums;


class HttpURLEnum
{
    const Prefix                            = '/v3';

    /**
     * 用户相关
     */
    const User_Verify_code                  = self::Prefix.'/auth/verify_code';
    const User_Register                     = self::Prefix.'/api/auth/register';
    const User_Login_Code                   = self::Prefix.'/auth/access_token/verify_code';
    const User_Logout                       = self::Prefix.'/logout';
    const User_Login_PSW                    = self::Prefix.'/auth/login';
    const User_Reset_PSW                    = self::Prefix.'/auth/forget_password';
    const User_Edit_PSW                     = self::Prefix.'/users/modifypassword';
    const User_Profile                      = self::Prefix.'/profile/get_profile';
    const User_Update_Profile               = self::Prefix.'/profile/set_profile';
    const User_Summary                      = self::Prefix.'/account/summary';
    const User_Cash_Bill_List               = self::Prefix.'/account/cash/bills';
    const User_Bonus_List                   = self::Prefix.'/account/bonus_list';
    const User_Bonus_Detail                 = self::Prefix.'/account/bonus_package';
    const User_Coupons_List                 = self::Prefix.'/pay/get_coupons';
    const User_Exchange_Coupon_Code         = self::Prefix.'/pay/coupon_code';
    const User_Feedback                     = self::Prefix.'/account/complaint';

    /**
     * 支付相关
     */
    const Create_Contract_Multi             = self::Prefix.'/pay/create_contract_multi';
    const Pay_Contract                      = self::Prefix.'/pay/pay_contract';
    const Pay_Notify                        = self::Prefix.'/pay/wxpay_finish';
    const Cancel_Contract                   = self::Prefix.'/contracts/cancel_contract';

    const Create_Shuttle_Contract           = self::Prefix.'/pay/create_contract_shuttle';
    const Get_Paid_Bus_Ticket               = self::Prefix.'/pay/get_paid_bus_ticket';
    const Get_Paid_Shuttle_Ticket           = self::Prefix.'/pay/get_paid_shuttle_ticket';


    /**
     * 班车相关
     */
    const Bus_Line_List                     = self::Prefix.'/buses/bus_lines_page';
    const Bus_Line_Detail                   = self::Prefix.'/buses/pre_order_multi';
    const Ticket_List_With_Date             = self::Prefix.'/contracts/get_tickets_multi';
    const Ticket_Available_List             = self::Prefix.'/contracts/get_available_tickets';
    const Ticket_Month_Map                  = self::Prefix.'/contracts/get_tickets_map';
    const Ticket_Detail                     = self::Prefix.'/pay/get_ticket_detail';
    const Check_Bus_Ticket                  = self::Prefix.'/bus/check_bus_ticket';
    const Quick_Show_Ticket                 = self::Prefix.'/contracts/quick_show_ticket';
    const Remark_Bus                        = self::Prefix.'/contracts/review_contract';
    const Refund                            = self::Prefix.'/contracts/refund_contract_ticket';
    const Line_Real_Loc                     = self::Prefix.'/buses/line_real_loc';
    const Line_Search                       = self::Prefix.'/buses/search_line';
    const Get_All_Line                      = self::Prefix.'/buses/get_all_line';
    /**
     * 锁座相关
     */
    const Seats_Status_Day                  = self::Prefix.'/buses/seats_status_for_day';
    const Seats_Status_Month                = self::Prefix.'/buses/seats_status_for_month';
    const Lock_Seats_By_Day                 = self::Prefix.'/buses/lock_seats_for_day';
    const Lock_Seats_By_Month               = self::Prefix.'/buses/lock_seats_for_month';
    const UnLock_Seats_By_Day               = self::Prefix.'/buses/unlock_seats_for_day';
    const UnLock_Seats_By_Month             = self::Prefix.'/buses/unlock_seats_for_month';


    /**
     * 快捷巴士相关
     */
    const Shuttle_Line_List                 = self::Prefix.'/shuttle/shuttle_lines_page';
    const Shuttle_Line_Position             = self::Prefix.'/shuttle/real_time_position';
    const Shuttle_Refund                    = self::Prefix.'/shuttle/refund_shuttle_ticket';
    const Check_Shuttle_Ticket              = self::Prefix.'/shuttle/check_shuttle_ticket';
    const Shuttle_Ticket_List               = self::Prefix.'/shuttle/get_shuttle_tickets';
    const Shuttle_Ticket_Detail             = self::Prefix.'/shuttle/get_shuttle_ticket_detail';


    /**
     * 旅游相关
     */
    

    /**
     * 其他
     */
    const Config                            = self::Prefix.'/other/config';

}
