<?php

namespace App\Http\Controllers\Api;


use App\Http\Requests;
use App\Http\Controllers\Controller;

class BaseApi extends Controller
{
    /**
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function getRequestData($url, $params)
    {
        $client = ApiClient::singleton();
        $result = $client->http_request($url, 'GET', $params);
        return $result;
    }

    /**
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function postRequestData($url, $params)
    {
        $client = ApiClient::singleton();
        $result = $client->http_request($url, 'POST', $params);
        return $result;
    }

    /**
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function putRequestData($url, $params)
    {
        $client = ApiClient::singleton();
        $result = $client->http_request($url, 'PUT', $params);
        return $result;
    }

    /**
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function deleteRequestData($url, $params)
    {
        $client = ApiClient::singleton();
        $result = $client->http_request($url, 'DELETE', $params);
        return $result;
    }


}
