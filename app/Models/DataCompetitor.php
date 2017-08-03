<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\DataCompetitor
 *
 * @property int $id
 * @property int $user_id
 * @property string $company
 * @property string $project_name
 * @property string $project_product
 * @property int $timestamp
 * @property int $power 竞争力
 * @property int $liveness 活跃度
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataCompetitor whereCompany($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataCompetitor whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataCompetitor whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataCompetitor whereLiveness($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataCompetitor wherePower($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataCompetitor whereProjectName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataCompetitor whereProjectProduct($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataCompetitor whereTimestamp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataCompetitor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataCompetitor whereUserId($value)
 * @mixin \Eloquent
 */
class DataCompetitor extends Model
{
    protected $table = 'data_competitor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}
