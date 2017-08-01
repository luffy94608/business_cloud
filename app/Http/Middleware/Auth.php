<?php

namespace App\Http\Middleware;

use App\Helper\Util;
use App\Models\ApiResult;
use Closure;
use Illuminate\Support\Facades\Config;

class Auth
{

    /**
     * Auth constructor.
     */
    public function __construct()
    {

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $loginUrl = '/login';
        $uid = Util::getUid();
        if(empty($uid)){
            if ($request->ajax()) {
                $data = [
                    'url'=>$loginUrl
                ];
                return response()->json((new ApiResult(-10001, '未登录', $data))->toJson());
            } else {
                $refer = sprintf('%s%s', Config::get('app')['url'], $_SERVER['REQUEST_URI']);
                $url = sprintf('%s?callback=%s', $loginUrl, urlencode($refer));
                return redirect($url);
            }

        }
        return $next($request);
    }
}
