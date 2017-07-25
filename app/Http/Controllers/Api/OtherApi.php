<?php

namespace App\Http\Controllers\Api;



use App\Models\Enums\HttpURLEnum;
use Carbon\Carbon;

class OtherApi extends BaseApi
{

    /**
     * 获取config
     * @return mixed
     */
    public static function getConfig()
    {

        $url = HttpURLEnum::Config;
        $params = [
            'timestamp'=> 0,
        ];
        $result = self::postRequestData($url, $params);
        return $result;
    }

}
