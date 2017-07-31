<script src='http://api.map.baidu.com/api?v=2.0&ak={{Config::get('app')['ak']}}' type='text/javascript'></script>
<script type="text/javascript" src="http://developer.baidu.com/map/jsdemo/demo/convertor.js"></script>

<div class="dis-animate-overlay" id="ap_search_modal">
    <header class="header">
        <div class="header-left">
            <i class="header-icon icon-v-left js_back " onclick="this.parentNode.parentNode.parentNode.className = 'dis-animate-overlay'"></i>
        </div>
        <div class="header-right">
        </div>
        <h1 class="page-tt">搜索地址</h1>
    </header>
    <main id="pages" class="page-wrap">
        <div class="page">
            <div class="ap-search-section display-flex">
                <div class="p-10">
                    <svg class="icon-svg fill-grey fxWH-25">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/images/icons.svg#icon-search"></use>
                    </svg>
                </div>
                <input class="box-flex-1" type="text" id="search_input" placeholder="请输入搜索地址">
            </div>
            <ul class="ap-order-list search gone" id="search_result_panel">
            </ul>
            <ul class="ap-order-list search " id="search_history_panel">

            </ul>
        </div>
    </main>
</div>
