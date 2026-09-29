
(function($){
    "use strict";
    $(document).ready(function () {
        var baseUrl = $('#app_base_url').val();
        var dtbl_url = $('#dtbl_url').val();
        var delete_url = $('#delete_url').val();
        let _token = $('meta[name=_token]').attr('content');
        var hidden_col = $('#hidden_list_tbl').val();
        if (hidden_col != '' && typeof hidden_col != "undefined") {
            var targets_col = JSON.parse(hidden_col);
        }else {
            var targets_col = [];
        }

        $(document).on('click', '.delete_item', function(event){
            event.preventDefault();
            let id = $(this).data('id');
            $('#delete_item_id').val(id);
            $('#deleteItemModal').modal('show');
        });

        $(document).on('submit', '#item_delete_form', function(event) {
            event.preventDefault();
            $('#pre-loader').removeClass('d-none');
            $('#deleteItemModal').modal('hide');
            let formData = new FormData();
            formData.append('_token', _token);
            formData.append('id', $('#delete_item_id').val());
            let id = $('#delete_item_id').val();
            let table = $('.Crm_table_active_3').DataTable();
            $.ajax({
                url: delete_url,
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    toastr.success(response.message)
                    window.location.reload();
                    $('#pre-loader').addClass('d-none');
                },
                error: function(response) {
                    console.log(response);
                    toastr.error(response.message)
                    $('#pre-loader').addClass('d-none');
                }
            });
        });
        $(document).on('click','.rename_account', function(){
            $('#RenameAccount').modal('show');
            $('.account_id').val($(this).attr("data-id"));
            $('.name').val($(this).attr("data-name"));
            $('.code').val($(this).attr("data-code"));
            if ($(this).attr("data-is-active") == 1) {
                $(".active").attr("checked", true);
            } else {
                $(".de_active").attr("checked", true);
            }
            leadgerAccount("edit_leadger_account_list", $(this).attr("data-leadger-id"),$(this).attr("data-type"));
        });

        $("#chart_account_rename_form").on("submit", function (event) {
            event.preventDefault();
            let formData = $(this).serializeArray();
            $.each(formData, function (key, message) {
                $("#" + formData[key].name + "_error").html("");
            });
            let table = $('.Crm_table_active_3').DataTable();
            $.ajax({
                url: baseUrl + "/account/sub-leadger/update-info",
                data: formData,
                type: "POST",
                success: function (response) {
                    $("#RenameAccount").modal("hide");
                    $("#chart_account_rename_form").trigger("reset");
                    $(".parent_chartAccount").hide();
                    table.clearPipeline();
                    table.ajax.reload();
                    toastr.success(response.message);
                },
                error: function (error) {
                    if (error) {
                        $.each(error.responseJSON.errors, function (key, message) {
                            $("#edit_" + key + "_error").html(message[0]);
                        });
                    }
                    toastr.warning(response.message);
                }

            });
        });
        function leadgerAccount(type = null, selected = null, editItem = null) {
            $('#pre-loader').removeClass('d-none');
            var accoutList = null;
            $.ajax({
                url: $('#get_leadger_data_list').val(),
                type: "GET",
                dataType: "JSON",
                success: function (response) {
                    if (type) {
                        accoutList = response;
                        $("#" + type).html("");
                    } else {
                        accoutList = response;
                        $("#leadger_account_list").html("");
                    }
                    let parent_chartAccount = '';
                    parent_chartAccount += `<select name="leadger_id" class="primary_select mb-15 leadger_account_list">`;
                    $.each(accoutList, function (key, item) {
                        if (selected && selected == item.id) {
                            console.log(selected);
                            parent_chartAccount += `<option selected value="${item.id}">${item.name}</option>`;
                        } else {
                            console.log("else");
                            parent_chartAccount += `<option value="${item.id}">${item.name}</option>`;
                        }
                    });
                    parent_chartAccount += `<select>`;
                    if (type) {
                        $("#" + type).html(parent_chartAccount);
                    } else {
                        $("#leadger_account_list").html(parent_chartAccount);
                    }
                    $('select').niceSelect();
                    $('#pre-loader').addClass('d-none');
                },
                error: function (error) {
                    console.log(error);
                    $('#pre-loader').addClass('d-none');
                }
            });
        }
    });

})(jQuery);
