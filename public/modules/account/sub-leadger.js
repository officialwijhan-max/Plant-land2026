(function($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content') ;
    $(document).ready(function(){
        leadgerAccount();
        var baseUrl = $('#app_base_url').val();
        console.log(baseUrl);
        function chartAccountList() {
            $.ajax({
                url: baseUrl + "/account/sub-leadger-list",
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

        $("#chart_account_form").on("submit", function (event) {
            event.preventDefault();
            $('#pre-loader').removeClass('d-none');
            let formData = $(this).serializeArray();
            $.each(formData, function (key, message) {
                $("#" + formData[key].name + "_error").html("");
            });
            $.ajax({
                url: baseUrl + "/account/sub-leadger-store",
                data: formData,
                type: "POST",
                success: function (response) {
                    $("#Item_Details").modal("hide");
                    $("#chart_account_form").trigger("reset");
                    $(".parent_chartAccount").hide();
                    chartAccountList();
                    page = 1;
                    toastr.success(response.message);
                    $('#pre-loader').addClass('d-none');
                },
                error: function (error) {
                    if (error) {
                        $.each(error.responseJSON.errors, function (key, message) {
                            $("#" + key + "_error").html(message[0]);
                        });
                    }
                    toastr.warning(response.message);
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
            $.ajax({
                url: baseUrl + "/account/sub-leadger/update-info",
                data: formData,
                type: "POST",
                success: function (response) {
                    $("#RenameAccount").modal("hide");
                    $("#chart_account_rename_form").trigger("reset");
                    $(".parent_chartAccount").hide();
                    chartAccountList();
                    page = 1;
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


        $(document).on('click', '.delete_leadger', function(event){
            event.preventDefault();
            let id = $(this).data('id');
            $('#delete_item_id').val(id);
            $('#deleteItemModal').modal('show');
        });

        $(document).on('submit', '#item_delete_form', function(event) {
            event.preventDefault();
            $('#deleteItemModal').modal('hide');
            let formData = new FormData();
            formData.append('_token', _token);
            formData.append('id', $('#delete_item_id').val());
            let id = $('#delete_item_id').val();
            $.ajax({
                url: baseUrl + "/account/sub-leadger/delete",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    chartAccountList();
                    toastr.success(response.message)
                },
                error: function(response) {
                    console.log(response);
                    toastr.error(response.message)
                }
            });
        });
        var new_url = '/account/sub-leadger?page=';
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
