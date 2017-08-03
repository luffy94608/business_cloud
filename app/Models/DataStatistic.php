<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\DataStatistic
 *
 * @property int $id
 * @property int $user_id
 * @property int $tender 招标信息数量
 * @property int $bid 中标信息数量
 * @property int $competitor 竞争对手数量
 * @property int $tender_today
 * @property int $bid_today
 * @property int $competitor_today
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatistic whereBid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatistic whereBidToday($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatistic whereCompetitor($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatistic whereCompetitorToday($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatistic whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatistic whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatistic whereTender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatistic whereTenderToday($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatistic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatistic whereUserId($value)
 * @mixin \Eloquent
 */
class DataStatistic extends Model
{
    protected $table = 'data_statistics';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}
