<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\DataSubscribe
 *
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataSubscribe whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataSubscribe whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataSubscribe whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DataSubscribe extends Model
{
    protected $table = 'data_subscribe';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}
