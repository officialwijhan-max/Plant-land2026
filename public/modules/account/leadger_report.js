
(function($){
    "use strict";
    $(document).ready(function () {
        let _token = $('meta[name=_token]').attr('content') ;
        $(".account_id").select2({
            ajax: {
                url: $('#get_leadger_list_url').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            type: null
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
