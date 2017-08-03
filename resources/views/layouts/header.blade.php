{{--header--}}
<nav class="navbar navbar-default navbar-static-top border-none {{ isset($bgStyle) ? $bgStyle : '' }}">
    <div class="container bc-header p-relative ">
        <div class="navbar-header">
            @if(!isset($hideContent))
                {{--<button type="button" class="navbar-toggle collapsed border-none" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">--}}
                    {{--<span class="b-icon-menu"></span>--}}
                {{--</button>--}}
            @endif
            <a class="navbar-brand" href="/">
                <div class="bc-log-img"></div>
                {{--<img src="/images/logo.png" class="bch-logo">--}}
            </a>
        </div>
        @if(!isset($hideContent))
            <button type="button" class="navbar-toggle dropdown-toggle collapsed border-none" data-toggle="dropdown"  aria-haspopup="true"  aria-expanded="false" aria-controls="navbar">
                <span class="b-icon-menu"></span>
            </button>
            <div id="navbar" class="dropdown-menu bc-menu-content collapse {{ isset($hideContent) ? 'gone' : ''  }}" aria-expanded="false" >
                <ul class="nav navbar-nav menu">
                    <li class="triangle"></li>
                    <li class="{{Request::path() == 'bid-call' ? 'active' : ''}}"><a href="/bid-call">招标信息</a></li>
                    <li role="separator" class="divider"></li>
                    <li class="{{Request::path() == 'bid-winner' ? 'active' : ''}}"><a href="/bid-winner">中标信息</a></li>
                    <li role="separator" class="divider"></li>
                    <li class="{{Request::path() == 'rival' ? 'active' : ''}}"><a href="/rival">竞争对手</a></li>
                </ul>
            </div><!--/.nav-collapse -->
            <div class="bch-login" >
                {{--<span class="b-icon-logout-user"></span><a href="#">登录</a>--}}
                {{--<a href="#" class="dropdown-toggle" id="drop_down_profile_menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">常先生</a>--}}
                <a href="#" class="dropdown-toggle " id="drop_down_profile_menu"  data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="hidden-xs">
                        <span class="b-icon-login-user"></span>
                        常先生
                        <span class="caret"></span>
                    </div>
                    <div class="visible-xs">
                        <span class="b-icon-menu-user"></span>
                    </div>
                </a>
                <ul  class="dropdown-menu bc-drop-menu pull-right" aria-labelledby="drop_down_profile_menu">
                    <li class="triangle"></li>
                    <li><a href="#">个人信息</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#">修改密码</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#">退出登录</a></li>
                </ul>
            </div>
        @endif

    </div>
</nav>