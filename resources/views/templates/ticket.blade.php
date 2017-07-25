{{--车票--}}
<section class="dialog-wrap " id="js_ticket_modal" >
    {{--隐藏样式--}}
    <div class="overlay"></div>
    <div class="dialog-content">
        <div class="dialog-header ">
            <h3 class="text-center tk-dialog-header">
                {{--我的票夹--}}
                &nbsp;
                <span class="close js_close_btn">关闭</span>
            </h3>
        </div>
        <div class="dialog tk-dialog">
            <ul class="show-ticket-list" id="js_ticket_list">
                
            </ul>
        </div>
    </div>
</section>

{{--车票列表--}}
<section class="dialog-wrap " id="js_ticket_list_modal" >
    {{--隐藏样式--}}
    <div class="overlay"></div>
    <div class="dialog-content">
        <div class="dialog-header ">
            <h3 class="text-center tk-dialog-header">
                我的票夹
                <span class="close js_close_btn">关闭</span>
            </h3>
        </div>
        <div class="dialog tk-list-dialog ">
            <div class="swiper-ticket-container">
                <div class="show-ticket-list swiper-wrapper" id="js_ticket_list">

                </div>
            </div>
        </div>
    </div>
</section>