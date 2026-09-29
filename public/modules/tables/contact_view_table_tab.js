
(function($){
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    var baseUrl = $('#app_base_url').val();
    $(document).ready(function () {
        new_data_table('#invoice_list_in_customer_detail');
        new_data_table('#return_invoice_list_in_customer_detail');
        new_data_table('#invoice_list_in_supplier_detail');
        new_data_table('#return_invoice_list_in_supplier_detail');
        new_data_table('#commission_list_in_retialer_detail');
    });
})(jQuery);
function new_data_table(table = '#table'){
    // alert(table)
    $(window).on('hashchange', function() {
        if (window.location.hash) {
            var page = window.location.hash.replace('#', '');

            var row_count = $(table+' .row_filter_option').find(":selected").val();
            if (page == Number.NaN || page <= 0) {
                return false;
            }
        }
    });
    $(document).ready(function()
    {
        $(document).on('click', '.return_inv_class', function(){
            var row_count = $('#return_invoice_list_in_supplier_detail .row_filter_option').find(":selected").val();
            getData(1, row_count, "#return_invoice_list_in_supplier_detail");
        });

        $(document).on('click', '.return_customer_inv_class', function(){
            var row_count = $('#return_invoice_list_in_customer_detail .row_filter_option').find(":selected").val();
            getData(1, row_count, "#return_invoice_list_in_customer_detail");
        });

        $(document).on('click', table+' .pagination a',function(event)
        {
            console.log(table)
            event.preventDefault();

            $('li').removeClass('active');
            $(this).parent('li').addClass('active');

            var myurl = $(this).attr('href');
            var page=$(this).attr('href').split('page=')[1];

            var row_count = $(table+' .row_filter_option').find(":selected").val();

            getData(page, row_count, table);
        });

        $(document).on('click', table+' .custom_thead_title',function(event)
        {
            event.preventDefault();
            if (table == "#invoice_list_in_customer_detail") {
                var typeF = '&type=invoice_list_in_customer_detail';
            }
            if (table == "#return_invoice_list_in_customer_detail") {
                var typeF = '&type=return_invoice_list_in_customer_detail';
            }
            if (table == "#documents_list_table") {
                var typeF = '&type=documents_list_table';
            }
            if (table == "#loan_and_advance_list_table") {
                var typeF = '&type=loan_and_advance_list_table';
            }
            if (table == "#invoice_list_in_supplier_detail") {
                var typeF = '&type=invoice_list_in_supplier_detail';
            }
            if (table == "#return_invoice_list_in_supplier_detail") {
                var typeF = '&type=return_invoice_list_in_supplier_detail';
            }
            if (table == "#commission_list_in_retialer_detail") {
                var typeF = '&type=commission_list_in_retialer_detail';
            }
            if ($('.sort').val() == "asc") {
                var sort = 'desc';
            }else {
                var sort = 'asc';
            }
            var quick_search = $(table+' .quick_search').val();
            var col = $(this).data('id');
            var row_count = $(table+' .row_filter_option').find(":selected").val();
            if (quick_search != '') {
                var new_url = '?page=' + 1 + '&row=' + row_count + '&quick_search=' + quick_search + '&sort=' + sort + '&col=' + col + typeF;
            } else {
                var new_url = '?page=' + 1 + '&row=' + row_count + '&sort=' + sort + '&col=' + col + typeF;
            }
            $.ajax(
            {
                url: new_url,
                type: "get",
                datatype: "html"
            }).done(function(data){
                var parent_div = $(table).parent();
                parent_div.empty().html(data);
                if (quick_search != '') {
                    $(table+' .quick_search').val(quick_search);
                }
                if (table == "#invoice_list_in_customer_detail") {
                    var hidden_col = $('#hidden_list_tbl').val();
        
                    if (hidden_col != '') {
                        eval(hidden_col).forEach(function(item) {
                            
                            $('td:nth-child('+item+'),th:nth-child('+item+')').hide();
                        });
                    }
                }
                $(table+' .primary_select, '+table+' .select_div').niceSelect();
            }).fail(function(jqXHR, ajaxOptions, thrownError){
                alert('No response from server');
                $(table+' .primary_select, '+table+' .select_div').niceSelect();
            });
        });

        $(document).on('change', table+' .row_filter_option',function(event)
        {
            if (table == "#invoice_list_in_customer_detail") {
                var typeF = '&type=invoice_list_in_customer_detail';
            }
            if (table == "#return_invoice_list_in_customer_detail") {
                var typeF = '&type=return_invoice_list_in_customer_detail';
            }
            if (table == "#documents_list_table") {
                var typeF = '&type=documents_list_table';
            }
            if (table == "#loan_and_advance_list_table") {
                var typeF = '&type=loan_and_advance_list_table';
            }
            if (table == "#invoice_list_in_supplier_detail") {
                var typeF = '&type=invoice_list_in_supplier_detail';
            }
            if (table == "#return_invoice_list_in_supplier_detail") {
                var typeF = '&type=return_invoice_list_in_supplier_detail';
            }
            if (table == "#commission_list_in_retialer_detail") {
                var typeF = '&type=commission_list_in_retialer_detail';
            }
            var row_count = $(this).find(":selected").val();
            var sort = $('.sort').val();
            var col = $('.column').val();
            var quick_search = $(table+' .quick_search').val();
            if (quick_search != '') {
                var new_url = '?page=' + 1 + '&row=' + row_count + '&quick_search=' + quick_search + '&sort=' + sort + '&col=' + col + typeF;
            } else {
                var new_url = '?page=' + 1 + '&row=' + row_count + '&sort=' + sort + '&col=' + col + typeF;
            }
            $.ajax(
            {
                url: new_url,
                type: "get",
                datatype: "html"
            }).done(function(data){
                var parent_div = $(table).parent();
                parent_div.empty().html(data);
                if (quick_search != '') {
                    $(table+' .quick_search').val(quick_search);
                }
                if (table == "#invoice_list_in_customer_detail") {
                    var hidden_col = $('#hidden_list_tbl').val();
        
                    if (hidden_col != '') {
                        eval(hidden_col).forEach(function(item) {
                            
                            $('td:nth-child('+item+'),th:nth-child('+item+')').hide();
                        });
                    }
                }
                $(table+' .primary_select, '+table+' .select_div').niceSelect();
            }).fail(function(jqXHR, ajaxOptions, thrownError){
                alert('No response from server');
                $(table+' .primary_select, '+table+' .select_div').niceSelect();
            });
        });
    });

    var $input = $('.quick_search');
    var inputTimeout;

    function checkData() {
        if (table == "#invoice_list_in_customer_detail") {
            var typeF = '&type=invoice_list_in_customer_detail';
        }
        if (table == "#return_invoice_list_in_customer_detail") {
            var typeF = '&type=return_invoice_list_in_customer_detail';
        }
        if (table == "#documents_list_table") {
            var typeF = '&type=documents_list_table';
        }
        if (table == "#loan_and_advance_list_table") {
            var typeF = '&type=loan_and_advance_list_table';
        }
        if (table == "#invoice_list_in_supplier_detail") {
            var typeF = '&type=invoice_list_in_supplier_detail';
        }
        if (table == "#return_invoice_list_in_supplier_detail") {
            var typeF = '&type=return_invoice_list_in_supplier_detail';
        }
        if (table == "#commission_list_in_retialer_detail") {
            var typeF = '&type=commission_list_in_retialer_detail';
        }
        var row_count = $(table+' .row_filter_option').find(":selected").val();
        var quick_search = $(table+' .quick_search').val();
        var sort = $('.sort').val();
        var col = $('.column').val();
        $.ajax(
        {
            url: '?page=' + 1 + '&row=' + row_count + '&quick_search=' + quick_search + '&sort=' + sort + '&col=' + col + typeF,
            type: "get",
            datatype: "html"
        }).done(function(data){
            var parent_div = $(table).parent();
            parent_div.empty().html(data);
            $(table+' .quick_search').val(quick_search);
            if (table == "#invoice_list_in_customer_detail") {
                var hidden_col = $('#hidden_list_tbl').val();
    
                if (hidden_col != '') {
                    eval(hidden_col).forEach(function(item) {
                        
                        $('td:nth-child('+item+'),th:nth-child('+item+')').hide();
                    });
                }
            }
            $(table+' .primary_select, '+table+' .select_div').niceSelect();
        }).fail(function(jqXHR, ajaxOptions, thrownError){
            alert('No response from server');
            $(table+' .primary_select, '+table+' .select_div').niceSelect();
        });
    }

    $(document).on('keyup', table+' .quick_search', function(){
        // if already set
        if (inputTimeout) {
            clearTimeout(inputTimeout);
        }
        
        inputTimeout = setTimeout(checkData, 500);
    });
}

function getData(page, row_count, table){
    if (table == "#invoice_list_in_customer_detail") {
        var typeF = '&type=invoice_list_in_customer_detail';
    }
    if (table == "#return_invoice_list_in_customer_detail") {
        var typeF = '&type=return_invoice_list_in_customer_detail';
    }
    if (table == "#documents_list_table") {
        var typeF = '&type=documents_list_table';
    }
    if (table == "#loan_and_advance_list_table") {
        var typeF = '&type=loan_and_advance_list_table';
    }
    if (table == "#invoice_list_in_supplier_detail") {
        var typeF = '&type=invoice_list_in_supplier_detail';
    }
    if (table == "#return_invoice_list_in_supplier_detail") {
        var typeF = '&type=return_invoice_list_in_supplier_detail';
    }
    if (table == "#commission_list_in_retialer_detail") {
        var typeF = '&type=commission_list_in_retialer_detail';
    }
    var quick_search = $(table+' .quick_search').val();
    var sort = $('.sort').val();
    var col = $('.column').val();
    if (quick_search != '') {
        var new_url = '?page=' + page + '&row=' + row_count + '&quick_search=' + quick_search + '&sort=' + sort + '&col=' + col + typeF;
    } else {
        var new_url = '?page=' + page + '&row=' + row_count + '&sort=' + sort + '&col=' + col + typeF;
    }
    $.ajax(
    {
        url: new_url,
        type: "get",
        datatype: "html"
    }).done(function(data){
        var parent_div = $(table).parent();
        parent_div.empty().html(data);
        if (quick_search != '') {
            $(table+' .quick_search').val(quick_search);
        }
        $(table+' .primary_select, '+table+' .select_div').niceSelect();

        if (table == "#invoice_list_in_customer_detail") {
            var hidden_col = $('#hidden_list_tbl').val();

            if (hidden_col != '') {
                eval(hidden_col).forEach(function(item) {
                    
                    $('td:nth-child('+item+'),th:nth-child('+item+')').hide();
                });
            }
        }
        
        location.hash = page;
    }).fail(function(jqXHR, ajaxOptions, thrownError){
        toastr.info('No response from server');
        $(table+' .primary_select, '+table+' .select_div').niceSelect();
    });
}