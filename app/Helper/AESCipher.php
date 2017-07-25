<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 16/11/2016
 * Time: 17:45
 */

namespace App\Helper;


class AESCipher
{
    protected $blockSize;
    protected $key;
    protected $iv;
    public function __construct($key, $iv)
    {
        $this->key = md5($key, true);
        $this->iv = md5($iv, true);
        $this->blockSize = 16;
    }

    private function pkcs5Pad($text)
    {
        $blockSize = $this->blockSize;
        $pad = $blockSize - (strlen($text) % $blockSize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function pkcs5Unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

    public function encrypt($data)
    {
        $key = $this->key;
        $iv = $this->iv;
        $blockSize = $this->blockSize;

        $data = $this->pkcs5Pad($data, $blockSize);

        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
        return $encrypted;
    }

    public function decrypt($encrypted)
    {
        $key = $this->key;
        $iv = $this->iv;

        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted, MCRYPT_MODE_CBC, $iv);
        return $this->pkcs5Unpad($decrypted);
    }
}
