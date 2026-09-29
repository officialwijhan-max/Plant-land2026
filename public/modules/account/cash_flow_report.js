
(function($){
    "use strict";
    $(document).ready(function () {
        let _token = $('meta[name=_token]').attr('content') ;
        $(".account_id").select2({
            ajax: {
                url: $('#cash_flow_list_select_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1
                        }
                        return query;
                },
                cache: false
            },
            escapeMarkup: function (m) {
                return m;
            }
        });
        var new_url = window.location.href+'&page=';
        var page = 2;
        infinteLoadMore(page);

        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                page++;
                infinteLoadMore(page);
            }
        });

        function infinteLoadMore(page) {
            $.ajax({
                    url: new_url + page,
                    datatype: "html",
                    type: "get",
                    beforeSend: function () {
                        $('.auto-load').show();
                    }
                })
                .done(function (response) {
                    if (response.length == 0) {
                        toastr.warning('No More Data to show');
                        return;
                    }
                    $('.auto-load').hide();
                    $("#datas").append(response);
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log('Server error occured');
                });
        }
    });

})(jQuery);
