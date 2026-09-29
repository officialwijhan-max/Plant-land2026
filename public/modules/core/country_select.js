
(function ($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        $(".country_id").select2({
            ajax: {
                url: $('#country_list_select_option').val(),
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
            }
        });
    });

})(jQuery);
