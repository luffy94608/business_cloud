<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 2/15/17
 * Time: 3:39 PM
 */

namespace App\Exceptions;

/**
 * 用于在内部方法中返回错误信息。 handle exception 会返回客户端错误信息
 * Class MessageException
 * @package App\Exceptions
 */
class MessageException extends \Exception
{
    public function __construct($message, $code=-1, \Exception $previous=null)
    {
        parent::__construct($message, $code, $previous);
    }
}