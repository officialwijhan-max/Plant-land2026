<script type="text/javascript">
$(document).ready(function() {

    $('.Crm_table_active_3').DataTable({
        processing: false,
        serverSide: true,
        "dom": '<"top"i>rt<"bottom"flp><"clear">',
        "ajax": $.fn.dataTable.pipeline( {
            url: '{{ route('pos-order.get_data_tbl') }}',
            // data: function(d) {
            //     d.filter_date = $('input[name="date_range_filter"]').val();
            // },
            pages: 5 // number of pages to cache
        }),
        columns: [
            { data: 'DT_RowIndex', name: 'id' },
            { data: 'created_at', name: 'created_at' },
            { data: 'sale_type', name: 'sale_type' },
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'user_name', name: 'user_name' },
            { data: 'total_quantity', name: 'total_quantity' },
            { data: 'total_tax', name: 'total_tax' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'paid_amount', name: 'paid_amount' },
            { data: 'due_amount', name: 'due_amount' },
            { data: 'status', name: 'status' },
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
                .column( 10 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0 );
            var paidTotal = api
                .column( 9 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0 );
            var dueTotal = api
                .column( 8 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0 );
            var VatTotal = api
                .column( 7 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0 );
            var QtyTotal = api
                .column( 6 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0 );

            var currency_sym = $('#currency_sym').val();
            // Update footer by showing the total with the reference of the column index
        $( api.column( 0 ).footer() ).html('Total');
            $( api.column( 10 ).footer() ).html(currency_sym + ' ' +monTotal.toFixed(2));
            $( api.column( 9 ).footer() ).html(currency_sym + ' ' +paidTotal.toFixed(2));
            $( api.column( 8 ).footer() ).html(currency_sym + ' ' +dueTotal.toFixed(2));
            $( api.column( 7 ).footer() ).html(VatTotal + '%');
            $( api.column( 6 ).footer() ).html(QtyTotal);
        },
        bLengthChange: true,
        "bDestroy": true,
        language: {
            search: "<i class='ti-search'></i>",
            searchPlaceholder: 'Quick Search',
            paginate: {
                next: "<i class='ti-arrow-right'></i>",
                previous: "<i class='ti-arrow-left'></i>"
            }
        },
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="fa fa-files-o"></i>',
                title : $("#header_title").text(),
                titleAttr: 'Copy',
                exportOptions: {
                    columns: ':visible',
                    columns: ':not(:last-child)',
                },
                footer: true,
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                title : $("#header_title").text(),
                messageTop: info,
                margin: [10 ,10 ,10, 0],
                exportOptions: {
                    columns: ':visible',
                    columns: ':not(:last-child)',
                },
                footer: true,

            },
            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-file-text-o"></i>',
                titleAttr: 'CSV',
                messageTop: info,
                footer: true,
                exportOptions: {
                    columns: ':visible',
                    columns: ':not(:last-child)',
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o"></i>',
                title : $("#header_title").text(),
                titleAttr: 'PDF',
                exportOptions: {
                    columns: ':visible',
                    columns: ':not(:last-child)',
                },
                orientation: 'landscape',
                pageSize: 'A4',
                margin: [ 0, 0, 0,0 ],
                alignment: 'center',
                header: true,
                footer: true,
                messageTop: info,
                messageBottom: 'Generated By : {{ auth()->user()->name }}',
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i>',
                titleAttr: 'Print',
                alignment: 'center',
                title : window.dataTableHeadingText,
                exportOptions: {
                    columns: ':visible',
                    columns: ':not(:last-child)',
                },
                header: true,
                extend: 'print',
                footer: true

            },
            {
                extend: 'colvis',
                text: '<i class="fa fa-columns"></i>',
                postfixButtons: ['colvisRestore']
            }
        ],
        columnDefs: [{
            visible: false
        }],
        responsive: false,
        ordering: false
    });

    var table = $('.Crm_table_active_3').DataTable();

    table.on( 'draw', function () {
        if($('.Crm_table_active_3 tbody tr').length <= 3){
            $('.dataTables_scrollBody').addClass('manage-table-height')
        }else{
            $('.dataTables_scrollBody').removeClass('manage-table-height')
        }
    });

    // $('input[name="date_range_filter"]').daterangepicker();

    $("#reset-date-filter").on('click',function(){
        window.location.reload();
    });

    $('input[name="date_range_filter"]').on('change',function(){
        table.clearPipeline();
        table.ajax.reload();
    });

});

</script>
