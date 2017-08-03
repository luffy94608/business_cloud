<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductInfo
 *
 * @mixin \Eloquent
 * @property-read \App\Models\User $profile
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property bool $gender
 * @property string $position 职位
 * @property string $mail
 * @property string $company_name
 * @property int $company_area
 * @property int $company_industry
 * @property string $follow_area 关注区域
 * @property int $follow_industry 关注行业
 * @property string $follow_keyword data_keyword中的id，拼接字符串，逗号分隔
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereCompanyArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereCompanyIndustry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereCompanyName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereFollowArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereFollowIndustry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereFollowKeyword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereMail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereUserId($value)
 */
class Profile extends Model
{
//    protected $table = 'activity';

    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}
