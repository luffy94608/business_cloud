<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  


use App\Models\Profile;
use App\Models\User;

class UserRepositories
{
    /**
     * 添加user
     * @param $data
     * @return bool
     */
    public static function insertUser($data)
    {
        $user = new User();
        return BaseRepositories::updateOrInsert($user, $data)? $user->id :false;
    }

    /**
     * update user
     * @param $user
     * @param $data
     * @return bool
     */
    public static function updateUser($user, $data)
    {
        return BaseRepositories::updateOrInsert($user, $data);
    }

    /**
     * update profile
     * @param $profile
     * @param $data
     * @return bool
     */
    public static function updateProfile($profile, $data)
    {
        return BaseRepositories::updateOrInsert($profile, $data);
    }

    /**
     * 添加profile
     * @param $data
     * @return bool
     */
    public static function insertProfile($data)
    {
        $profile = new Profile();
        return BaseRepositories::updateOrInsert($profile, $data);
    }

    /**
     * 手机号是否存在
     * @param $mobile
     * @return bool
     */
    public static function mobileIsExist($mobile)
    {
        $result = User::where('username', $mobile)
            ->get();
        return $result->isNotEmpty();
    }

    /**
     * 登录是否成功
     * @param $mobile
     * @param $psw
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function login($mobile, $psw)
    {
        $result = User::where('username', $mobile)
            ->where('password', md5($psw))
            ->first();
        return $result;
    }


    /**
     * @param $id
     * @param bool $isObject
     * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public static function getProfile($id, $isObject = false)
    {
        $user = User::find($id);
        if (!is_null($user)) {
            $user->profile;
            if (!$isObject) {
                $user = $user->toArray();
            }
        }
        return $user;
    }

    /**
     * @param $mobile
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getUserByMobile($mobile)
    {
        $user = User::where('username', $mobile)
            ->first();
        return $user;
    }


}