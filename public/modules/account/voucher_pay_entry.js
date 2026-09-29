
(function($){
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        getPaymentToMain();
        paymentToSub(0);

        $(".credit_account_id").select2({
            ajax: {
                url: $('#get_accounts_for_payment').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            type: $("#payment_from_type_1").is(':checked') ? "cash" : "bank"
                        }
                        return query;
                },
                cache: false
            }
        });
        $(".sub_account_id").select2({
            ajax: {
                url: $('#sub_leadger_by_leadger').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            leadger_id: $('.credit_account_id').find(':selected').val()
                        }
                        return query;
                },
                cache: false
            }
        });

        function getPaymentToMain()
        {
            $(".payment_to_main").select2({
                ajax: {
                    url: $('#leadger_list_select_option').val(),
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
                }
            });
        }

        $(document).on('change', '.payment_to_main', function(){
            $('.payment_to_sub').find(':selected').val(0);
            paymentToSub(this.value);
        });

        function paymentToSub(main_leager_id)
        {
            $(".payment_to_sub").select2({
                ajax: {
                    url: $('#sub_leadger_by_leadger').val(),
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                            var query = {
                                search: params.term,
                                page: params.page || 1,
                                leadger_id: main_leager_id
                            }
                            return query;
                    },
                    cache: false
                }
            });
        }

        $(document).on('click', '#add_payment_to_form', function(e){
            e.preventDefault();
            $('#pre-loader').removeClass('d-none');
            var leadger_id = $('.payment_to_main').find(':selected').val();
            var sub_leadger_id = $('.payment_to_sub').find(':selected').val();
            var amount = $('.sub_amount').val();
            let myformData = new FormData();
            myformData.append('_token', _token);
            myformData.append('leadger_id', leadger_id);
            myformData.append('sub_leadger_id', sub_leadger_id);
            myformData.append('amount', amount);
            $.ajax({
                url: $('#get_tbl_row_data').val(),
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: myformData,
                success: function (response) {
                    $(".details_div").append(response);
                    sumAmount();
                    $('#pre-loader').addClass('d-none');
                },
                error: function (error) {
                    console.log(error);
                    $('#pre-loader').addClass('d-none');
                }
            });
        });

        $(document).on('click', '.delete_item_row', function(e){
            e.preventDefault();
            $(this).closest(".below_div_select").remove();
            sumAmount();
        });

        function sumAmount()
        {
            var values = $("input[name='sub_amount[]']").map(function(){return $(this).val();}).get();
            var sum = 0;
            for (var i = 0; i < values.length; i++) {
                sum = parseInt(sum) + parseInt(values[i]);
            }
            $('.total_txt_right').html(sum);
        }
    });

})(jQuery);
