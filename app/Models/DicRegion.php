<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\DicRegion
 *
 * @property int $id
 * @property string $region_code
 * @property string $region_name
 * @property float $parent_id
 * @property float $region_level
 * @property float $region_order
 * @property string $region_name_en
 * @property string $region_shortname_en
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicRegion whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicRegion whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicRegion whereRegionCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicRegion whereRegionLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicRegion whereRegionName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicRegion whereRegionNameEn($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicRegion whereRegionOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicRegion whereRegionShortnameEn($value)
 * @mixin \Eloquent
 */
class DicRegion extends Model
{
    protected $table = 'dic_district';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public $timestamps = false;
}
