<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  

use App\Models\ProductInfo;

class ProductInfoRepositories
{

    /**
     * 获取扫码订单
     * @param $id
     * @return mixed
     */
    public static function getProductInfoById($id)
    {
        $res = ProductInfo::find($id);
        return $res;
    }


}