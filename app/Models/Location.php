<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Location
 *
 * @property integer $id
 * @property string $open_id
 * @property float $lat
 * @property float $lng
 * @property float $pre
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Location whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Location whereOpenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Location whereLat($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Location whereLng($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Location wherePre($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Location whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Location extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['open_id', 'lat', 'lng','pre'];
}
