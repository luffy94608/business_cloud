{{--红包相关--}}

{{--红包 style 1--}}
<script id="js_red_style_1" type="text/html">
    <div class="hl-red-packet-body width-p-100  " >
        <i class="hl-rp-close-1 js_bonus_close_btn" ></i>
        <div class="hl-rp-bg-1">
            <div class="hl-rp1-title">@{{ content.data.title }}</div>
            <div class="hl-rp1-content"> @{{ content.data.description }}</div>
            @{{each display.buttons as button index}}
                <div class="hl-rp1-btn js_bonus_click_btn" data-url="@{{ button.jump_url }}" data-action="@{{ button.action }}" >@{{ button.title }}</div>
            @{{/each}}
        </div>
    </div>
</script>

{{--红包 style 2--}}
<script id="js_red_style_2" type="text/html">
    <div class="hl-red-packet-body ">
        <i class="hl-rp-close-2 js_bonus_close_btn" ></i>
        <p class="hl-rp-title color-white mt-50">@{{ content.data.title }}</p>
        <p class="hl-rp-sub-title color-white mt-20"> @{{ content.data.description }}</p>
        <img class="hlthp-img mt-20" src="/images/red-packet/gift-2-img@2x.png">
        <div class="hlthp-button-list mt-20">
            @{{each display.buttons as button index}}
            <button class="hl-rp-btn-1 box-flex-1 js_bonus_click_btn" data-url="@{{ button.jump_url }}" data-action="@{{ button.action }}" >@{{ button.title }}</button>
            @{{/each}}
        </div>
    </div>
</script>


{{--红包 style 3--}}
<script id="js_red_style_3" type="text/html">
    <div class="hl-red-packet-body " >
        <i class="hl-rp-close-2 js_bonus_close_btn" ></i>
        <p class="hl-rp-title color-white mt-50">@{{ content.data.title }}</p>
        <p class="hl-rp-sub-title color-white mt-20"> @{{ content.data.description }}</p>
        <img class="hl-rp-img pl-25 mt-20" width="80%" src="/images/red-packet/gift-3-img@2x.png">
        <div class="hlthp-button-list mt-20 display-box">
            @{{each display.buttons as button index}}
            <button class="hl-rp-btn-1 box-flex-1 js_bonus_click_btn" data-url="@{{ button.jump_url }}" data-action="@{{ button.action }}" >@{{ button.title }}</button>
            @{{/each}}
        </div>
    </div>
</script>


{{--红包 style 4--}}
<script id="js_red_style_4" type="text/html">
    <div class="hl-red-packet-body bg-white " >
        <i class="hl-rp-close-4 js_bonus_close_btn" ></i>
        <p class="hl-rp-title color-orange mt-50">@{{ content.data.title }}</p>
        <p class="hl-rp-sub-title color-orange mt-20"> @{{ content.data.description }}</p>
        <img class="hlthp-img" src="/images/red-packet/gift-4-img@2x.png">
        <div class="hlthp-button-list mt-10 display-box">
            @{{each display.buttons as button index}}
                <button class="hlthp-button bg-orange border-none box-flex-1 ml-5 mr-5 js_bonus_click_btn" data-url="@{{ button.jump_url }}" data-action="@{{ button.action }}" >@{{ button.title }}</button>
            @{{/each}}
        </div>
    </div>
</script>


{{--红包 bonus --}}
<script id="js_red_style_bonus" type="text/html">
    <div class="hlthp-img-title " >
        <i class="hl-rp-close-2  js_bonus_close_btn" ></i>
        <img  src="/images/red-packet/red_packet_3_body@2x.png">
    </div>
    <div class="hl-th-share-packet" >
        <img class="hlthp-img" src="/images/red-packet/red_packet_3_header@2x.png">
        <p class="hlthp-desc">@{{ content.data.description }}</p>
        <div class="hlthp-button-list">
            <button class="hlthp-button js_bonus_share_btn" >发红包</button>
        </div>
    </div>
</script>