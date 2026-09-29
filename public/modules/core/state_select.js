
(function ($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        $(".state_id").select2({
            ajax: {
                url: $('#state_list_select_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
                        country_id: $('.country_id').find(':selected').val(),
                        type: 0
                    }
                    return query;
                },
                cache: false
            }
        });
    });

})(jQuery);
