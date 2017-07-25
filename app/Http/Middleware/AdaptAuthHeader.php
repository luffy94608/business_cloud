<?php

namespace App\Http\Middleware;

use Closure;

class AdaptAuthHeader
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
        $sid = $request->header('sid');
        if ($sid) {
            $request->headers->set('Authorization', 'Bearer ' . $sid);
        }

        $token = $request->header(env('HEADER_PREFIX') . '-Token', '');
        if ($token) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        if ($request->header('Hollo-Platform') == 'Android' && $request->header('Content-Type') == 'application/octet-stream')
        {
            $request->headers->set('Content-Type', 'application/json; charset=utf-8');
        }

        return $next($request);
    }
}
