<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 4/21/16
 * Time: 10:23 AM
 */

namespace App\Tools\HandleException;

use Illuminate\Support\Facades\Redis;

/**
 * 使用redis记录非常规异常，用于处理多次异常时，只发送一次报警。间隔时间60秒
 * Class RecordException
 * @package App\Tools\HandleException
 */
class RecordException
{
    private $delay = 60;

    public function ExceptionIN($exceptionCode)
    {
        $exceptionCode = strval($exceptionCode);
        $exceptionTime = Redis::get('Record:Exception:'.$exceptionCode);
        if(isset($exceptionTime))
        {
            if($exceptionTime > time())
            {
                return true;
            }

        }
        Redis::set('Record:Exception:'.$exceptionCode, time()+ $this->delay);
        return false;
    }
}