<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BusApi;
use App\Http\Requests;
use App\Models\Enums\ErrorEnum;
use App\Repositories\BusRepositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class BusController extends Controller
{

    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**                                       
     * 线路列表
     *
     * @return \Illuminate\Http\Response
     */
    public function lines()
    {
        $type = Input::get('type');
        if ($type === null) {
            $now = Carbon::now();

            $type = $now->hour>9  && $now->hour<21 ? 1 : 0;
            return redirect('/?type='.$type);
        }
        $params = [
            'type' =>$type,
            'page' =>'page-lines',
        ];
        return View::make('bus.lines',$params);
    }

    /**
     * 车票列表
     *
     * @return \Illuminate\Http\Response
     */
    public function myOrder()
    {
        $params = [
            'page' =>'page-my-order',
        ];
        \Cache::forget('hollo_config');
        return View::make('bus.order',$params);
    }

    /**
     * 车票详情
     * @param $id
     * @return mixed
     */
    public function orderDetail($id)
    {
        $ticket = [];
        $result = BusApi::getTicketDetail($id);
        if (isset($result['code']) && $result['code']===0) {
            $ticket = $result['data'];
        }
        $params = [
            'ticket'=>$ticket,
            'page' =>'page-order-detail',
        ];
        return View::make('bus.order_detail',$params);
    }

    /**
     * 评价
     * @param $id
     * @return mixed
     */
    public function remark($id)
    {
        $ticket = [];
        $result = BusApi::getTicketDetail($id);
        if (isset($result['code']) && $result['code']===0) {
            $ticket = $result['data'];
        }
        $config = \Cache::get('hollo_config');
        $commentItems = [];
        if (isset($config['comment_items'])) {
            $commentItems = $config['comment_items'];
        }
        $params = [
            'commentItems'=>$commentItems,
            'ticket'=>$ticket,
            'page' =>'page-remark',
        ];
        return View::make('bus.remark',$params);
    }

    /**
     * 站点详情
     * @param $id
     * @return mixed
     */
    public function map($id)
    {
        $line = '';
        $stations = [];
        $result = BusApi::getBusDetail($id);
        if (isset($result['code']) && $result['code']===0) {
            $info = $result['data'];
            $line = $info['line'];
            $stations = array_merge($info['departure']?:[], $info['destination']?:[]);
        }

        $params = [
            'line' =>$line,
            'stations'=>$stations,
            'page' =>'page-bus-map',
        ];
        return View::make('bus.map',$params);
    }

    /**
     * 车辆位置
     * @param $id
     * @return mixed
     */
    public function location($id)
    {
        $line = '';
        $stations = [];
        $result = BusApi::getBusDetail($id);
        if (isset($result['code']) && $result['code']===0) {
            $info = $result['data'];
            $line = $info['line'];
            $stations = array_merge($info['departure']?:[], $info['destination']?:[]);
        }

        $params = [
            'line' =>$line,
            'stations'=>$stations,
            'page' =>'page-bus-location',
        ];
        return View::make('bus.location',$params);
    }


    /**
     * 线路详情
     * @param $lineId
     * @return mixed
     */
    public function pay($lineId)
    {
        $line = '';
        $stations = [];
        $shifts = [];
        $defaultShift = [];
        $monthlyPriceRule = '';
        $monthlySchedule = '';
        $paidTicketCount = '';
        $lineSchedules = [];
        $lineFrequency = [];
        $apply_monthly_price_rule = 0;
        $result = BusApi::getBusDetail($lineId);
        if (isset($result['code']) && $result['code']===0) {
            $info = $result['data'];
            $apply_monthly_price_rule = $info['apply_monthly_price_rule'];
            $line = $info['line'];
            $paidTicketCount = $info['paid_ticket_count'];
            $monthlyPriceRule = isset($info['monthly_price_rule']) ? $info['monthly_price_rule'] : '';
            $monthlySchedule = isset($info['monthly_schedule']) ? $info['monthly_schedule'] : '';
            $lineSchedules = isset($line['line_schedules']) ? $line['line_schedules'] : [];
            $lineFrequency = isset($line['line_frequency']) ? $line['line_frequency'] : [];
//            $stations = array_merge($info['departure']?:[], $info['destination']?:[]);
            $stations = BusRepositories::mergeStationWithType($info['departure']?:[], $info['destination']?:[]);

        } else {
            //未登录重新登录
            if (isset($result['code']) && $result['code']=== ErrorEnum::InvalidToken) {
                $loginUrl = '/auth/login';
                $refer = sprintf('%s%s', Config::get('app')['url'], $_SERVER['REQUEST_URI']);
                $url = sprintf('%s?callback=%s', $loginUrl, urlencode($refer));
                return redirect($url);
            } else {
                $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
                return redirect('/error?title='.$desc);
            }
        }



        $monthlyDesc = BusRepositories::getMonthlyDiscountDesc($monthlySchedule, $monthlyPriceRule, $paidTicketCount);
        $type = 3;
        if (empty($lineSchedules) && !empty($monthlySchedule) ) {
            $type = 1;
        } elseif (!empty($lineSchedules) && empty($monthlySchedule)) {
            $type = 2;
        } elseif (empty($monthlySchedule) && empty($lineSchedules)) {
            $type = 0;
        }
        $shifts = BusRepositories::toShiftGroupData($lineSchedules);
        $shiftMap = BusRepositories::getShiftMap($lineFrequency);
        $defaultShift = BusRepositories::getRecentlyCanBuyShift($lineSchedules);
        $params = [
            'line' =>$line,
            'apply_monthly_price_rule' =>$apply_monthly_price_rule,
            'type' =>$type,// 0 没有月票和日票 1 只有月票 2只有日票 3月票日票都有
            'stations' =>$stations,
            'shifts' =>$shifts,
            'defaultShift' =>$defaultShift,
            'shiftMap' =>$shiftMap,
            'stationSelect' =>$stations,
            'paidTicketCount' =>$paidTicketCount,
            'monthlyPriceRule' =>$monthlyPriceRule,
            'monthlySchedule' =>$monthlySchedule,
            'monthlyDesc' =>$monthlyDesc,
            'page' =>'page-pay',
        ];
        return View::make('bus.pay',$params);
    }

    /**
     * 站点详情
     * @return mixed
     */
    public function seat()
    {
        $params = [
            'page' =>'seat',
        ];
        return View::make('templates.seat',$params);
    }

}
