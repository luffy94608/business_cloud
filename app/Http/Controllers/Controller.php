<?php

namespace App\Http\Controllers;

use App\Helper\Util;
use App\Models\Enums\ErrorEnum;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\ApiResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /***
     * @var $openId
     */
    protected $openId;

    /***
     * @var $openId
     */
    protected $uid;

    /***
     * @var $sid
     */
    protected $sid;
    protected $isVip;


    public function __construct()
    {
        $this->refreshInitInfo();
    }

    public function refreshInitInfo()
    {
        //TODO Session In The Constructor not work 
        $this->middleware(function ($request, $next) {
            $this->uid = Util::getUid();
            $this->isVip = Util::isVip();
            return $next($request);
        });

    }



    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        $keys = array_keys($errors);
        $error_msg = $errors[$keys[0]][0];
        return response()->json((new ApiResult(-1, $error_msg ?: '参数错误', ''))->toJson());
    }

    /**
     * 获取客户端ip
     * @return mixed
     */
    public function getRemoteIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');

        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * 通用返回结果
     * @param $result
     * @return \Illuminate\Http\JsonResponse
     */
    public function inputResult($result)
    {
        if (isset($result['code']) && $result['code'] === 0) {
            $data = $result['data'];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            $code = isset($result['code']) ? $result['code'] : -1;
            $desc = isset($result['msg']) ? $result['msg'] : ErrorEnum::transform(ErrorEnum::Failed);
            return response()->json((new ApiResult($code, $desc, $result))->toJson());
        }
    }

    /***********************
    第二种实现办法：用readdir()函数
     ************************/
    public  function listDir($dir)
    {
        if(is_dir($dir))
        {
            if ($dh = opendir($dir))
            {
                while (($file = readdir($dh)) !== false)
                {
                    if((is_dir($dir."/".$file)) && $file!="." && $file!="..")
                    {
//                    echo "<b><font color='red'>文件名：</font></b>",$file,"<br><hr>";
                        $this->listDir($dir."/".$file."/");
                    }
                    else
                    {
                        if($file!="." && $file!="..")
                        {
                            echo sprintf('%s%s<br/>',$dir, $file);
                        }
                    }
                }
                closedir($dh);
            }
        }
    }
}
