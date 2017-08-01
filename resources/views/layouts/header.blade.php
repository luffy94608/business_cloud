{{--header--}}
<nav class="navbar navbar-default navbar-static-top border-none {{ isset($bgStyle) ? $bgStyle : '' }}">
    <div class="container bc-header">
        <div class="navbar-header">
            @if(!isset($hideContent))
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">菜单</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            @endif
            <a class="navbar-brand" href="/">
                <img src="/images/logo.png" class="bch-logo">
            </a>
        </div>
        @if(!isset($hideContent))
            <div id="navbar" class="navbar-collapse collapse {{ isset($hideContent) ? 'gone' : ''  }}" aria-expanded="false" style="height: 1px;">
                <ul class="nav navbar-nav menu">
                    <li class="{{Request::path() == 'bid-call' ? 'active' : ''}}"><a href="/bid-call">招标信息</a></li>
                    <li class="{{Request::path() == 'bid-winner' ? 'active' : ''}}"><a href="/bid-winner">中标信息</a></li>
                    <li class="{{Request::path() == 'rival' ? 'active' : ''}}"><a href="/rival">竞争对手</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right ">
                    <li class="bch-login dropdown" >
                        {{--<span class="b-icon-logout-user"></span><a href="#">登录</a>--}}
                        {{--<a href="#" class="dropdown-toggle" id="drop_down_profile_menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">常先生</a>--}}
                        <a href="#" class="dropdown-toggle bg-white" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <span class="b-icon-login-user"></span>
                            常先生
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu bc-drop-menu" aria-labelledby="drop_down_profile_menu">
                            <li><a href="#">个人信息</a></li>
                            <li><a href="#">修改密码</a></li>
                            <li><a href="#">退出登录</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!--/.nav-collapse -->

        @endif

    </div>
</nav>