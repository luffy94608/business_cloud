<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\DataPublisher
 *
 * @property int $id publisher_id
 * @property int $user_id 用户ID
 * @property string $title 标题
 * @property string $from 方式
 * @property int $timestamp 时间戳
 * @property int $product 产品
 * @property int $area_id 招标地点
 * @property int $power 竞争力
 * @property bool $is_hot 最热信息
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher whereAreaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher whereFrom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher whereIsHot($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher wherePower($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher whereProduct($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher whereTimestamp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataPublisher whereUserId($value)
 * @mixin \Eloquent
 */
class DataPublisher extends Model
{
    protected $table = 'data_publisher';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class,'user_id', 'user_id');
    }

    public function area()
    {
        return $this->belongsTo(DicRegion::class,'area_id', 'id');
    }
}
