
(function($){
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        $(".normal_product").select2({
            ajax: {
                url: $('#product_select_list_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 0,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            house: $('.house').find(':selected').val(),
                            purpose_filter: $('#purpose_filter').val(),
                        }
                        return query;
                },
                cache: false
            },
            placeholder: "Choose Product",
        });
        
        $(".service").select2({
            ajax: {
                url: $('#service_select_list_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 0,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                        }
                        return query;
                },
                cache: false
            },
            placeholder: "Choose Service",
        });
    });

})(jQuery);
