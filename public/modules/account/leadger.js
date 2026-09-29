(function($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content') ;
    $(document).ready(function(){
        var baseUrl = $('#app_base_url').val();

        parentChartAccount();
        parentChartAccountEdit();
        function chartAccountList() {
            $.ajax({
                url: baseUrl + "/account/leadger-list",
                type: "GET",
                dataType: "HTML",
                success: function (response) {
                    $("#chart_account_list").html(response);
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

        $(document).on('click', '.as_sub_category_edit', function () {
            $(".parent_chartAccountEdit").toggle();
        });

        function parentChartAccount(type = null, selected = null, editItem = null) {
            var accoutList = null;
            $.ajax({
                url: baseUrl + "/account/leadger-list-cost-center",
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
                    parent_chartAccount += `<select name="parent_id" class="primary_select mb-15 parent_chart_account_list">`;
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
                    $('select').niceSelect();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        function parentChartAccountEdit(type = null, selected = null, editItem = null) {
            var accoutList = null;
            $.ajax({
                url: baseUrl + "/account/leadger-list-cost-center",
                type: "GET",
                dataType: "JSON",
                success: function (response) {
                    if (type) {
                        accoutList = response.filter(item => item.type == editItem)
                        $("#" + type).html("");
                    } else {
                        accoutList = response;
                        $("#parent_chart_account_list_edit").html("");
                    }
                    let parent_chartAccount = '';
                    parent_chartAccount += `<select name="parent_id" class="primary_select mb-15 parent_chart_account_list_edit">`;
                    $.each(accoutList, function (key, item) {
                        if (selected && selected == item.id) {
                            parent_chartAccount += `<option selected value="${item.id}">${item.name}</option>`;
                        } else {
                            parent_chartAccount += `<option value="${item.id}">${item.name}</option>`;
                        }
                    });
                    parent_chartAccount += `<select>`;
                    if (type) {
                        $("#" + type).html(parent_chartAccount);
                    } else {
                        $("#parent_chart_account_list_edit").html(parent_chartAccount);
                    }
                    $('select').niceSelect();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        $("#chart_account_form").on("submit", function (event) {
            $('.submit_button_form').prop('disabled', true);
            event.preventDefault();
            $('#pre-loader').removeClass('d-none');
            let formData = $(this).serializeArray();
            $.each(formData, function (key, message) {
                $("#" + formData[key].name + "_error").html("");
            });
            $.ajax({
                url: baseUrl + "/account/leadger-store",
                data: formData,
                type: "POST",
                success: function (response) {
                    $('.submit_button_form').prop('disabled', false);
                    $("#Item_Details").modal("hide");
                    $("#chart_account_form").trigger("reset");
                    $('.a_type').val(1);
                    $('.a_type').trigger("change");
                    $('.a_type').niceSelect('update');
                    $(".parent_chartAccount").hide();
                    chartAccountList();
                    parentChartAccount();
                    parentChartAccountEdit();
                    page = 1;
                    toastr.success(response.message);
                    $('#pre-loader').addClass('d-none');
                },
                error: function (error) {
                    $('.submit_button_form').prop('disabled', false);
                    if (error) {
                        $.each(error.responseJSON.errors, function (key, message) {
                            $("#" + key + "_error").html(message[0]);
                        });
                    }
                    $('#pre-loader').addClass('d-none');
                    toastr.warning("Something went wrong");
                }

            });
        });

        $(document).on('click','.edit_leadger', function(){
            let url = $('#edit_url').val();
            let id = $(this).data("id");
            url = url.replace(":id", id)
            
            $.get(url, {_token:'{{ csrf_token() }}', id:id}, function(data){
                $('#edit_form').html(data);
                $('#RenameAccount').modal('show');
                let selected_parent_id = $('#selected_parent_id').val();
                console.log(selected_parent_id)
                parentChartAccountEdit(null, selected_parent_id, null);
            });
        });

        $("#chart_account_rename_form").on("submit", function (event) {
            event.preventDefault();
            $('#pre-loader').removeClass('d-none');
            let formData = $(this).serializeArray();
            $.each(formData, function (key, message) {
                $("#" + formData[key].name + "_error").html("");
            });
            $.ajax({
                url: baseUrl + "/account/leadger/update-info",
                data: formData,
                type: "POST",
                success: function (response) {
                    $("#RenameAccount").modal("hide");
                    $("#chart_account_rename_form").trigger("reset");
                    $(".parent_chartAccount").hide();
                    chartAccountList();
                    parentChartAccount();
                    parentChartAccountEdit();
                    page = 1;
                    toastr.success(response.message);
                    $('#pre-loader').addClass('d-none');
                },
                error: function (error) {
                    if (error) {
                        $.each(error.responseJSON.errors, function (key, message) {
                            $("#edit_" + key + "_error").html(message[0]);
                        });
                    }
                    toastr.warning("Something went wrong");
                    $('#pre-loader').addClass('d-none');
                }

            });
        });


        $(document).on('click', '.delete_leadger', function(event){
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
            $.ajax({
                url: baseUrl + "/account/leadger/delete",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    chartAccountList();
                    parentChartAccount();
                    parentChartAccountEdit();
                    toastr.success(response.message)
                    $('#pre-loader').addClass('d-none');
                },
                error: function(response) {
                    console.log(response);
                    toastr.error("{{__('common.Something Went Wrong')}}")
                    $('#pre-loader').addClass('d-none');
                }
            });
        });

        var new_url = '/account/leadger?page=';
        var page = 2;
        infinteLoadMore(page);

        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                page++;
                infinteLoadMore(page);
            }
        });

        function infinteLoadMore(page) {
            $.ajax({
                    url: baseUrl + new_url + page,
                    datatype: "html",
                    type: "get",
                    beforeSend: function () {
                        $('.auto-load').show();
                    }
                })
                .done(function (response) {
                    if (response.length == 0) {
                        toastr.warning('No More Data to show');
                        return;
                    }
                    $('.auto-load').hide();
                    $("#datas").append(response);
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log('Server error occured');
                });
        }
    });
})(jQuery);
