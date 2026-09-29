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
        $(document).on('click','.delete_customer',function (event){
            event.preventDefault();
            let id = $(this).data('id');
            $('#delete_item_id').val(id);
            $('#deleteItemModal').modal('show');
        });
        $(document).on('submit', '#item_delete_form', function(event) {
            event.preventDefault();
            $('#deleteItemModal').modal('hide');
            var formData = new FormData();
            formData.append('_token', _token);
            formData.append('id', $('#delete_item_id').val());
            $.ajax({
                url:  $('#delete_url').val(),
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    if(response.parent_msg){
                        toastr.warning(response.parent_msg);
                    }
                    else{
                        resetAfterChange();
                        toastr.success("Deleted Successfully");
                    }
                },
                error: function(response) {
                    toastr.error("Something Went Wrong");
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
            $(formType +' #name_error').text(errors.first_name);
            $(formType +' #code_error').text(errors.code);
            $(formType +' #type_error').text(errors.type);
        }
        function resetValidationError(){
            $('#name_error').html('');
            $('#code_error').html('');
            $('#type_error').html('');
        }
    });
})(jQuery);
