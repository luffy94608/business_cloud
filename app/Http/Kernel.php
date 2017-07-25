<?php

namespace App\Http;

use App\Http\Middleware\AdaptAuthHeader;
use App\Http\Middleware\CheckDeposit;
use App\Http\Middleware\HeartBeatMiddleware;
use App\Http\Middleware\RouteRecordMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Tymon\JWTAuth\Facades\JWTAuth;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\RequestResponseLogger::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
//            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

//        'api' => [
//            'throttle:60,1',
//            'bindings',
//        ],
//
//        'driver' => [
//            'throttle:60,1',
//            'bindings',
//        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Auth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'wechat.oauth' => \Overtrue\LaravelWechat\Middleware\OAuthAuthenticate::class,
    ];
}
