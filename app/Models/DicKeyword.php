<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\DicKeyword
 *
 * @property int $id
 * @property string $keyword
 * @property int $industry_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicKeyword whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicKeyword whereIndustryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicKeyword whereKeyword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicKeyword whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DicKeyword extends Model
{
    protected $table = 'dic_keyword';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}
