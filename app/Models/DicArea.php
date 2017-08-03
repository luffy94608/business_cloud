<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\DicArea
 *
 * @property int $id
 * @property string $area
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicArea whereArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicArea whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicArea whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DicArea whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DicArea extends Model
{
    protected $table = 'dic_area';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}
