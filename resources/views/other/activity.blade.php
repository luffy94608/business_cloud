@extends('layouts.default')
@section('title', '哈罗同行')
@section('bodyBg', 'bg-light-blue')

{{--内容区域--}}
@section('content')
    <main id="page" >
        <div class="hl-activity">
            <img src="/images/other/a-banner.png">
            <div class="hl-activity-header">
                成功注册成为哈罗用户，即可获得哈罗为您准备的4张乘车券大礼包。赶快行动起来吧。<br/>
                礼包内的乘车券可以购买哈罗班车和快捷巴士的车票。<br/>
                哈罗班车一人一座，真正的私人定制班车。<br/>
                快捷巴士专属直达。<br/>
            </div>
            <div class="hl-activity-desc">
                <p class="hl-ad-title">礼包领取规则：</p>
                1 礼包会在您成功注册哈罗用户后，自动发送到您的哈罗账号内。<br/>
                2 礼包内乘车券可以购买班车车票或快捷巴士车票（注：不能用快捷巴士券购买班车车票，也不能用班车券购买快捷巴士车票），使用后，不可退回。<br/>
            </div>
        </div>
        <img class="hl-acb-tree" src="/images/other/a-tree.png">
    </main>
@stop

