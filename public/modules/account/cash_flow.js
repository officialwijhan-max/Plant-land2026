
(function($){
    "use strict";
    $(document).ready(function () {
        var cash_in_dtbl_url = $('#cash_in_dtbl_url').val();
        var cash_out_dtbl_url = $('#cash_out_dtbl_url').val();
        let _token = $('meta[name=_token]').attr('content') ;
        $('input[name="date_range_filter"]').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Financial Year': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            },
            "startDate": moment().subtract(7, 'days'),
            "endDate": moment()
        }, function (start, end, label) {
            $('#start').val(start.format('YYYY-MM-DD'))
            $('#end').val(end.format('YYYY-MM-DD'))
        });

        $("#reset-date-filter").on('click',function(){
            window.location.reload();
        });

        $(document).on('click', '.filter_type', function(){
            if ($('.cash_in').prop('checked') == true) {
                table_in.clearPipeline();
                table_in.ajax.reload();
            }
            if ($('.cash_out').prop('checked') == true) {
                table_out.clearPipeline();
                table_out.ajax.reload();
            }
        });

        $(document).on('click','.excel_btn', function(){
            var excel_url = window.location.href + '&excel=1';
            var status = 0;
            if ($('.cash_in').prop('checked') == true) {
                status = 1;
            }
            if ($('.cash_out').prop('checked') == true) {
                status = 1;
            }
            if (status == 1) {
                window.open(excel_url, '_blank')
            }else {
                toastr.warning("Please select cash flow type first !!!");
            }
        });

        $(document).on('click','.print_btn', function(){
            var pdf_url = window.location.href + '&print=1';
            var status = 0;
            if ($('.cash_in').prop('checked') == true) {
                status = 1;
            }
            if ($('.cash_out').prop('checked') == true) {
                status = 1;
            }
            if (status == 1) {
                window.open(pdf_url, '_blank');
            }else {
                toastr.warning("Please select cash flow type first !!!");
            }

        });
    });

})(jQuery);
