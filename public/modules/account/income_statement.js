(function($) {
    "use strict";
    let _token = $('meta[name=_token]').attr('content') ;
    // $(document).ready(function(){
    //     $('.Crm_table_active_3').DataTable({
    //         bLengthChange: false,
    //         "bDestroy": false,
    //         columnDefs: [{
    //             visible: false
    //         }],
    //         responsive: true,
    //         ordering: true,
    //         paging: false,
    //     });
    // });
    $(document).on('change', '.report_type', function(){
        if (this.value == "fiscal_year") {
            $('.others_div').addClass('d-none');
            $('.fiscal_year_div').removeClass('d-none');
        }else{
            $('.others_div').removeClass('d-none');
            $('.fiscal_year_div').addClass('d-none');
        }
    });
})(jQuery);
