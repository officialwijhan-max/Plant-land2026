
(function ($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        $(".city_id").select2({
            ajax: {
                url: $('#city_list_select_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
                        state_id: $('.state_id').find(':selected').val(),
                        type: 0
                    }
                    return query;
                },
                cache: false
            }
        });
    });

})(jQuery);
