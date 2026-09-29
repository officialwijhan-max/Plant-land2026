
(function($){
    "use strict";
    $(document).ready(function () {
        var dtbl_url = $('#dtbl_url').val();
        $('.Crm_table_active_3').DataTable({
            processing: false,
            serverSide: true,
            "ajax": $.fn.dataTable.pipeline( {
                url: dtbl_url,
                data: function(d) {
                    d.filter_date = $('input[name="date_range_filter"]').val();
                },
                pages: 5 // number of pages to cache
            } ),

            columns: [
                { data: 'DT_RowIndex', name: 'id' },
                { data: 'start_date', name: 'start_date' },
                { data: 'end_date', name: 'end_date' },
                { data: 'is_locked', name: 'is_locked' },
                { data: 'action', name: 'action' },
            ],
            bLengthChange: true,
            "bDestroy": true,
            language: {
                search: "<i class='ti-search'></i>",
                searchPlaceholder: $('#quick_search_dtbl').val(),
                paginate: {
                    next: "<i class='ti-arrow-right'></i>",
                    previous: "<i class='ti-arrow-left'></i>"
                }
            },
            columnDefs: [{
                visible: false
            }],
            responsive: true,
            searching: false,
            paging: true,
            info: false
        });
        $(window).on('load', function() {
            $('select').niceSelect();
            $('label .nice-select').addClass('dataTable_select');

            $('label .nice-select').on('click', function () {
                $(this).toggleClass('open_selectlist');
            })
            console.log('Datatable Loaded');
         });

        $(document).on('click','.close_now_btn', function(){
            $('#pre-loader').removeClass('d-none');
            let url = $('#closing_by_id').val();
            var id = $(this).attr("data-id");
            url = url.replace(':id',id);
            $.ajax({
                url: url,
                type: "GET",
                dataType: "HTML",
                success: function (response) {
                 $('#close_details').html(response);
                 $('#close_details_modal').modal('show');
                 $('#closingDate').datepicker({
                     Default: {
                         leftArrow: '<i class="fa fa-long-arrow-left"></i>',
                         rightArrow: '<i class="fa fa-long-arrow-right"></i>'
                     },
                     autoclose: true,
                     endDate: "today"
                 });
                 $('#pre-loader').addClass('d-none');
             },
                error: function (error) {
                     console.log(error);
                     $('#pre-loader').addClass('d-none');
                }
            });
        });
    });
})(jQuery);
