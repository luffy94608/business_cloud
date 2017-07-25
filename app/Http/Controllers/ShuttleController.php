<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BusApi;
use App\Http\Controllers\Api\PayApi;
use App\Http\Controllers\Api\ShuttleApi;
use App\Http\Requests;
use App\Models\Enums\ErrorEnum;
use App\Models\Enums\ShuttleTicketStatusEnum;
use App\Models\Enums\TicketTypeEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class ShuttleController extends Controller
{

    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**                                       m
     * 线路列表
     *
     * @return \Illuminate\Http\Response
     */
    public function shuttleList()
    {
        $params = [
            'page' =>'page-shuttle-list',
        ];
        return View::make('shuttle.shuttle_list',$params);
    }

    /**
     * 车票详情
     * @param $id
     * @return mixed
     */
    public function ticketDetail($id)
    {
        $ticket = [];
        $result = ShuttleApi::getTicketDetail($id);
        if (isset($result['code']) && $result['code']===0) {
            $ticket = $result['data'];
        }
        $params = [
            'ticket'=>$ticket,
            'page' =>'page-ticket-detail',
        ];
        return View::make('shuttle.ticket_detail',$params);
    }


    /**
     * 地图列表
     *
     * @return \Illuminate\Http\Response
     */
    public function shuttleMap()
    {
        $result = BusApi::quickShowTicket(TicketTypeEnum::Shuttle);
        $tickets = [];
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $tickets = $res[TicketTypeEnum::transformListKey(TicketTypeEnum::Shuttle)];
        }

        $list = [];
        $result = ShuttleApi::LinesList();
        if (isset($result['code']) && $result['code'] === 0) {
            $res = $result['data'];
            $list = $res['lines'];
        }
        $params = [
            'ticketCount'=>count($tickets),
            'list'=>$list,
            'page' =>'page-shuttle-map',
        ];
        return View::make('shuttle.shuttle_map',$params);
    }

    /**
     * 线路详情
     * @param $lineId
     * @return mixed
     */
    public function payShuttle($lineId)
    {
        if ($lineId == -1){
            $lineId = null;
        }
        $result = PayApi::createShuttleContract($lineId);
        if (isset($result['code']) && $result['code']===0) {
            $data = $result['data'];
        } else {
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
        $lineCode = Input::get('code');
        $params = [
            'lineCode'=>$lineCode?:'快捷巴士车票',
            'contract'=>$data,
            'page' =>'page-pay-shuttle',
        ];
        return View::make('shuttle.pay_shuttle',$params);
    }

}
