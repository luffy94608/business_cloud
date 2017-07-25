/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        submitBtn :$('#js_remark_btn'),
        inputLineTarget :$('#js_input_line'),
        reasonPickNode :'.js_reason_pick',
        modal : $('#js_search_line_modal'),
        lineSection : $('#js_line_sec'),
        loading :false,
        /**
         * 日期选择
         */
        initTimeSelectEvent:function () {
            $('#datetime').mobiscroll().date({
                theme: 'ios',      // Specify theme like: theme: 'ios' or omit setting to use default
                lang: 'zh',    // Specify language like: lang: 'pl' or omit setting to use default
                display: 'bottom',  // Specify display mode like: display: 'bottom' or omit setting to use default
                monthNames: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                showNow: true,
                rows : 4,
                cancelText:'取消',
                setText:'完成',
                daySuffix:'日',
                monthSuffix:'月',
                yearSuffix:'年',
                dateWheels: 'yy mm dd',
                showLabel : false,
                // min: new Date(),
                // max: new Date(now.getTime() + 3*60*60*24*1000),
                headerText : '',
                dateFormat: 'yy-mm-dd',
                hourText: "点",
                nowText: "今天",
                onSet: function (event, inst) {
                    var time = inst.getVal();
                },
                onChange: function (event, inst) {

                },
                onInit: function (event, inst) {
                   
                }
            });
        },
        /**
         * 获取投诉选项
         */
        getPickItems : function () {
            var res = [];
            var targets =  $(init.reasonPickNode+':checked');
            var len = targets.length;
            if (len) {
                targets.each(function (index, item) {
                    res.push(parseInt($(item).val()))
                });
            }
            return res;
        },
        /**
         * 显示路线选择
         */
        showModal : function () {
            init.lineSection.addClass('fixed-to-dialog');
            init.modal.addClass('active');
            $('body,html').addClass('o-hidden');
            $('.dialog-scroll').height($(window).height());

        },
        /**
         * 隐藏路线选择
         */
        hideModal : function () {
            init.lineSection.removeClass('fixed-to-dialog');
            init.modal.removeClass('active');
            $('body,html').removeClass('o-hidden');
        },
        /**
         * 路线搜索
         * @param text
         */
        searchLineByKeyEvent:function (text) {
            var targets = init.modal.find('.js_line_item_option');
            targets.each(function (key, item) {
                var itemTarget = $(item);
                var lineTxt = itemTarget.text().toLowerCase();
                if (lineTxt.indexOf(text.toLowerCase()) === -1) {
                    itemTarget.addClass('gone');
                } else {
                    itemTarget.removeClass('gone');
                }
            });
        },

        initBtnEvent : function () {
            /**
             * 投诉
             */
            init.submitBtn.unbind().bind('click', function () {
                if (init.loading) {
                    return false;
                }
                var params = {
                    phone: $.trim($('#js_input_phone').val()),
                    line: init.inputLineTarget.data('line'),
                    dept_date: $.trim($('#datetime').val()),
                    reason_pick: init.getPickItems(),
                    reason_content: $.trim($('#js_input_content').val())
                };

                if ($.isEmptyObject(params.line)) {
                    $.showToast($.string.TICKET_LINE_MUST);
                    return false;
                }

                if (!params.dept_date.length) {
                    $.showToast($.string.TICKET_DAY_TWO_MUST);
                    return false;
                }

                if (!params.reason_pick.length && !params.reason_content.length) {
                    $.showToast($.string.PICK_OR_CONTENT_MUST_ONE);
                    return false;
                }

                var status;
                status = $.checkInputVal({val:params.phone,type:'mobile',onChecked:function(val,state,hint){
                    if(state <= 0){
                        $.showToast(hint,false);
                    }
                }
                });
                if(status<=0){
                    return false;
                }
                init.loading = true;
                $.wpost($.httpProtocol.FEEDBACK, params, function (data) {
                    $.showToast($.string.FEEDBACK_SUCCESS);
                    setTimeout(function () {
                        wx.closeWindow();
                        init.loading = false;
                    },600);
                }, function () {
                    init.loading = false;
                })
            });
            /**
             * 选择线路
             */
            init.inputLineTarget.bind('focus', function () {
                init.showModal();
            });
            init.inputLineTarget.bind('input', function () {
                var text = $.trim($(this).val());
                init.searchLineByKeyEvent(text);
            });
            init.modal.on('click', '.js_line_item_option', function () {
                var line = $(this).data('line');
                init.inputLineTarget.val('{0} {1}'.format(line.line_code, line.line_name));
                init.inputLineTarget.data('line', line);
                console.log(line);
                init.hideModal();
            });
        },
        run : function () {
            init.initBtnEvent();
            init.initTimeSelectEvent();
        }
    };
    init.run();
})($);