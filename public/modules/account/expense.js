(function($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content') ;
    $(document).ready(function(){
        $(document).on('submit', '#create_cash_flow', function(event){
            event.preventDefault();
            let formElement = $(this).serializeArray()
            let formData = new FormData();
            formElement.forEach(element => {
                formData.append(element.name,element.value);
            });
            formData.append('_token',_token);
            resetValidationError();
            $.ajax({
                url: $('#store_url').val(),
                type:"POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                    create_form_reset();
                    $('#create_cash_flow_account').modal('hide');
                    toastr.success('Added Successfully','Success');
                    resetAfterChange();
                },
                error:function(response) {
                    showValidationErrors('#create_cash_flow',response.responseJSON.errors);
                }
            });
        });
        $(document).on('click', '.edit_cash_flow_acc', function(event){
            event.preventDefault();
            let id = $(this).data('id');
            let url =  $('#edit_url').val();
            url = url.replace(':id',id);
            $.get(url, function(response){
                if(response){
                    $('#edit_form').html(response);
                    $('#edit_cash_flow_modal').modal('show');
                    $('select').niceSelect();
                }
            });
        });
        $(document).on('submit', '#update_cash_flow', function(event){
            event.preventDefault();
            let formElement = $(this).serializeArray()
            let formData = new FormData();
            formElement.forEach(element => {
                formData.append(element.name,element.value);
            });
            formData.append('_token',_token);
            let id = $('#rowId').val();
            let url = $('#update_url').val();
            url = url.replace(':id',id);
            resetValidationError();
            $.ajax({
                url: url,
                type:"POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                    $('#edit_cash_flow_modal').modal('hide');
                    resetAfterChange()
                    toastr.success('Update Successfully');
                },
                error:function(response) {
                    $('#edit_cash_flow_modal').modal('show');
                    showValidationErrors('#update_cash_flow',response.responseJSON.errors);
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
        
        function resetAfterChange(){
            window.location.reload();
        }
        function create_form_reset(){
            $('#create_cash_flow')[0].reset();
        }
        function showValidationErrors(formType, errors){
            $(formType +' #amount_error').text(errors.type);
            $(formType +' #purpose_error').text(errors.type);
        }
        function resetValidationError(){
            $('#amount_error').html('');
            $('#purpose_error').html('');
        }
    });
})(jQuery);
