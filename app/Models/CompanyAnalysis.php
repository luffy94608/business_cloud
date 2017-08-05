<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CompanyAnalysis
 *
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id 用户id
 * @property string $area 关注区域
 * @property int $industry 关注行业
 * @property string $time 关注时间 月
 * @property string $keyword 关键词
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CompanyAnalysis whereArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CompanyAnalysis whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CompanyAnalysis whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CompanyAnalysis whereIndustry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CompanyAnalysis whereKeyword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CompanyAnalysis whereTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CompanyAnalysis whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CompanyAnalysis whereUserId($value)
 */
class CompanyAnalysis extends Model
{
    protected $table = 'company_analysis';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
