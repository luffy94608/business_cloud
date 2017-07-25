<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WechatAutoReply
 *
 * @property integer $id
 * @property string $key
 * @property string $content
 * @property boolean $type
 * @property boolean $enable
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatAutoReply whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatAutoReply whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatAutoReply whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatAutoReply whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatAutoReply whereEnable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatAutoReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WechatAutoReply whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WechatAutoReply extends Model
{
    protected $table = 'wechat_auto_reply';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'content', 'type', 'enable'];
}
