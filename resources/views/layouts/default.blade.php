<!DOCTYPE html>
<html lang="zh-CN" class="@yield('bodyBg','')" >
{{--<html lang="zh-CN" class="@yield('bodyappBg','')"  manifest="/wechat.appcache">--}}
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title',\App\Models\Enums\SettingEnum::transform(\App\Models\Enums\SettingEnum::App_Name))</title>
    <meta name="format-detection" content="telephone=no, email=no"/>
    <link rel="stylesheet" href="/styles/app.css?v={{Config::get('app')['version']}}"/>
    <link rel="stylesheet" href="/bower_components/dropload/dist/dropload.css"/>
    <link rel="stylesheet" href="/styles/red_packet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <script type="text/javascript">
        document.global_config_data = {
            version: '{{Config::get('app')['version']}}',
            app_name: '{{\App\Models\Enums\SettingEnum::transform(\App\Models\Enums\SettingEnum::App_Name_Simple)}}',
            page:'{{ isset($page) ? $page : '' }}',
            resource_root: '{{Config::get('app')['url']}}',
            platform: '{{Config::get('app')['platform']}}',
            upyun_host: '{{Config::get('app')['upyun_host']}}',
            config: {!! json_encode($config) !!},
            heart_time: 5000,
            heart_at: 0
        };
    </script>

</head>
<body class="@yield('bodyBg','')">
{{--内容区域--}}
@section('content')
    
@show

{{--模板--}}
@include('templates.base',[])
@include('templates.bonus',[])


{{--微信js sdk--}}
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    if(typeof wx != 'undefined'){
        wx.config({!! $js_api_list !!});
        wx.error(function(res){
            // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名
            window.console.log("wx error data is ");
            window.console.log(res);
        });
    }else{
        console.log('wx load failed');
    }
</script>
{{--main--}}
<script src='/release/libs/require.js' data-main='/main.js' type='text/javascript'></script>
</body>
</html>
