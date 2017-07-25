<?php

namespace App\Http\Middleware;

use App\Models\RouteRecord;
use App\Models\VersionLastConnect;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * route 记录，记录每天的route数量，与版本号
 * Class RouteRecordMiddleware
 * @package App\Http\Middleware
 */
class RouteRecordMiddleware
{
    
    protected $dontRecord = [
        'share/vote'
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $method = $request->getMethod();
        if ($method == 'POST')
        {
            
        }


        return $next($request);
    }
}
