<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Enums\HeartTypeEnum
 *
 * @mixin \Eloquent
 */
class HeartTypeEnum extends Model
{
    const Version           = 'version';    //是否有新版本
    const Notification      = 'notification';    //是否有未读通知
    const RefreshProfile    = 'refresh_profile';    //需要刷新profile

    const Driver_NewMission    = 'schedule';    //司机有新任务
}
