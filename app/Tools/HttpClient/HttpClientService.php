<?php

namespace App\Tools\HttpClient;

/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 2/9/17
 * Time: 4:13 PM
 */

use App\Models\Enum\HttpUrlEnum;
use GuzzleHttp;
use Carbon\Carbon;

class HttpClientService
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new GuzzleHttp\Client(['timeout' => 10]);
    }

    public function postJson($url, $data) {
        return json_decode($this->httpClient->post($url, ['json' => $data])->getBody()->__toString(), true);
    }

    public function get($url, $param) {
        $paramStr = http_build_query($param);
        $url = $url.'?'.$paramStr;
        return json_decode($this->httpClient->get($url)->getBody()->__toString(), true);
    }

    /**
     * 发送报警
     * @param $alarmSubcategory
     * @param $pushMessage
     * @param array $extraInfo
     */
    public function SendWarning($alarmSubcategory, $pushMessage, $extraInfo=[]) {
        app('http_client')->postJson(HttpUrlEnum::Warning, [
            'alarm_subcategory' => $alarmSubcategory,
            'push_message'=> $pushMessage,
            'warning_time'=> Carbon::now()->timestamp,
            'extra_info' => count($extraInfo) > 0 ? $extraInfo : json_decode('{}')
        ]);
    }
}