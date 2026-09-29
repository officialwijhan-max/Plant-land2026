
(function ($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        $(".brand").select2({
            ajax: {
                url: $('#brand_list_select_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
                    }
                    return query;
                },
                cache: false
            }
        });
    });

})(jQuery);
