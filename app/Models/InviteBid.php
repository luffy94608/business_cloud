<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\InviteBid
 *
 * @property int $id
 * @property string $title 标题
 * @property string $url url
 * @property string $area 地区ID
 * @property string $industry 行业ID
 * @property string $tenderee 招标人
 * @property float $budget 项目预算
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InviteBid whereArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InviteBid whereBudget($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InviteBid whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InviteBid whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InviteBid whereIndustry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InviteBid whereTenderee($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InviteBid whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InviteBid whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InviteBid whereUrl($value)
 * @mixin \Eloquent
 */
class InviteBid extends Model
{
    protected $table = 'invite_bid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $fillable = ['key', 'content', 'type', 'enable'];
}
