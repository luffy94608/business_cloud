{{--dialog--}}
<section class="dialog-wrap" id="js_dialog_section">
    <div class="overlay"></div>
    <div class="dialog-content">
        <div class="dialog">
            <div class="dialog-bd clearfix">
                <h3 class="bd-tt"></h3>
                <div class="bd-table">
                    <div class="bd-cell">
                    </div>
                    <p class="bd-txt"></p>
                </div>
            </div>
            <!-- ft--full ft-btn-->
            <footer class="dialog-ft">
                <span class="ft-btn js_cancel">取消</span>
                <span class="ft-btn js_submit">确定</span>
            </footer>
        </div>
    </div>
</section>

{{--toast--}}
<section class="toast-wrap" id="js_toast_section">
    <div class="toast">
        <i class="toast-icon "></i>
        {{--<i class="toast-icon icon-right"></i>--}}
        <p class="toast-txt"></p>
    </div>
</section>
{{--toast loading--}}
<section class="toast-wrap" id="js_loading_toast_section">
    <div class="toast toast--loading has-close">
        <svg class="icon-svg icon-loading">
            <use xlink:href="/images/icons.svg#icon-loading"></use>
        </svg>
        <p class="toast-text">提交中...</p>
        {{--<i class="icon-close">&times;</i>--}}
    </div>
</section>

{{--显示班次--}}
<section class="dialog-wrap" id="js_shifts_dialog" >
    <div class="overlay"></div>
    <div class="dialog-content">
        <div class="dialog white">
            <div class="p-15 color-hint">
                <h3 class="text-center font-16">
                    <span class="js_shift_title"></span>
                    <svg class="icon-svg fxWH-25 fr mt-5 js_cancel_btn" style="position: absolute;right: 15px;">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/images/icons.svg#icon-close"></use>
                    </svg>
                </h3>
                <div class="js_shift_content shift-list clearfix">

                </div>

            </div>
        </div>
    </div>
</section>

{{--红包分享--}}
<section class="dialog-wrap " id="js_red_packet_modal" >
    <div class="overlay"></div>
    <div class="dialog-content" id="js_rpm_content">
        
    </div>
</section>


{{--分享--}}
<section class="dialog-wrap " id="js_share_hint_modal" >
    <div class="overlay"></div>
    <div class="share-overlay">
        <div class="js_share_content">
            请点击这里发送给朋友,<br/>或分享到朋友圈
        </div>
        <img class="share-arrow" src="/images/icons/share_arrow.png">
    </div>

</section>