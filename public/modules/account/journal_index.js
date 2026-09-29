
    (function($){
        "use strict";
        $(document).ready(function() {
            let _token = $('meta[name=_token]').attr('content') ;
            var baseUrl = $('#app_base_url').val();
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
                    url: $('#delete_url_1').val(),
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        toastr.success(response.message);
                        window.location.reload();
                        $('#pre-loader').addClass('d-none');
                    },
                    error: function(response) {
                        console.log(response);
                        $('#pre-loader').addClass('d-none');
                        toastr.error(response.message)
                    }
                });
            });
            var hidden_col = $('#hidden_list_tbl').val();
            if (hidden_col != '' && typeof hidden_col != "undefined") {
                var targets_col = JSON.parse(hidden_col);
            }else {
                var targets_col = [];
            }


            $("#reset-date-filter").on('click',function(){
                window.location.reload();
            });

            $(document).on('click', '.delete_approved_leadger', function(event){
                event.preventDefault();
                let id = $(this).data('id');
                $('#delete_approved_item_id').val(id);
                $('#deleteApprovedDeleteModal').modal('show');
            });
            $(document).on('submit', '#approved_voucher_delete_form', function(event) {
                event.preventDefault();
                $('#pre-loader').removeClass('d-none');
                $('#deleteApprovedDeleteModal').modal('hide');
                let formData = new FormData();
                formData.append('_token', _token);
                formData.append('id', $('#delete_approved_item_id').val());
                formData.append('password', $('#password').val());
                let id = $('#delete_approved_item_id').val();
                $.ajax({
                    url: $('#delete_url_2').val(),
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        toastr.success(response.message);
                        location.reload();
                        $('#pre-loader').addClass('d-none');
                    },
                    error: function(error) {
                        $('#pre-loader').addClass('d-none');
                        toastr.error(error.message)
                    }
                });
            });
        });

        $(document).on('click', '.undo_btn', function() {
            let _token = $('meta[name=_token]').attr('content') ;
            var voucher_id = $(this).data('id');
            var voucher_approval_url = $('#voucher_approval_url').val();
            var status = 0;
            $.post(voucher_approval_url, {_token:_token, id:voucher_id, status:status}, function(data){
                toastr.success(data.message);
                window.location.reload();
            });
        });
        
    })(jQuery);
