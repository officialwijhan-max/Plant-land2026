
(function ($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        getStaffList();
    });

})(jQuery);


function getStaffList()
{
    $(".user_staff").select2({
        ajax: {
            url: $('#user_select_list_option').val(),
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