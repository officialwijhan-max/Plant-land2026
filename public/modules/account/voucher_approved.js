
(function($){
    "use strict";
    $(document).ready(function () {
        var dtbl_url = $('#dtbl_url').val();
        let _token = $('meta[name=_token]').attr('content') ;

        $(document).on('change','.check_all', function(){
            if ($('.check_all').prop('checked') == true) {
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
        var hidden_col = $('#hidden_list_tbl').val();
        if (hidden_col != '' && typeof hidden_col != "undefined") {
            var targets_col = JSON.parse(hidden_col);
        }else {
            var targets_col = [];
        }
        $('.Crm_table_active_3').DataTable({
                processing: false,
                serverSide: true,
                "ajax": $.fn.dataTable.pipeline( {
                    url: dtbl_url,
                    // data: function(d) {
                    //     d.filter_date = $('input[name="date_range_filter"]').val();
                    // },
                    pages: 5 // number of pages to cache
                } ),

                columns: [
                    { data: 'DT_RowIndex', name: 'id' },
                    { data: 'date', name: 'date' },
                    { data: 'txn_id', name: 'txn_id' },
                    { data: 'amount', name: 'amount' },
                    { data: 'is_approved', name: 'is_approved' },
                    { data: 'action', name: 'action' }
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // converting to interger to find total
                    var parseFloat = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[^0-9\.]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    // computing column Total of the complete result
                    var monTotal = api
                        .column( 3 , { page: 'current'})
                        .data()
                        .reduce( function (a, b) {
                            return parseFloat(a) + parseFloat(b);
                        }, 0 );

                    var currency_sym = $('#currency_sym').val();
                    // Update footer by showing the total with the reference of the column index
                $( api.column( 0 ).footer() ).html('Total');
                    $( api.column( 3).footer() ).html(currency_sym + ' ' +monTotal.toFixed(2));
                },
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
                    "targets": targets_col,
                    "visible": false,
                    "searchable": false
                }],
                responsive: true,
            });

            let table = $('.Crm_table_active_3').DataTable();
            //
            // table.on( 'draw', function () {
            //     if($('.Crm_table_active_3 tbody tr').length <= 3){
            //         $('.dataTables_scrollBody').addClass('manage-table-height')
            //     }else{
            //         $('.dataTables_scrollBody').removeClass('manage-table-height')
            //     }
            // });

            // $('input[name="date_range_filter"]').daterangepicker();

            $("#reset-date-filter").on('click',function(){
                window.location.reload();
            });

            $('input[name="date_range_filter"]').on('change',function(){
                table.clearPipeline();
                table.ajax.reload();
            });
    });

    $(window).on('load', function() {

        $('select').niceSelect();
        $('label .nice-select').addClass('dataTable_select');

        $('label .nice-select').on('click', function () {
            $(this).toggleClass('open_selectlist');
        })
        console.log('Datatable Loaded');
     });

})(jQuery);
