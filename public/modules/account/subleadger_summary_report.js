
(function($){
    "use strict";
    $(document).ready(function () {
        $(".subleager").select2({
            ajax: {
                url: $('#get_subleadger_list_url').val(),
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1
                        }
                        return query;
                },
                cache: false
            },
            escapeMarkup: function (m) {
                return m;
            }
        });
        $('.Crm_table_active_3').DataTable({
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
            ordering: false,
            paging: false,
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
    });

})(jQuery);
