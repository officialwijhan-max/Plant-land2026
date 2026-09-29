(function($){
    "use strict";
    $(document).ready(function () {

        var search_url = $('#search_url').val();
        if (search_url == 0) {
            var new_url = '/account/transactions?page=';
        }else {
            var new_url = '/account/transactions/search?' + search_url + '&page=';
        }
        var ENDPOINT = $('#app_base_url').val();
        var page = 1;

        if (page == 1) {
            $('.QA_table').addClass('d-none');
        }
        
        infinteLoadMore(page);

        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                page++;
                infinteLoadMore(page);
            }
        });

        function infinteLoadMore(page) {
            $.ajax({
                    url: ENDPOINT + new_url + page,
                    datatype: "html",
                    type: "get",
                    beforeSend: function () {
                        $('.auto-load').show();
                    }
                })
                .done(function (response) {
                    if (response.length == 0) {
                        toastr.warning('No More Data to show');
                        if (page == 1) {
                            $('.QA_table').addClass('d-none');
                        }
                        return;
                    }
                    $('.auto-load').hide();
                    $("#datas").find('.last_total_tr').remove();
                    $("#datas").append(response);
                    var last_row = '<tr class="last_total_tr"><td colspan="4" class="text-right">Total :</td><td class="debit_total"></td><td class="credit_total"></td></tr>';
                    $("#datas").append(last_row);
                    sumDebitCredit();
                    $('.QA_table').removeClass('d-none');
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log('Server error occured');
                });
        }

        function sumDebitCredit()
        {
            var debit_amounts = $("input[name='dr_amount[]']").map(function(){return $(this).val();}).get();
            var credit_amounts = $("input[name='cr_amount[]']").map(function(){return $(this).val();}).get();
            var debit_amounts_sum = 0;
            var credit_amounts_sum = 0;
            for (var i = 0; i < debit_amounts.length; i++) {
                if (debit_amounts[i] != '') {
                    debit_amounts_sum = parseFloat(debit_amounts_sum) + parseFloat(debit_amounts[i]);
                }
            }
            for (var i = 0; i < credit_amounts.length; i++) {
                if (credit_amounts[i] != '') {
                    credit_amounts_sum = parseFloat(credit_amounts_sum) + parseFloat(credit_amounts[i]);
                }
            }
            var currency = $('#currency_symbol').val();
            $('.debit_total').text(currency + ' ' +parseFloat(debit_amounts_sum));
            $('.credit_total').text(currency + ' ' +parseFloat(credit_amounts_sum));
        }
    });

})(jQuery);
