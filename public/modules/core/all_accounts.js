
(function ($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        getAccount();
    });

})(jQuery);


function getAccount()
{
    $(".account_id").select2({
        ajax: {
            url: $('#account_select_list_option').val(),
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
}