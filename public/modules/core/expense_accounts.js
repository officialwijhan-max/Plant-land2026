
(function ($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        getAccountBasedOnType();
        getCustomers();
        getAccountBasedOnConfigurationGroup();
    });

})(jQuery);


function getAccountBasedOnType()
{
    $(".payment_to").select2({
        ajax: {
            url: $('#expense_account_select_list_option').val(),
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                    page: params.page || 1,
                    type: $('#expense_account_select_list_option').attr('data-type')
                }
                return query;
            },
            cache: false
        }
    });
}
function getCustomers()
{
    $(".customer_to").select2({
        ajax: {
            url: $('#get_customers').val(),
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                    page: params.page || 1,
                    type: $('#get_customers').attr('data-type')
                }
                return query;
            },
            cache: false
        }
    });
}
function getAccountBasedOnConfigurationGroup()
{
    $(".payment_from").select2({
        ajax: {
            url: $('#select_based_on_configuration_group').val(),
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                    page: params.page || 1,
                    configuration_group_id: $('#payment_from_type').val()
                }
                return query;
            },
            cache: false
        }
    });
}