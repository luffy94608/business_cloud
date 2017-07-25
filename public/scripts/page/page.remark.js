/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        remarkBtn :$('#js_remark_btn'),
        maxText : 60,
        loading :false,
        scoreItemNode : '.js_bus_score_item',

        getScoreParams:function () {
            var params = [];
            var targets = $(init.scoreItemNode);
            var len = targets.length;
            if (len) {
                for (var i=0;i<len;i++) {
                    var item = $(targets[i]);
                    var value  = {
                        key:item.data('key'),
                        score:$.trim(parseInt(item.data('score')))
                    };
                    params.push(value);
                }
            }
            return params;
        },

        initBtnEvent : function () {
            
            /**
             * 评论
             */
            init.remarkBtn.unbind().bind($.getClickEventName(),function () {
                if (init.loading) {
                    return false;
                }
                var scoreParams = init.getScoreParams();
                var params = {
                    ticket_id: $('#js_ticket_id').val(),
                    comment: $.trim($('#js_comment').val()),
                    score: scoreParams
                };
                var deptAt = $(this).data('time');
                if (params.comment.length > init.maxText) {
                        $.showToast($.string.REMARK_CONTENT_LIMIT, false);
                        return false;
                }
                init.loading = true;
                $.wpost($.httpProtocol.BUS_REMARK, params, function (data) {
                    $.showToast($.string.REMARK_SUCCESS);
                    var cacheKey = 'js_ticket_list_cache_key_'+new Date(deptAt*1000).Format('yyyy_MM_dd');
                    $.cache.remove(cacheKey);
                    setTimeout(function () {
                        $.locationUrl('/order-detail/'+params.ticket_id, true);
                    }, 300)
                    // init.loading = false;
                }, function () {
                    init.loading = false;
                })
            });

            /**
             * 最大字数限制
             */
            $('#js_comment').unbind().bind('input', function () {
                var $this = $(this);
                var val = $this.val();
                var len = val.length;
                var newVal = val.substr(0,init.maxText);
                $('.js_text_count').html(newVal.length);
                $this.val(newVal);
            });

            /**
             * 打分事件
             */
            $.initScoreEvent();

        },
        run : function () {
            init.initBtnEvent();
        }
    };
    init.run();
})($);