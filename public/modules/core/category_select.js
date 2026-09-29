
(function ($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        getCategoryList()
    });

    $(document).ready(function () {
        $(".sub_category").select2({
            ajax: {
                url: $('#sub_category_list_select_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
                        category_id: $('#category_id').find(':selected').val()
                    }
                    return query;
                },
                cache: false
            },
            escapeMarkup: function (m) {
                return m;
            }
        });
    });

})(jQuery);



function getCategoryList() {
    $(".category").select2({
        ajax: {
            url: $('#category_list_select_option').val(),
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                    page: params.page || 1,
                    type: 0
                }
                return query;
            },
            cache: false
        },
        escapeMarkup: function (m) {
            return m;
        }
    });
}