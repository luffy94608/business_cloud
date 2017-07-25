/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        loading :false,
        refundBtn :$('#js_refund_btn'),

        initBtnEvent : function () {

            /**
             * 评价
             */
            $(document).on('click', '.js_remark_btn',function (e) {
                e.stopPropagation();
                var id = $(this).data('id');
                $.locationUrl('/remark/'+id);
            });


            /**
             * 同意选择框
             */
            $('#read_btn').unbind().bind('change', function () {
                    init.refundBtn.toggleClass('disabled');
            });
            if ($('#read_btn').prop('checked')) {
                init.refundBtn.removeClass('disabled');
            } else {
                init.refundBtn.addClass('disabled');
            }

            /**
             * 退票
             */
            init.refundBtn.unbind().bind('click' ,function () {
                var $this = $(this);
                var id = $this.data('id');
                var ahead = parseInt($this.data('ahead'));
                var deptAt = parseInt($this.data('dept-at'));
                if ($this.hasClass('disabled') || init.loading || !id.length) {
                    return false;
                }
                var currentTime = Math.floor(new Date().getTime()/1000);
                if (currentTime> deptAt - ahead) {
                    $.showToast($.string.TICKET_REFUND_FORBIDDEN_HINT.format(Math.floor(ahead/60)));
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
                        $.wpost($.httpProtocol.REFUND_BUS_TICKET,params,function (data) {
                            $this.addClass('disabled').html($.string.TICKET_REFUND);
                            $.showToast(data.msg ? data.msg : $.string.TICKET_REFUND_HINT);
                            $('.agree').remove();
                            var cacheKey = 'js_ticket_list_cache_key_'+new Date(deptAt*1000).Format('yyyy_MM_dd');
                            $.cache.remove(cacheKey);
                            init.loading = false;
                        },function () {
                            init.loading = false;
                        });
                    }
                });

            });

            /**
             * 打分事件
             */
            $.initScoreEvent();

        },
        run : function () {
            //搜索
            init.initBtnEvent();
        }
    };
    init.run();
})($);