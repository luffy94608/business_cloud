<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WechatUser
 *
 * @property integer $id
 * @property string $open_id
 * @property string $name
 * @property string $nickname
 * @property boolean $sex
 * @property boolean $status
 * @property string $avatar
 * @property string $province
 * @property string $country
 * @property string $city
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereOpenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereNickname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereSex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereProvince($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatUser whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 */
class WechatUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['open_id', 'name', 'nickname', 'avatar', 'sex', 'status', 'province', 'country', 'city'];

    public function users()
    {
        return $this->hasMany('App\Models\User','open_id','open_id');
    }
}
