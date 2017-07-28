{{--header--}}
<nav class="navbar navbar-default navbar-static-top border-none">
    <div class="container bc-header">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">菜单</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">
                <img src="/images/logo.png" class="bch-logo">
            </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse" aria-expanded="false" style="height: 1px;">
            <ul class="nav navbar-nav menu">
                <li class="active"><a href="#">招标信息</a></li>
                <li><a href="#about">中标信息</a></li>
                <li><a href="#contact">竞争对手</a></li>
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
    </div>
</nav>