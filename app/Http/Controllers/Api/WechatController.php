<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use App\Models\Enums\WechatClickEnum;
use App\Models\Enums\WechatMenuEnum;
use App\Models\DataCompetitor;
use App\Models\WechatAutoReply;
use App\Models\WechatUser;
use App\Repositories\WechatAutoReplyRepositories;
use Doctrine\Common\Cache\PredisCache;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Transfer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Ixudra\Curl\Facades\Curl;

class WechatController extends Controller
{

    public $wechat;
    public $openId;

    public function __construct()
    {
        parent::__construct();
        $predis = app('redis')->connection();// connection($name), $name 默认为 `default`
        $cacheDriver = new PredisCache($predis);
        $this->wechat = app('wechat');
        $this->wechat->driver = $cacheDriver;
    }

    /**
     *获取js sdk 配置
     * @return mixed
     */
    public function getJsSdk()
    {
        $js = $this->wechat->js;
        $jsApiList = [
            'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone',//分享
            'previewImage',
            'openLocation', 'getLocation',
            'hideOptionMenu', 'showOptionMenu', 'hideMenuItems', 'showMenuItems', 'hideAllNonBaseMenuItem', 'showAllNonBaseMenuItem',
            'closeWindow', 'scanQRCode',
            'chooseWXPay',
        ];
        $debug = false;
        $result = $js->config($jsApiList, $debug);
        return $result;
    }

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
        $server = $this->wechat->server;
        $staff = $this->wechat->staff;

        try {
            $message = (object)$server->getMessage();
            $openId = $message->FromUserName; // 用户的 openid
            $msgType = $message->MsgType; // 消息类型：event, text....
            Log::info(\GuzzleHttp\json_encode($message));
            $accessToken = $this->wechat->access_token;
            $token = $accessToken->getToken();
            Log::info('decode token == '.$token);

            $msg = '';
            switch ($msgType) {
                case 'event':
                    # 事件消息...
                    $msg = $this->handleEvent($message);
                    break;
                case 'text':
                    # 文字消息...
                    $key = trim($message->Content);//  文本消息内容
                    $msg = $this->getAutoReplyContent($key);
                    break;
                case 'image':
                    # 图片消息...
                    $picUrl = $message->PicUrl;//  图片链接
                    break;
                case 'voice':
                    # 语音消息...
                    $mediaId = $message->MediaId;        //语音消息媒体id，可以调用多媒体文件下载接口拉取数据。
                    $format = $message->Format;         //语音格式，如 amr，speex 等
                    $message->Recognition; //* 开通语音识别后才有
                    // 请注意，开通语音识别后，用户每次发送语音给公众号时，微信会在推送的语音消息XML数据包中，增加一个 `Recongnition` 字段
                    break;
                case 'video':
                    # 视频消息...
                    $mediaId = $message->MediaId;;       //视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
                    $thumbMediaId = $message->ThumbMediaId;  //视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
                    break;
                case 'shortvideo':
                    # 小视频消息...
                    $mediaId = $message->MediaId;;       //视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
                    $thumbMediaId = $message->ThumbMediaId;  //视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
                    break;
                case 'location':
                    # 坐标消息...
                    $message->MsgType ;//    location
                    $message->Location_X;//  地理位置纬度
                    $message->Location_Y;//  地理位置经度
                    $message->Scale;//       地图缩放大小
                    $message->Label;//       地理位置信息
                    break;
                case 'link':
                    # 链接消息...
                    $message->Title;//        消息标题
                    $message->Description;//  消息描述
                    $message->Url;//          消息链接
                    break;
                // ... 其它消息
                default:
                    # code...
                    break;
            }
            if (!empty($msg)) {
                $staff->message($msg)->to($openId)->send();
            }

            $server->setMessageHandler(function($message){
                $openId = $message->FromUserName; // 用户的 openid
                $this->openId = $openId;
                $msgType = $message->MsgType; // 消息类型：event, text....
                $createTime = $message->CreateTime;//    消息创建时间（时间戳）
                $msgId = $message->MsgId;//         消息 ID（64位整型）
                $transfer  = new Transfer();
                return $transfer;

            });

            $response = $server->serve();
            return $response->send();
        } catch (\Exception $e) {
            Log::info('return error.');
        }

        Log::info('return response.');
        return '';
    }

    /**
     * 获取自动回复内容
     * @param $key
     * @return string
     */
    public function getAutoReplyContent($key = '')
    {
        $result = '';
        $default = '';
        $maps = WechatAutoReplyRepositories::replyMsgMap();
        if ($maps) {
            $material = $this->wechat->material;
            foreach ($maps as $map) {
                $keyArr = preg_split("/(、|，|,)/", $map->key);
                if(in_array($key, $keyArr, true)){
                    switch ($map->type)
                    {
                        case 0://文字消息
                            $result = $map->content;
                            break;
                        case 1://图片消息
                            $mediaId = '';
                            if(!empty($map->media_id)) {
                                $mediaId = $map->media_id;
                            }else{
                                $absPath = realpath(sprintf('%s/../../..%s', dirname(__DIR__) ,$map->content));
                                Log::info($absPath);
                                if($absPath) {
                                    $mediaResult = $material->uploadImage($absPath);
                                    Log::info($mediaResult);
                                    if(isset($mediaResult->media_id)) {
                                        $mediaId = $mediaResult->media_id;
                                        $map->media_id = $mediaId;
                                        $map->save();
                                    }
                                }
                            }
                            if( !empty($mediaId) ) {
                                $result = new Image(['media_id' => $mediaId]);
                            }

                            break;
                        case 3://点击回复
                            $result = $map->content;
                            break;
                        default:

                            break;
                    }
                    break;
                }

                if ($map->key == WechatClickEnum::Event_Click_Key_Feedback) {
                    $default = $map->content;
                }
            }
        }
        return empty($result) ? $default : $result;
    }


    /**
     * 处理event 事件
     * @param $message
     * @return string
     */
    private function handleEvent($message)
    {
        $event = strtolower($message->Event);      // 事件类型 （如：subscribe(订阅)、unsubscribe(取消订阅) ...， CLICK 等）
        $result = '';
        switch ($event)
        {
            case 'location':
                # 上报地理位置事件
                $lat = $message->Latitude ;      //   23.137466   地理位置纬度
                $lng = $message->Longitude;      //   113.352425  地理位置经度
                $pre = $message->Precision;      //   119.385040  地理位置精度
                $this->updateLocation($this->openId, $lat, $lng, $pre);
                break;

            case 'subscribe':
                $userService = $this->wechat->user;
                if($this->openId)
                {
                    $user = $userService->get($this->openId);
                    Log::info('subscribe');
                    Log::info(json_encode($user->toArray()));
                    $this->updateWechatUser($user);
                }
                $result = $this->getAutoReplyContent(WechatClickEnum::Event_Click_Key_Subscribe);
                break;

            case 'unsubscribe':
                if($this->openId)
                {
                    $this->unSubscribeWechatUser($this->openId);
                }
                break;

            case 'scan':
                # 扫描带参数二维码事件
                $message->EventKey;      //    事件KEY值，比如：qrscene_123123，qrscene_为前缀，后面为二维码的参数值
                $message->Ticket;      //      二维码的 ticket，可用来换取二维码图
                break;

            case 'view':
                $eventKey = $message->EventKey;      //     事件KEY值，与自定义菜单接口中KEY值对应，如：CUSTOM_KEY_001, www.qq.com

                break;

            case 'click':
                # 自定义菜单事件
                $eventKey = $message->EventKey;      //     事件KEY值，与自定义菜单接口中KEY值对应，如：CUSTOM_KEY_001, www.qq.com
                $result = $this->getAutoReplyContent($eventKey);
                Log::info('handleEvent click $result:::'.\GuzzleHttp\json_encode($result));
                break;

            case 'templatesendjobfinish'://模板消息结果
//                $content = $message->Raw;      //     事件KEY值，与自定义菜单接口中KEY值对应，如：CUSTOM_KEY_001, www.qq.com
//                Log::info('handleEvent templatesendjobfinish $result:::'.$content);
                break;
        }
        return $result;
    }

    private function getHgtMenuBtn()
    {
        $host = Config::get('app')['url'];
        $buttons = [
            [
                "name"       => "智享班车",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "我的班车",
                        "url"  => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_My_Bus)

                    ],
                    [
                        "type" => "view",
                        "name" => "班车车票",
                        "url"  => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_Bus_Ticket)

                    ],
                ],
            ],
            [
                "name"       => "快捷巴士",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "买票乘车",
                        "url"  => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_My_Shuttle)

                    ],
                    [
                        "type" => "view",
                        "name" => "购票记录",
                        "url"  => sprintf('%s/api/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_Shuttle_Ticket)

                    ],
                ],
            ],
            [
                "name"       => "我的智享",
                "sub_button" => [
                    [
                        "type" => "click",
                        "name" => "常见问题",
                        "key" => WechatClickEnum::Event_Click_Key_Subscribe
                    ],
                    [
                        "type" => "view",
                        "name" => "使用帮助",
                        "url" => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_Question)
                    ],
                    [
                        "type" => "view",
                        "name" => "投诉建议",
                        "url" => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_Feedback)
                    ],
                    [
                        "type" => "view",
                        "name" => "我的帐号",
                        "url"  => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_Account)

                    ],


                ],
            ],
        ];
        return $buttons;
    }

    /**
     * 创建菜单
     */
    public function getCreateMenu()
    {
        $menu = $this->wechat->menu;
        $host = Config::get('app')['url'];
        $appName = strtolower(Config::get('app')['name']);
        $buttons = [
            [
                "name"       => "哈罗班车",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "我的班车",
                        "url"  => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_My_Bus)

                    ],
                    [
                        "type" => "view",
                        "name" => "班车车票",
                        "url"  => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_Bus_Ticket)

                    ],
                ],
            ],
            [
                "name"       => "快捷巴士",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "买票乘车",
                        "url"  => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_My_Shuttle)

                    ],
                    [
                        "type" => "view",
                        "name" => "买票记录",
                        "url"  => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_Shuttle_Ticket)

                    ],
                ],
            ],
            [
                "name"       => "我的哈罗",
                "sub_button" => [
                    [
                        "type" => "click",
                        "name" => "常见问题",
                        "key" => WechatClickEnum::Event_Click_Key_Subscribe
                    ],
                    [
                        "type" => "view",
                        "name" => "投诉建议",
                        "url" => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_Feedback)
                    ],
                    [
                        "type" => "view",
                        "name" => "哈友汇",
                        "url"  => 'https://shequ.yunzhijia.com/thirdapp/forum/network/5850bfc8e4b08f1e85407a70'
                    ],
                    [
                        "type" => "view",
                        "name" => "我的帐号",
                        "url"  => sprintf('%s/api/wechat/wechat-menu?menuid=%s', $host, WechatMenuEnum::Menu_Id_Account)
                    ],


                ],
            ],
        ];
        if ($appName == 'hgt') {
            $buttons = $this->getHgtMenuBtn();
        }

        $menu->destroy(); // 全部删除 菜单
        $result = $menu->add($buttons);//添加普通菜单
        print_r($buttons);                                                                                                         
        var_dump($result);
        return;
    }

    public function menu(Request $request)
    {
        $pattern = [
            'menuid' => 'required'
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $host = Config::get('app')['url'];
        $maintain = Config::get('app')['maintain'];
        $ip = $this->getRemoteIp();
        $ipMap = [
            '121.69.19.166',
            '123.126.109.101',
        ];
        Log::info($ip);
        $ipFilter = false;
        foreach ($ipMap as $ipv) {
            if (strpos($ip, $ipv) !== false) {
                $ipFilter = true;
            }
        }

        if (!empty($maintain) && !$ipFilter) {
            $url = sprintf('%s/maintain', $host);
            header('Location: '.$url);
            die();
        }

        $url = '';
        switch (intval($params['menuid'])) {
            case WechatMenuEnum::Menu_Id_My_Bus:
                $url = sprintf('%s', $host);
                break;

            case WechatMenuEnum::Menu_Id_Bus_Ticket:
                $url = sprintf('%s/my-order?type=0', $host);
                break;

            case WechatMenuEnum::Menu_Id_My_Shuttle:
                $url = sprintf('%s/shuttle-map', $host);
                break;

            case WechatMenuEnum::Menu_Id_Shuttle_Ticket:
                $url = sprintf('%s/my-order?type=1', $host);
                break;
            case WechatMenuEnum::Menu_Id_Account:
                $url = sprintf('%s/auth/account?t=', $host, time());
                break;
            case WechatMenuEnum::Menu_Id_Feedback:
                $url = sprintf('%s/other/feedback', $host);
                break;
            case WechatMenuEnum::Menu_Id_Question:
                $url = 'http://m.hollo.cn/v3/hgt/usage.html';
                break;
        }
        header('Location: '.$url);
        die();
    }


    /**
     * 获取 accessToken 接口
     */
    public function getAccessToken()
    {

        $accessToken = $this->wechat->access_token;
        $status = Input::get('status') == 'refresh' ? true : false;
        $token = $accessToken->getToken($status);
        if ($token) {
            $response = Curl::to('https://api.weixin.qq.com/cgi-bin/getcallbackip')
                ->withData( array( 'access_token' => $token ) )
                ->asJson( true )
                ->get();

            Log::info('$response    == '.\GuzzleHttp\json_encode($response));
            $codeArr = [40001,40014,41001,42001];
            if(isset($response['errcode']) && in_array($response['errcode'],$codeArr)){
                $token = $accessToken->getToken(true);
                Log::info('token expire new tocken == '.$token);
            }

            Log::info('decode token == '.$token);
            $data['token'] = Crypt::encrypt($token);

            return response()->json((new ApiResult(0, ErrorEnum::Success, $data))->toJson());
        } else {
            return response()->json((new ApiResult(-1, ErrorEnum::Failed, []))->toJson());
        }
    }

    /**
     * 更新地理位置
     * @param $openId
     * @param $lat
     * @param $lng
     * @param $pre
     */
    private function updateLocation($openId, $lat, $lng, $pre)
    {
        $data = [
            'open_id'=>$openId,
            'lat'=>$lat,
            'lng'=>$lng,
            'pre'=>$pre,
        ];
        DataCompetitor::updateOrCreate(['open_id'=>$openId],$data);
    }

    /**
     * 更新微信用户信息
     * @param $user
     */
    private function updateWechatUser($user)
    {
        $openId =  $user['openid'];
        $data = [
            'open_id'=>$openId,
            'name'=>$user['nickname'],
            'nickname'=>$user['nickname'],
            'sex'=>$user['sex'],
            'avatar'=>$user['headimgurl'],
            'province'=>$user['province'],
            'country'=>$user['country'],
            'city'=>$user['city'],
            'status'=>$user['subscribe']//关注状态
        ];
        WechatUser::updateOrCreate(['open_id'=>$openId],$data);
    }


    /**
     * 取消关注
     * @param $openId
     */
    private function unSubscribeWechatUser($openId)
    {
        $user = WechatUser::where('open_id', $openId)
                    ->first();
        if ($user) {
            $user->status = 0;
            $user->save();
        }
    }

    /**
     * 测试模板消息 API
     */
    public function test()
    {
        $notice = $this->wechat->notice;

        $userId = 'orwAGs0XFajGOvuHUbImBnxH0dRk';
        $templateId = 'ftqZYNttPmgiTEk_pDl1-I1GJBYz4_A3kDZEqEtC9mo';
        $url = 'http://www.baidu.me';
        $color = '#FF0000';
        $data = array(
            "first"  => "您好，您已预约租车成功。",
            "carType"   => "SUV",
            "name"  => "机场巴士",
            "tel" => "18500227320",
            "expDate" => "2016年10月10日",
            "remark" => "点击查看详情",
        );
        $result = $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($userId)->send();
        var_dump($result);
    }
}
