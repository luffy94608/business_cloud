/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        loading :false,
        refundBtn :$('#js_refund_btn'),

        initBtnEvent : function () {

            /**
             * 跳转详情
             */
            $(document).on('click', '.bus-item',function () {
                var $this = $(this);
                var lineId = $this.data('line-id');
                $.locationUrl('/pay/'+lineId);
            });

            $('#read_btn').unbind().bind('change', function () {
                    init.refundBtn.toggleClass('disabled');
            });

            /**
             * 退票
             */
            init.refundBtn.unbind().bind('click' ,function () {
                var $this = $(this);
                var id = $this.data('id');
                if ($this.hasClass('disabled') || init.loading || !id.length) {
                    return false;
                }
                var params = {
                    ticket_id : id
                };

                $.confirm({
                    'content':$.string.TICKET_REFUND_CONFIRM_HINT,
                    'titleShow':true,
                    'success':function () {
                        init.loading = true;
                        $.wpost($.httpProtocol.REFUND_SHUTTLE_TICKET,params,function (data) {
                            $this.addClass('disabled').html($.string.TICKET_REFUND);
                            $.showToast(data.msg ? data.msg : $.string.TICKET_REFUND_HINT);
                            $('.agree').remove();
                            init.loading = false;
                        },function () {
                            init.loading = false;
                        });
                    }
                });

            });

        },
        run : function () {
            //搜索
            init.initBtnEvent();
        }
    };
    init.run();
})($);