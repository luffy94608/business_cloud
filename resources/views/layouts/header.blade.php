{{--header--}}
<nav class="navbar navbar-default navbar-static-top border-none">
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
                <ul class="nav navbar-nav navbar-right">
                    <li class="bch-login">
                        {{--登录状态--}}
                        <span class="b-icon-login-user"></span><a href="#">注销</a>

                        {{--未登录--}}
                        {{--<a href="#">登录</a>--}}
                        {{--/--}}
                        {{--<a href="#">注册</a>--}}
                    </li>
                </ul>
            </div><!--/.nav-collapse -->
        @endif

    </div>
</nav>