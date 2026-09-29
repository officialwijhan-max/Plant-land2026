
(function($){
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        $(".customer_id").select2({
            ajax: {
                url: $('#customer_list_select_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 0,
                data: function (params) {
                        var query = {
                            search: params.term,
                            company: $('.company_id').find(':selected').val(),
                            page: params.page || 1,
                        }
                        return query;
                },
                cache: false
            }
        });

        $("#supplier_id").select2({
            ajax: {
                url: $('#supplier_list_select_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 0,
                data: function (params) {
                        var query = {
                            search: params.term,
                            company: $('.company_id').find(':selected').val(),
                            page: params.page || 1,
                        }
                        return query;
                },
                cache: false
            }
        });
        
    });
})(jQuery);
