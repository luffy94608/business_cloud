<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  



use MongoDB\BSON\ObjectID;

class BaseRepositories
{
    /**
     * 万能修改类
     * @param $model
     * @param $updateData
     * @return bool
     */
    public static function updateOrInsert($model, $updateData)
    {
        $result = false;
        if($updateData && !is_null($model)) {
            $updateSwitch = false;
            foreach ($updateData as $key => $val) {
                if($val !== null && $model->{$key}!==$val) {
                    $model->{$key} = $val;
                    $updateSwitch = true;
                }
            }
            !$updateSwitch ?:$result =  $model->save();
        }
        return $result;
    }


    /**
     * 数组转换字典
     * @param $arr
     * @param $key
     * @return array
     */
    public static function arrayToDictionary($arr, $key)
    {
        $dic = [];
        foreach ($arr  as $v) {
            $dic[$v->{$key}] = $v;
        }
        return $dic;

    }

    /**
     * mongo获取字符串id
     * @param $id
     * @return string
     */
    public static function convertStringKey($id)
    {
        if ($id instanceof ObjectID) {
            $id = (string) $id;
        }
        return $id;
    }

   

}