<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\DicIndustry
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name 名称
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicIndustry whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicIndustry whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicIndustry whereParentId($value)
 * @mixin \Eloquent
 */
class DicIndustry extends Model
{
    protected $table = 'dic_industry';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public $timestamps = false;
}
