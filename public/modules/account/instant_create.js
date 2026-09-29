
(function($){
    "use strict";
    $(document).ready(function(){
        parentChartAccount();
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
                let class_name = response.class_name;
                if (class_name == ".credit_account_id") {
                    $('.credit_account_id').append($('<option>', {value: item.id,text: item.name}));
                    $('.credit_account_id').val(item.id);
                    $('.credit_account_id').trigger("change");
                }else {
                    $('.account_id').append($('<option>', {value: item.id,text: item.name}));
                    if (row_count_response) {
                        $('select[data-row="'+row_count_response+'"]').val(item.id);
                    }
                    $('.account_id').trigger("change");
                }
               
                $('#Item_Details').modal('hide');
                $("#chart_account_form").trigger("reset");
                toastr.success('Added Successfully')
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
                let row_count_response = response.row_count;
                $('.sub_account_id').append($('<option>', {value: item.id,text: item.name}));
                // $('.sub_account_id').val(item.id);
                if (row_count_response) {
                    $('select[data-rows="'+row_count_response+'"]').val(item.id);
                }
                $('.sub_account_id').trigger("change");
                $('#create_contact').modal('hide');
                $("#create_contact_form").trigger("reset");
                toastr.success('Added Successfully')
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

    $(document).on('change', '.account_id', function(){
        var value = $(this).find('option:selected').val();
        if (value == "create_new") {
            $('#Item_Details').modal('show');
            var text = $(this).find('option:selected').text().split('->');
            $('#name').val(text[1].replace('</span>',''))
            var row_count = $(this).data('row');
            $('#row_count').val(row_count)
            parentChartAccount();
        }
    });

    $(document).on('change', '.credit_account_id', function(){
        var value = $(this).find('option:selected').val();
        if (value == "create_new") {
            $('#Item_Details').modal('show');
            var text = $(this).find('option:selected').text().split('->');
            $('#name').val(text[1].replace('</span>',''))
            $('#row_count').val(null)
            $('#class_name').val('.credit_account_id')
            parentChartAccount();
        }
    });

    $(document).on('change', '.sub_account_id', function(){
        var value = $(this).find('option:selected').val();
        if (value == "create_new") {
            $('#create_contact').modal('show');
            var text = $(this).find('option:selected').text().split('->');
            $('.contact_name').val(text[1].replace('</span>',''));
            var row_count = $(this).data('rows');
            $('#row_counts').val(row_count);
        }
    });
})(jQuery);
