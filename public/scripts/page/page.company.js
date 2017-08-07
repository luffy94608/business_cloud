/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        section : '#js_register_section',
        submitBtn : $('#js_input_submit'),


        inputTime :$('#js_input_time'),
        inputCompanyName: $('#js_input_company'),
        inputCompanyArea: $('#js_input_company_area'),
        inputCompanyIndustry: $('#js_input_company_industry'),
        inputFollowIndustry: $('#js_input_follow_industry'),
        followAreaListSection : $('#js_follow_area_list'),
        keywordListSection : $('#js_follow_keyword_list'),
        followAreaAddBtn : $('#js_follow_area_add_btn'),
        keywordAddBtn : $('#js_keyword_add_btn'),
        keywordInput : $('#js_keyword_input'),
        loading :false,
        tpl :'<span class="bck-item active" data-id="{0}">{1}<i class="b-icon-close ml-5"></i></span>',

        /**
         * 表单验证
         * @returns {*}
         */
        initParams : function () {
            var params = {
                time:$.trim(init.inputTime.val()),
                follow_industry:$.trim(init.inputFollowIndustry.val()),
                follow_area:init.getItemArrById(init.followAreaListSection).join(','),
                follow_keyword:init.getItemArrById(init.keywordListSection).join(',')
            };

            if (params.follow_area.length<1) {
                $.showToast($.string.AREA_MUST_ADD, false);
                return false;
            }

            if (params.follow_keyword.length<1) {
                $.showToast($.string.KEYWORD_MUST_ADD, false);
                return false;
            }

            return params;
        },
        /**
         * 获取结果集
         * @param target
         * @returns {Array}
         */
        getItemArrById : function (target) {
            var list = [];
            target.find('.bck-item ').each(function (index, item) {
                var id = $(item).data('id');
                list.push(id);
            });
            return list;
        },

        initBtnEvent : function () {

            /**
             * 注册
             */
            init.submitBtn.unbind().bind('click',function () {
                if(init.loading){
                    return false;
                }
                var params = init.initParams();
                if(!params){
                    return false;
                }
                init.loading = true;
                $.wpost($.httpProtocol.USER_COMPANY,params,function (data) {
                    $.showToast($.string.COMPANY_OR_BUSINESS_SUCCESS, true);
                    // $.locationUrl('/login');
                    setTimeout(function () {
                        window.location.reload();
                        init.loading = false;
                    }, 400);
                },function () {
                    init.loading = false;
                })
            });

            /**
             * 关键词添加
             */
            init.keywordAddBtn.unbind().bind('click', function () {
                var keyword = $.trim(init.keywordInput.val());
                if (keyword.length<1) {
                    $.showToast($.string.KEYWORD_MUST,false);
                    return false;
                }
                var arr = init.getItemArrById(init.keywordListSection);
                if (arr.indexOf(keyword)!==-1) {
                    $.showToast($.string.KEYWORD_EXISTS,false);
                    return false;
                }

                var html = init.tpl.format(keyword, keyword);
                init.keywordListSection.append(html)

            });

            /**
             * 关注区域添加
             */
            init.followAreaAddBtn.bind('change', function () {
                var val = parseInt($.trim($(this).val()));
                var text = $.trim($(this).find('option:checked').text());
                var arr = init.getItemArrById(init.followAreaListSection);
                if (arr.indexOf(val)!==-1) {
                    $.showToast($.string.AREA_EXISTS,false);
                    return false;
                }
                var html = init.tpl.format(val, text);
                init.followAreaListSection.append(html)

            });

            /**
             * 删除事件
             */
            $(document).on('click', '.bck-item .b-icon-close',function () {
                $(this).parents('.bck-item').remove();
            });
        },
        run : function () {
            init.initBtnEvent();
            $('select').select2();
        }
    };
    init.run();

})($);