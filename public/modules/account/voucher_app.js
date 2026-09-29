
(function($){
    "use strict";
    $(document).ready(function () {
        var dtbl_url = $('#dtbl_url').val();
        let _token = $('meta[name=_token]').attr('content') ;

        $(document).on('click','.check_all_td', function(){
            $('.Crm_table_active_3').DataTable().columns.adjust().draw();

            if ($('.check_all_td').prop('checked') == true) {
                $.each($('.approve'), function () {
                    $(this).prop('checked', true)
                })
                $('.approve_btn').removeClass('d-none');
            } else {
                $.each($('.approve'), function () {
                    $(this).prop('checked', false)
                })
                $('.approve_btn').addClass('d-none');
            }
        });
        $(document).on('click','.approve', function(){
            // $('.Crm_table_active_3').DataTable().columns.adjust().draw();
            var status = 0;
            $.each($('.approve'), function () {
                if ($(this).prop('checked')) {
                    status = 1;
                }
            })
            if (status == 1) {
                $('.approve_btn').removeClass('d-none');
            }else {
                $('.approve_btn').addClass('d-none');
            }
            
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

        $('input[name="date_range_filter"]').on('change',function(){
            table.clearPipeline();
            table.ajax.reload();
        });

        $(document).on('click', '.approve_btn', function() {
            var voucher_id = $('#voucher_id').val();
            var voucher_approval_url = $('#voucher_approval_url').val();
            var status = 1;
            $.post(voucher_approval_url, {_token:_token, id:voucher_id, status:status}, function(data){
                toastr.success(data.message);
                window.location.reload();
            });
        });
        $(document).on('click', '.pending_btn', function() {
            var voucher_id = $('#voucher_id').val();
            var voucher_approval_url = $('#voucher_approval_url').val();
            var status = 2;
            $.post(voucher_approval_url, {_token:_token, id:voucher_id, status:status}, function(data){
                toastr.success(data.message);
                window.location.reload();
            });
        });
    });

})(jQuery);
