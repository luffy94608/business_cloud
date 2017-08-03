<?php

namespace App\Models;

use App\Http\Transformers\ApiResultTransformer;
use Spatie\Fractalistic\ArraySerializer;

class ApiResult
{
    protected $code;
    protected $msg;
    protected $data;
    protected $heart;

    public function __construct($code, $msg, $data, $heart = [])
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
        $this->heart = $heart;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function getHeart()
    {
        return $this->heart;
    }

    public function getData()
    {
        $data = $this->data;
//        $data['heart'] = $this->heart;
        if (empty($data))
        {
            $data = json_decode('{}');
        }
        return $data;
    }

    public function toJson()
    {
        $result = fractal()
            ->item($this, new ApiResultTransformer())
            ->serializeWith(new ArraySerializer())
            ->toArray();
        return $result;
    }
}
