
(function($){
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        $("#product_info").select2({
            ajax: {
                url: $('#products_list_select_option').val(),
                type: "POST",
                dataType: 'json',
                delay: 0,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            brand_id: $('.brand').find(':selected').val(),
                            model_id: $('.model').find(':selected').val(),
                            category_id: 0,
                        }
                        return query;
                },
                cache: false
            },
            placeholder: "Choose Product",
        });
    });

})(jQuery);
