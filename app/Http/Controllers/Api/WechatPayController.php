<?php

namespace App\Http\Controllers\Api;

use App\Helper\Util;
use App\Models\Enums\SettingEnum;
use App\Models\Enums\WXOrderStatusEnum;
use App\Models\Enums\WXPayStatusEnum;
use App\Models\PayInfo;
use App\Models\WechatUser;
use App\Repositories\WechatPayRepositories;
use App\Repositories\ProductInfoRepositories;
use Doctrine\Common\Cache\PredisCache;
use EasyWeChat\Payment\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class WechatPayController extends Controller
{


    public $wechat;
    public $openId;
    public $payment;

    public function __construct()
    {
        parent::__construct();
        $predis = app('redis')->connection();// connection($name), $name 默认为 `default`
        $cacheDriver = new PredisCache($predis);
        $this->wechat = app('wechat');
        $this->wechat->driver = $cacheDriver;
        $this->payment = $this->wechat->payment;
    }


    /**
     * 扫码回调
     * @param Request $request
     */
    public function native(Request $request)
    {
        $response = $this->payment->handleScanNotify(function($productId, $openId ,$notify){
//            Log::info('$notify');
//            Log::info(\GuzzleHttp\json_encode($notify));
            //{"appid":"wx9f0f2c946e2c3f1e","openid":"orwAGsxDRbOTQP0_WtG5iYIje5Zs","mch_id":"1244914102","is_subscribe":"Y","nonce_str":"zVGvDodRoGNc4tW8","product_id":"1","sign":"BF094E22D4298B6866A60E4FB21DC849"}

            $productInfo = ProductInfoRepositories::getProductInfoById($productId);
            $outTradeNo = WechatPayRepositories::getMchBillNumber();
            $attributes = [
                'trade_type'       => Order::NATIVE, // JSAPI，NATIVE，APP...
                'body'             => $productInfo->description,
                'detail'           => $productInfo->content,
                'out_trade_no'     => $outTradeNo,
                'product_id'       => $productId,
                'total_fee'        => $productInfo->fee, // 单位：分
                'notify_url'       => Util::getWechatPayNotifyUrl(), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
                'openid'           => $openId, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            ];
            $order = new Order($attributes);
//            Log::info('$order');
//            Log::info(\GuzzleHttp\json_encode($order));
            //{"trade_type":"NATIVE","body":"K001 \u5feb\u6377\u5df4\u58eb\u8f66\u7968","detail":"\u54c8\u7f57\u540c\u884c K001\u7ebf\u8def \u5feb\u6377\u5df4\u58eb\u8f66\u7968","out_trade_no":"1244914102201704251493115109","product_id":"1","spbill_create_ip":"140.207.54.75","total_fee":200,"notify_url":"http:\/\/wxdev.hollo.cn\/api\/pay\/notify","openid":"orwAGsxDRbOTQP0_WtG5iYIje5Zs"}
            $result = $this->payment->prepare($order);

//            Log::info('prepare order');
//            Log::info(\GuzzleHttp\json_encode($result));
            // {"return_code":"SUCCESS","return_msg":"OK","appid":"wx9f0f2c946e2c3f1e","mch_id":"1244914102","nonce_str":"s5ZAwHxaSYNYmis8","sign":"F1567990337D3BB660B1FF789F51EB9B","result_code":"SUCCESS","prepay_id":"wx20170425181153a5ac7b84ef0234444541","trade_type":"NATIVE","code_url":"weixin:\/\/wxpay\/bizpayurl?pr=om0tp5k"}
            $prepayId = '';
            if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
                $prepayId = $result->prepay_id;
            }

            $payInfo = new PayInfo();
            $payInfo->open_id = $openId;
            $payInfo->out_trade_no = $outTradeNo;
            $payInfo->contract_id = $productInfo->code;
            $payInfo->prepay_id = $prepayId;
            $payInfo->product_id = $productId;
            $payInfo->ip = $order->spbill_create_ip;
            $payInfo->fee = $productInfo->fee;
            $payInfo->trade_state = WXPayStatusEnum::Unpaid;
            $payInfo->trade_type = $order->trade_type;
            $payInfo->order_type = WXOrderStatusEnum::QrCode;
            $payInfo->save();
            return $prepayId;

        });
        return $response;
    }

    /**
     * 扫码支付回调
     * @param Request $request
     */
    public function notify(Request $request)
    {
        $response = $this->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
//            $order = 查询订单($notify->out_trade_no);
//            Log::info('handleNotify');
//            Log::info(\GuzzleHttp\json_encode($notify));
            // {"appid":"wx9f0f2c946e2c3f1e","bank_type":"ICBC_DEBIT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1244914102","nonce_str":"58ff22574c4e5","openid":"orwAGsxDRbOTQP0_WtG5iYIje5Zs","out_trade_no":"1244914102201704251493115479","result_code":"SUCCESS","return_code":"SUCCESS","sign":"424008F3ADA3C9034F0EA55276BAF553","time_end":"20170425181814","total_fee":"1","trade_type":"NATIVE","transaction_id":"4002602001201704258320006577"}

            $outTradeNo = $notify->out_trade_no;
            $payInfo = PayInfo::where('out_trade_no', $outTradeNo)
                ->first();
            $productInfo = ProductInfoRepositories::getProductInfoById($payInfo->product_id);
            if ($successful) {
                $payInfo->trade_state = WXPayStatusEnum::Paid;
                $title = sprintf('恭喜你成功购买 %s 车票', $productInfo->code);
                $fee=sprintf('%.2f元',$notify->total_fee/100);
                $name = $productInfo->description;
                $detail = '祝您乘车愉快！';
                $this->sendSuccessTplMsg($notify->openid, $title, $fee, $name, $detail);
                $msg = 'OK';
            } else { // 用户支付失败
                $payInfo->trade_state = WXPayStatusEnum::Failed;
                $msg = 'FAILED';
            }
            $payInfo->bank_type = $notify->bank_type;
            $payInfo->time_end = $notify->time_end;
            $payInfo->transaction_id = $notify->transaction_id;
            $payInfo->return_code = $notify->result_code;
            $payInfo->return_msg = $msg;
            $payInfo->save();

            return true; // 返回处理完成
        });
        return $response;
    }

    /**
     * 发送支付成功模板消息
     * @param $openId
     * @param $title
     * @param $fee
     * @param $name
     * @param string $detail
     * @param string $url
     */
    private function sendSuccessTplMsg($openId, $title, $fee, $name,  $detail = '', $url = '')
    {
        $notice = $this->wechat->notice;
//        $templateId = 'SlkTsBCelqM3jnnD-q7MQ7OwFfXsTLl1NS6OB-s-UIE';//正式
//        $templateId = 'mY8FHQFIzkPKHaBlGfN_4mG-qEBe2zZZupOm943tgio';
//        $templateId = 'QShQGHhHUm8nLWpZlrKvAFS1FhbYUUrJ3L849OQULho';//智享
        $color = '#FF0000';
        $templateId = SettingEnum::transform(SettingEnum::Pay_Success_Msg_Template_id);
        $data = array(
            "first"             => $title,
            "orderMoneySum"     => $fee,
            "orderProductName"  => $name,
            "Remark"            => $detail,
        );
        if (!empty($url)) {
            $notice->uses($templateId)->andData($data)->andReceiver($openId)->send();
        } else {
            $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        }
    }

    /***
     * 生成QRCode Url
     * @param $productId
     * @return string
     */
    public function createPayQRcodeUrl($productId)
    {
        $payment = $this->payment;
        $url = $payment->scheme($productId);
        $shortUrl = '';
        $res = $payment->urlShorten($url);
        if (strtolower($res['return_code']) == 'success') {
            $shortUrl = $res['short_url'];
        }
        return $shortUrl;
    }
    
}
