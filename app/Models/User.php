<?php

namespace App\Models;


use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * App\Models\User
 *
 * @property integer $id
 * @property string $uid
 * @property string $open_id
 * @property string $token
 * @property string $expire_time
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereOpenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereExpireTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\WechatUser $wechatUser
 */
class User extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uid', 'open_id', 'token','expire_time'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
//    protected $hidden = [];

//    public $timestamps = false;

    /**
     * 更新用户和微信openid 绑定关系
     * @param $openId
     * @param $uid
     * @param $token
     * @return bool
     */
    public static function updateUserInfo($openId, $uid, $token)
    {
        if(!$openId || !$uid || !$token){
            return false;
        }
        $user = User::where('uid', $uid)
        ->first();
        if (!is_null($user)) {
            $user->open_id = $openId;
            $user->token = $token;
            $user->save();
        } else {
            $data = [
                'open_id'=>$openId,
                'uid'=>$uid,
                'token'=>$token
            ];
            User::insert($data);
        }


    }

    public function wechatUser()
    {
        return $this->belongsTo('App\Models\WechatUser','open_id','open_id');
    }
}
