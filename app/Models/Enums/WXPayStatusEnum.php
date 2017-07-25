<?php

namespace App\Models\Enums;

/**
 * App\Models\Enums\WXPayStatusEnum
 *
 * @mixin \Eloquent
 */
class WXPayStatusEnum
{
    const Unpaid            = 'unpaid';
    const Paid              = 'paid';
    const Refund            = 'refund';   
    const Failed            = 'failed';
}
