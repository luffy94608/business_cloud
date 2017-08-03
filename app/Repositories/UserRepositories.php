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
    public static function addUser($data)
    {
        $user = new User();
        return BaseRepositories::updateOrInsert($user, $data)? $user->id :false;
    }

    /**
     * 添加profile
     * @param $data
     * @return bool
     */
    public static function updateOrInsertProfile($data)
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
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public static function getProfile($id)
    {
        $user = User::find($id);
        if (!is_null($user)) {
            $user->profile;
            $user = $user->toArray();
        }
        return $user;
    }


}