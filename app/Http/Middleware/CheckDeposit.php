<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckDeposit
{
    /**
     * 未支付押金使用自行车时进行拦截
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
//        if (!isset($user->deposit_paid) || $user->deposit_paid == false) {
//            return response()->json([
//                'code' => -1,
//                'msg' => '未支付押金，请重新登录。',
//                'data' => []
//            ]);
//        }
        return $next($request);
    }
}
