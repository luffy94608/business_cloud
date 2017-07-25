{{-- 快捷巴士线路列表 --}}
<script id="tpl-shuttle-list" type="text/html">
    @{{each lines as line index}}
        <div class="shuttle-item animated fadeInUp ant-delay-@{{ index+1 }}" data-id="@{{ line.line_id }}">
            <div class="shuttle-header clearfix">
                <span class="code">@{{ line.line_code }} </span>
                <p class="name">@{{ line.line_name }} </p>
                {{--<span class="more bus-after-v js_more_btn" >&nbsp;</span>--}}
            </div>
            <div class="shuttle-body">
                <div class="item-bd">
                    <h4 class="bd-tt">运营时间：@{{ line.business_hour }}</h4>
                    <div class="bd-txt">票价：<span class="price">@{{ line.price }}元</span></div>
                </div>
                <div class="item-right">
                    @{{ if line.status == 0  }}
                        <button class='btn btn-primary full-width btn-s js_buy_btn'>购买车票</button>
                    @{{ else }}
                        <button class='btn btn-primary full-width btn-s disabled js_disabled_btn'>暂未运营</button>
                    @{{ /if }}
                </div>
            </div>
        </div>
    @{{/each}}
</script>

{{--班车车票--}}
<script id="js_tpl_bus_ticket" type="text/html">
    <li class='stl-item swiper-slide' id='js_ticket_{0}'>
        <div class='js_style_section'></div>
        <header class='tk-header'>
            <div class='thh-content'>
                <p class='code'>{1}</p>
                <p class='name'>{2}</p>
            </div>
            <span class='tk-checked js_bg_checked_color' data-title='已验票'></span>
        </header>
        <article class='tk-body js_bg_color '>
            <p class='date'>{3}</p>
            <p class='info'>乘车时间：{4}</p>
            <p class='info'>车票价格：{5}元</p>
        </article>
        <p class='tk-gap js_bg_color js_before_after'> <span class='line'></span> </p>
        <footer class='tk-footer js_bg_color'>
            <div class='tkf-content'>
                <div>
                    <p class='info'>车牌号</p>
                    <p class='title'>{6}</p>
                </div>
                <div>
                    <p class='info'>座位号码</p>
                    <p class='title'>{7}</p>
                </div>
            </div>
            <button class='btn btn-primary tk-btn text-center full-width js_ticket_check_btn disabled' data-id='{0}'>
                上车验票
            </button>
        </footer>
    </li>
</script>

{{--快捷巴士车票--}}
<script id="js_tpl_shuttle_ticket" type="text/html">
    <div class='stl-item swiper-slide' id='js_ticket_@{{ ticket_id }}'>
        <div class='js_style_section'></div>
        <header class='tk-header'>
            <div class='thh-content'>
                <p class='code'>@{{ title }}</p>
            </div>
            <span class='tk-checked js_bg_checked_color' data-title='已验票'></span>
        </header>
        <article class='tk-body js_bg_color '>
            <p class='info'>有效期</p>
            <p class='date'>@{{ dept_date }}</p>
            <p class='info'>@{{ shuttle_line.business_hour }}</p>
        </article>
        <p class='tk-gap js_bg_color js_before_after'> <span class='line'></span> </p>
        <footer class='tk-footer js_bg_color'>
            <div class='tkf-content'>
                <p class='info'>@{{ desc }}</p>
                <p class='info'>一票一人</p>
            </div>
            <button class='btn btn-primary tk-btn text-center full-width js_ticket_check_btn disabled' data-id='@{{ ticket_id }}'>
                上车验票
            </button>
        </footer>
    </div>
</script>