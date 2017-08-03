<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\DataStatisticDetail
 *
 * @property int $id
 * @property int $user_id
 * @property int $tender_today_total 今日招标
 * @property int $tender_week_total 本周招标
 * @property int $tender_month_total 本页招标
 * @property int $bid_today_total 今日中标
 * @property int $bid_week_total 本周中标
 * @property int $bid_month_total 本月中标
 * @property int $competitor_today_total 今日新增企业
 * @property int $competitor_total 行业竞争对手
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereBidMonthTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereBidTodayTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereBidWeekTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereCompetitorTodayTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereCompetitorTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereTenderMonthTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereTenderTodayTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereTenderWeekTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataStatisticDetail whereUserId($value)
 * @mixin \Eloquent
 */
class DataStatisticDetail extends Model
{
    protected $table = 'data_statistics_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}
