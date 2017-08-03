<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductInfo
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property int $company_name
 * @property int $project_name
 * @property int $project_product
 * @property int $timestamp
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataBid whereCompanyName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataBid whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataBid whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataBid whereProjectName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataBid whereProjectProduct($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataBid whereTimestamp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataBid whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DataBid whereUserId($value)
 */
class DataBid extends Model
{
    protected $table = 'data_bid';

}
