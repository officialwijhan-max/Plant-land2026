
(function($){
    "use strict";
    $(document).ready(function () {
        parentChartAccount();
        var baseUrl = $('#app_base_url').val();
        $(".debit_account_id").select2({
            ajax: {
                url: $('#get_accounts_for_payment').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            type: null
                        }
                        return query;
                },
                cache: false
            },
            escapeMarkup: function (m) {
                return m;
            }
        });

        $(".debit_sub_account_id").select2({
            ajax: {
                url: $('#sub_leadger_by_leadger').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            leadger_id: $('.debit_account_id').find(':selected').val()
                        }
                        return query;
                },
                cache: false
            },
            escapeMarkup: function (m) {
                return m;
            }
        });

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
            },
            escapeMarkup: function (m) {
                return m;
            }
        });

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
                            leadger_id: 0
                        }
                        return query;
                },
                cache: false
            },
            escapeMarkup: function (m) {
                return m;
            }
        });

        $(document).on('change', '.payment_to_sub', function(){
            var value = $( ".payment_to_sub option:selected" ).val();
            if (value == "create_new") {
                var value = $(this).find('option:selected').val();
                if (value == "create_new") {
                    $('#create_contact').modal('show');
                    var text = $(this).find('option:selected').text().split('->');
                    $('.contact_name').val(text[1].replace('</span>',''));
                    var row_count = $(this).data('rows');
                    $('#row_counts').val(row_count);
                }
            }
            else {
                let url = $('#get_parent_account').val();
                url = url.replace(":id", $( ".payment_to_sub option:selected" ).val());
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "JSON",
                    success: function (response) {
                        $(".leadger_name").val(response);
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }
        });

        $(document).on("submit", '#create_contact_form', function(event){
            event.preventDefault();
            let formData = $(this).serializeArray();
            $.each(formData, function (key, message) {
                $("#" + formData[key].name + "_error").html("");
            });
            $.ajax({
                url: $('#create_contact_form_url').val(),
                data: formData,
                type: "POST",
                success: function (response) {
                    let item = response.leadger;
                    $('.payment_to_sub').append($('<option>', {value: item.id,text: item.name}));
                    $('.payment_to_sub').val(item.id);
                    $('.payment_to_sub').trigger("change");
                    $('#create_contact').modal('hide');
                    $("#create_contact_form").trigger("reset");
                },
                error: function (error) {
                    if (error) {
                        $.each(error.responseJSON.errors, function (key, message) {
                            $("#" + key + "_error").html(message[0]);
                        });
                    }
                }
    
            });
        });
        
        $(document).on('change', '.debit_account_id', function(){
            var value = $( ".debit_account_id option:selected" ).val();
            if (value == "create_new") {
                $('#Item_Details').modal('show');
                var text = $( ".debit_account_id option:selected" ).text().split('->');
                $('#name').val(text[1].replace('</span>',''));
                parentChartAccount();
            }
        });
        function parentChartAccount(type = null, selected = null, editItem = null) {
            var accoutList = null;
            $.ajax({
                url: $('#cost_center_url').val(),
                type: "GET",
                dataType: "JSON",
                success: function (response) {
                    if (type) {
                        accoutList = response.filter(item => item.type == editItem)
                        $("#" + type).html("");
                    } else {
                        accoutList = response;
                        $("#parent_chart_account_list").html("");
                    }
                    let parent_chartAccount = '';
                    parent_chartAccount += `<select name="parent_id" class="primary_select primary-nice-select mb-15 parent_chart_account_list">`;
                    $.each(accoutList, function (key, item) {
                        if (selected && selected === item.id) {
                            parent_chartAccount += `<option selected value="${item.id}">${item.name}</option>`;
                        } else {
                            parent_chartAccount += `<option value="${item.id}">${item.name}</option>`;
                        }
                    });
                    parent_chartAccount += `<select>`;
                    if (type) {
                        $("#" + type).html(parent_chartAccount);
                    } else {
                        $("#parent_chart_account_list").html(parent_chartAccount);
                    }
                    $('.primary-nice-select').niceSelect();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        $(".as_sub_category").unbind().click(function () {
            $(".parent_chartAccount").toggle();
            $("#account_type").toggle();
        });
        $(document).on("submit", '#chart_account_form', function(event){
            event.preventDefault();
            let formData = $(this).serializeArray();
            $.each(formData, function (key, message) {
                $("#" + formData[key].name + "_error").html("");
            });
            $.ajax({
                url: $('#chart_account_form_url').val(),
                data: formData,
                type: "POST",
                success: function (response) {
                    let item = response.leadger
                    let row_count_response = response.row_count;
                    console.log(response)
                    $('.debit_account_id').append($('<option>', {value: item.id,text: item.name}));
                        if (row_count_response) {
                            $('select[data-row="'+row_count_response+'"]').val(item.id);
                        }
                        $('.debit_account_id').trigger("change");
                
                    $('#Item_Details').modal('hide');
                    $("#chart_account_form").trigger("reset");
                },
                error: function (error) {
                    if (error) {
                        $.each(error.responseJSON.errors, function (key, message) {
                            $("#" + key + "_error").html(message[0]);
                        });
                    }
                }

            });
        });
        
        $(".due_invoice_list").select2({
            ajax: {
                url: $('#get_due_invoice_list').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            leadger_id: $( ".payment_to_sub option:selected" ).val()
                        }
                        return query;
                },
                cache: false
            }
        });

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
            }
        });

        $(document).on('keyup','.sub_amount', function(){
            var values = $("input[name='sub_amount[]']").map(function(){return $(this).val();}).get();
            var sum = 0;
            for (var i = 0; i < values.length; i++) {
                sum = parseInt(sum) + parseInt(values[i]);
            }
            $('#total_sum').val(sum);
        });

        var typingTimer;                //timer identifier
        var doneTypingInterval = 2000;  //time in ms, 5 seconds for example
        var $input = $('.discount_percentage');
        var $main_input = $('.sub_amount');

        //on keyup, start the countdown
        $input.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        //on keydown, clear the countdown 
        $input.on('keydown', function () {
            clearTimeout(typingTimer);
        });

        $main_input.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        //on keydown, clear the countdown 
        $main_input.on('keydown', function () {
            clearTimeout(typingTimer);
        });

        //user is "finished typing," do something
        function doneTyping () {
            let has_invoice = $('.due_invoice_list').val();
            if (has_invoice > 0) {
                let discount_amount = 0;
                let total_discount_amount = 0;
                let discount = parseFloat($('.discount_percentage').val());
                
                $.each($('.sub_amount'), function (index, value) {
                    let amount = $(this).val();
                    discount_amount = amount * discount / 100;
                    total_discount_amount += discount_amount;
                    amount = $(this).val(parseFloat(amount - discount_amount).toFixed(2));
                });
                $('.discount_amount').val(total_discount_amount);
            }
        }
    });

})(jQuery);
