<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 16/11/2016
 * Time: 17:45
 */

namespace App\Helper;


class XORCipher
{
    protected $key;
    public function __construct($key)
    {
        $this->key = $key;
    }

    private function doXOR($str)
    {
        $key = $this->key;

        $res = '';

        for ($i=0; $i < strlen($str); $i++) {
            $res .= chr(ord($str[$i]) ^ $key);
        }
        return $res;
    }

    public function encrypt($str)
    {
        return $this->doXOR($str);
    }

    public function decrypt($str)
    {
        return $this->doXOR($str);
    }
}
