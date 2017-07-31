<!DOCTYPE html>
<html lang="zh-CN" class="@yield('bodyBg','')" >
{{--<html lang="zh-CN" class="@yield('bodyappBg','')"  manifest="/wechat.appcache">--}}
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <meta name="format-detection" content="telephone=no, email=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>@yield('title',\App\Models\Enums\SettingEnum::transform(\App\Models\Enums\SettingEnum::App_Name))</title>
    <link rel="stylesheet" href="/styles/bootstrap.css?v={{Config::get('app')['version']}}"/>
    <link rel="stylesheet" href="/styles/app.css?v={{Config::get('app')['version']}}"/>
    <link rel="stylesheet" href="/styles/toastr.min.css"/>
    {{--<link rel="stylesheet" href="/bower_components/select2/dist/css/select2.min.css"/>--}}
    <link rel="stylesheet" href="/styles/select2.css"/>
    <script type="text/javascript">
        document.global_config_data = {
            version: '{{Config::get('app')['version']}}',
            page:'{{ isset($page) ? $page : '' }}',
            resource_root: '{{Config::get('app')['url']}}',
            platform: '{{Config::get('app')['platform']}}',
            upyun_host: '{{Config::get('app')['upyun_host']}}',
            heart_time: 5000,
            heart_at: 0
        };
    </script>
    <!--[if lt IE 9]>
    <script src="/bower_components/html5shiv/dist/html5shiv.min.js"></script>
    <script src="/scripts/libs/respond.min.js"></script>
    <![endif]-->
    <!--[if IE]>
        <script type="text/javascript" src="/scripts/libs/excanvas.min.js"></script>
    <![endif]-->
</head>
<body class="@yield('bodyBg','')">
{{--内容区域--}}
@section('content')
    
@show

{{--模板--}}
{{--@include('templates.base',[])--}}
{{--@include('templates.bonus',[])--}}

<script src='/release/libs/require.js' data-main='/main.js' type='text/javascript'></script>
</body>
</html>
