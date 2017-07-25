/**
 * Created by luffy on 16/1/28.
 */

(function($){

    var init = {
        lotteryListSec : $('#js_dialog_list_section'),
        contentSec : $('#js_raffle_content'),
        awardModalResult : $('#js_dialog_section'),
        typeBtnNode : '.awd-type span',
        loading: false,
        loadingNodeObj: $('.toast-wrap'),

        btnEvent:function () {
            //type 切换
            $(document).on('click',init.typeBtnNode,function () {
                var slide = $('.bg-slide');
                var $this = $(this);
                var index = $this.index();
                if($this.parents('.awd-type').hasClass('disabled')){
                    return false;
                }

                $(init.typeBtnNode).removeClass('active');
                $this.addClass('active');
                if(index == 0){
                    slide.css('left',0);
                }else{
                    slide.css('left','50%');
                }
            });

            //查看中奖列表
            $(document).on('click','.icon-msg-info',function () {
                init.lotteryListSec.modalDialog();
            });

            $(document).on('click','.icon-close',function () {
                $(this).parents('.dialog-wrap').removeClass('active');
            });
            /**
             * 抽奖
             */
            $(document).on('click','.awd-submit',function () {
                if(init.loading){
                    return false;
                }
                // init.loadingNodeObj.addClass('active');
                init.loading = true;
                var params = {
                    type:$.trim($('.awd-type .active').data('type'))
                };
                $.wpost($.httpProtocol.LOTTERY_DRAW,params,function (res) {
                    init.lotteryListSec.find('.lottery-list').html(res.listHtml);
                    init.contentSec.html(res.contentSection);
                    init.awardModalResult.find('.dialog-cell ').html(res.html);
                    init.awardModalResult.modalDialog();
                    // init.awardModalResult.addClass('active');
                    
                    init.loading = false;
                    // init.loadingNodeObj.removeClass('active');
                },function () {
                    init.loading = false;
                    // init.loadingNodeObj.removeClass('active');
                });
            });
        },

        /**
         * 复制按钮
         */
        copyBtnEvent:function () {
            // 定义一个新的复制对象
            var clip = new ZeroClipboard( document.getElementById("js_test"), {
                moviePath: "./ZeroClipboard.swf"
            } );

            // 复制内容到剪贴板成功后的操作
            clip.on( 'complete', function(client, args) {
                // alert("复制成功"+ args.text);
                alert("复制成功");
            } );
        },

        run:function () {
            init.btnEvent();
            // init.copyBtnEvent();
            $('.page-loading').remove();
        }
    };

    init.run();
})($);