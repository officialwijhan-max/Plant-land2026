
(function($){
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    var row = (parseInt($('.new_added_row').length) > 0) ? parseInt($('.new_added_row').length) : 1;
    $(document).ready(function () {
        getMainAccount();
        getPartners();
        getCashFlowAccounts();
        getSumAmount();
        var leadger_array = [];

        function getMainAccount(){
            $(".account_id").select2({
                ajax: {
                    url: $('#leadger_list_select_option').val(),
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                            var query = {
                                search: params.term,
                                page: params.page || 1,
                                type: $('.journal_type').find(':selected').val()
                            }
                            return query;
                    },
                    cache: false
                },
                escapeMarkup: function (m) {
                    return m;
                }
            });
        }

        function getPartners(){
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
                            }
                            return query;
                    },
                    cache: false
                },
                escapeMarkup: function (m) {
                    return m;
                }
            });
        }

        function getCashFlowAccounts(){
            $(".cash_flow_account").select2({
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
        }

        $(document).on('click', '.is_cashflow_journal', function(){
            if ($("#is_cashflow_journal_yes").is(':checked')) {
                $('.red_input').removeClass('d-none');
            }else {
                $('.red_input').addClass('d-none');
            }
        });

        $(document).on('click', '#refresh_btn', function(e){
            e.preventDefault();
            window.location.replace($('#create_url').val());
        });

        $(document).on('click', '#add_new_line', function(e){
            e.preventDefault();
            row += 1;
            $.ajax({
                url: $('#add_new_entry').val(),
                type: "GET",
                dataType: "JSON",
                data: {row:row},
                success: function (response) {
                    $(".entry_row_div").append(response);
                    getMainAccount();
                    getPartners();
                    getCashFlowAccounts();
                    sumAmount();
                    var last_append = $('.entry_row_div').children().last();
                    last_append.find("#narration").val($('.narration').val());
                    $('#pre-loader').addClass('d-none');
                },
                error: function (error) {
                    console.log(error);
                    $('#pre-loader').addClass('d-none');
                }
            });
        });

        $(document).on('keyup', '.debit_amount', function(){
            var div = $(this).closest(".new_added_row");
            div.find(".credit_amount").val(0);
            getSumAmount();
        });

        $(document).on('keyup', '.credit_amount', function(){
            var div = $(this).closest(".new_added_row");
            div.find(".debit_amount").val(0);
            getSumAmount();
        });

        function sumAmount()
        {
            var debit_amounts = $("input[name='debit_amount[]']").map(function(){return $(this).val();}).get();
            var credit_amounts = $("input[name='credit_amount[]']").map(function(){return $(this).val();}).get();
            var debit_amounts_sum = 0;
            var credit_amounts_sum = 0;
            for (var i = 0; i < debit_amounts.length; i++) {
                debit_amounts_sum = parseFloat(debit_amounts_sum) + parseFloat(debit_amounts[i]);
            }
            for (var i = 0; i < credit_amounts.length; i++) {
                credit_amounts_sum = parseFloat(credit_amounts_sum) + parseFloat(credit_amounts[i]);
            }
            if (parseFloat(credit_amounts_sum) > parseFloat(debit_amounts_sum)) {
                var last_div = $('.entry_row_div .new_added_row:last-child');
                last_div.find(".debit_amount").val(parseFloat(credit_amounts_sum) - parseFloat(debit_amounts_sum));
            }
            if (parseFloat(debit_amounts_sum) > parseFloat(credit_amounts_sum)) {
                var last_div = $('.entry_row_div .new_added_row:last-child');
                last_div.find(".credit_amount").val(parseFloat(debit_amounts_sum) - parseFloat(credit_amounts_sum));
            }

            getSumAmount();
        }

        function getSumAmount()
        {
            var debit_amounts = $("input[name='debit_amount[]']").map(function(){return $(this).val();}).get();
            var credit_amounts = $("input[name='credit_amount[]']").map(function(){return $(this).val();}).get();
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
            $('.total_debit_amount').text(currency+' '+parseFloat(debit_amounts_sum));
            $('.total_credit_amount').text(currency+' '+parseFloat(credit_amounts_sum));
        }

        $(document).on('click', '.delete_new_row', function(e){
            e.preventDefault();
            $(this).closest(".new_added_row").remove();
            getSumAmount();
        });

        $(document).on('submit', '.journal_create_form', function(event){

            event.preventDefault();
            go_for_form_submit();
        });

        function go_for_form_submit(){
            let formElement = $('.journal_create_form').serializeArray()
            let formData = new FormData();
            formElement.forEach(element => {
                formData.append(element.name,element.value);
            });
            formData.append('_token',_token);
            $.ajax({
                url: $('#journal_store_url').val(),
                type:"POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                    if(response.message_warning !== undefined)
                    {
                        toastr.warning(response.message_warning);
                    }else if (response.message !== undefined) {
                        $(".entry_row_div").html("");
                        $(".narration").val("");
                        toastr.success(response.message);
                        window.location.replace(response.url);
                    }else {
                        toastr.error("Something went wrong");
                    }
                },
                error:function(response) {
                    if (response.message_error !== undefined) {
                        toastr.error(response.message_error)
                    }else {
                        toastr.error("Something went wrong");
                    }
                }
            });
        }

        $(document).on('submit', '.journal_update_form', function(event){

            event.preventDefault();
            go_for_update_form_submit();
        });

        function go_for_update_form_submit(){
            let formElement = $('.journal_update_form').serializeArray();
            let id = $('#rowId').val();
            let url = $('#journal_update_url').val();
            url = url.replace(':id',id);
            let formData = new FormData();
            formElement.forEach(element => {
                formData.append(element.name,element.value);
            });
            formData.append('_token',_token);
            $.ajax({
                url: url,
                type:"POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                    if(response.message_warning !== undefined)
                    {
                        toastr.warning(response.message_warning);
                    }else if (response.message !== undefined) {
                        toastr.success(response.message);
                        window.location.replace(response.url);
                    }else {
                        toastr.error("Something went wrong");
                    }
                },
                error:function(response) {
                    toastr.error(response.message_error)
                }
            });
        }
    });

})(jQuery);
