<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Website
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $position 广告位置
 * @property string $img_url 图片url
 * @property string $http_url 广告网址
 * @property int $status 1开启，0关闭
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Website whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Website whereHttpUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Website whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Website whereImgUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Website wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Website whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Website whereUpdatedAt($value)
 */
class Website extends Model
{
//    protected $table = 'product_info';

//    public $timestamps = false;


}
