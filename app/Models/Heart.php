<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Heart
 *
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class Heart extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'uid');
    }
}
