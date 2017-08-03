<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class RequestResponseLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        return $next($request);
        
        $requestData = $request->__toString();
        Log::info(sprintf(" INPUT:  %s  %s \n%s", $request->ip(), $request->url(), $requestData));
        $response = $next($request);

        $responseData = $response->__toString();
        Log::info(sprintf(" OUTPUT: %s  %s \n%s", $request->ip(), $request->url(), $responseData));

        return $response;
    }
}
