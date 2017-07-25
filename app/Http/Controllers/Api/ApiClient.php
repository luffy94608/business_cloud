<?php

namespace App\Http\Controllers\Api;


use App\Helper\Util;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ApiResult;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Ixudra\Curl\Facades\Curl;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ApiClient
{
    /**
     * @var ApiClient
     * */
    private static $_instance;

    private static $host = '';

    private static $debugPath = '';

    public  $token = '';
    public  $uid = '';


    public function __construct()
    {
        self::$debugPath = storage_path('logs/http-server.log');
        self::$host = Config::get('app')['platform'];

        //TODO 修改uid 和token 获取方式

        $this->uid = Util::getUid();
        $this->token = Util::getUserToken();

    }

    public static function singleton()
    {
        if (!isset(self::$_instance)) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }
        return self::$_instance;
    }

    /**
     * log记录
     * @param $msg
     * @param array $context
     */
    public function serverHttpLog($msg, $context = [])
    {
        $handle = new RotatingFileHandler(self::$debugPath);
        $logger = new Logger('http_server');
        $handle->setFormatter(new \Monolog\Formatter\LineFormatter(null, null, true));
        $logger->pushHandler($handle);
        $logger->info($msg, $context);
    }

    /**
     * @param $url
     * @param $method
     * @param $params
     * @param $json
     * @return mixed
     */
    public function http_request($url, $method, $params, $json = true)
    {
        $uid = $this->uid;
        $token = $this->token;
        $postMsg = sprintf("\nPostData::%s::%s\n%s [%s]\n\n\r", $uid, $token, $url, $method);
        $receiveMsg = sprintf("\nReceiveData::%s::%s\n%s [%s]\n\n", $uid, $token, $url, $method);
        $this->serverHttpLog($postMsg, $params);
        $result = $this->sendRequest($url, $method, $params, $json);
        $this->serverHttpLog($receiveMsg, $result);
        return $result;
    }

    public function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
    }

    /**
     * http center
     * @param $url
     * @param $method
     * @param $params
     * @param $json
     * @return mixed
     */
    public function sendRequest($url, $method, $params, $json)
    {
        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
            $url = sprintf('%s%s', self::$host, $url);
        }

        $params = $params ? $params : [];
        $appName = Config::get('app')['name'];
        $header = array(
            sprintf("Authorization: Bearer %s",trim(Util::getUserToken())),
            'User-Agent: PinChe/1.0.0(wechat)',
            sprintf('%s-Version:500', $appName),
            sprintf('%s-Platform:wechat', $appName),
            sprintf('%s-OS:wechat', $appName),
        );
        Log::info("X_FORWARDED_FOR##".$this->getIp());
        $sender = Curl::to($url)
            ->enableDebug(self::$debugPath)
            ->withHeaders($header)
            ->withData($params)
            ->asJson($json);

        switch ($method)
        {
            case 'GET':
                $reponse = $sender->get();
                break;

            case 'POST':
                $reponse = $sender->post();
                break;

            case 'PUT':
                $reponse = $sender->put();
                break;

            case 'DELETE':
                $reponse = $sender->delete();
                break;

            default:
                $reponse = $sender->get();
                break;
        }

        $this->refreshLogin($reponse);

        return $reponse ? $reponse : [];
    }

    /**
     * 退出登录后清空本地session
     * @param $reponse
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshLogin($reponse)
    {
        $codeMap = [-10001];
        $filterMap = [
            '/shuttle-map'
        ];
        if(isset($reponse['code']) && in_array($reponse['code'], $codeMap) && !in_array($_SERVER['REQUEST_URI'], $filterMap)) {
//            Session::forget('account_info');
            Util::clearCacheUserInfo();
            $loginUrl = '/auth/login';
            if (request()->ajax()) {
                $data = [
                    'url'=>$loginUrl
                ];
                return response()->json((new ApiResult(-10001, '未登录', $data))->toJson());
            } else {
                $refer = sprintf('%s%s', Config::get('app')['url'], $_SERVER['REQUEST_URI']);
                $url = sprintf('%s?callback=%s', $loginUrl, urlencode($refer));
                return redirect($url);
            }
        }
    }

}
