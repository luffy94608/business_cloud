<?php

namespace App\Providers;

use App\Http\Controllers\Api\OtherApi;
use App\Http\Controllers\Api\WechatController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        View::composer('*',function($view){
            $env = env('APP_ENV');
            if ($env == 'local') {
                $jsSdk = \GuzzleHttp\json_encode([]);
            } else {
                $wechat = new WechatController();
                $jsSdk = $wechat->getJsSdk();
            }
            $config = \Cache::get('hollo_config');
            if (empty($config)) {
                $result  = OtherApi::getConfig();
                if (isset($result['code']) && $result['code']===0) {
                    $config = $result['data']['config'];
                    \Cache::put('hollo_config', $config, 60*60*24);
                } else {
                    $config = [];
                }
            }
            $params = [
                'config'=>$config,
                'js_api_list'=>$jsSdk
            ];
            $view->with($params);
        });
    }
}
