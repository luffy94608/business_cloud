<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BusinessAnalysis
 *
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id 用户id
 * @property string $name 关注公司
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BusinessAnalysis whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BusinessAnalysis whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BusinessAnalysis whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BusinessAnalysis whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BusinessAnalysis whereUserId($value)
 */
class BusinessAnalysis extends Model
{
    protected $table = 'business_analysis';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}
