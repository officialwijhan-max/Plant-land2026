
(function($){
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    var baseUrl = $('#app_base_url').val();
    $(document).ready(function () {
        $(window).on('hashchange', function() {
            if (window.location.hash) {
                var page = window.location.hash.replace('#', '');
    
                var row_count = $('.row_filter_option').find(":selected").val();
                if (page == Number.NaN || page <= 0) {
                    return false;
                }
            }
        });
        $(document).ready(function()
        {
            $(document).on('click', '.pagination a',function(event)
            {
                event.preventDefault();
    
                $('li').removeClass('active');
                $(this).parent('li').addClass('active');
    
                var myurl = $(this).attr('href');
                var page=$(this).attr('href').split('page=')[1];
    
                var row_count = $('.row_filter_option').find(":selected").val();
    
                getData(page, row_count);
            });
    
            $(document).on('click', '.custom_thead_title',function(event)
            {
                event.preventDefault();
                if ($('.sort').val() == "asc") {
                    var sort = 'desc';
                }else {
                    var sort = 'asc';
                }
                var quick_search = $('.quick_search').val();
                var col = $(this).data('id');
                var row_count = $('.row_filter_option').find(":selected").val();
                if (quick_search != '') {
                    var new_url = '?page=' + 1 + '&row=' + row_count + '&quick_search=' + quick_search + '&sort=' + sort + '&col=' + col;
                } else {
                    var new_url = '?page=' + 1 + '&row=' + row_count + '&sort=' + sort + '&col=' + col;
                }
                $.ajax(
                {
                    url: new_url,
                    type: "get",
                    datatype: "html"
                }).done(function(data){
                    $("#item_list_tbl").empty().html(data);
                    if (quick_search != '') {
                        $('.quick_search').val(quick_search);
                    }
                    var hidden_col = $('#hidden_list_tbl').val();
        
                    if (hidden_col != '') {
                        eval(hidden_col).forEach(function(item) {
                            
                            $('td:nth-child('+item+'),th:nth-child('+item+')').hide();
                        });
                    }
                    $('.select_div').niceSelect();
                }).fail(function(jqXHR, ajaxOptions, thrownError){
                    alert('No response from server');
                    $('.select_div').niceSelect();
                });
            });
    
            $(document).on('change', '.row_filter_option',function(event)
            {
                var row_count = $(this).find(":selected").val();
                var sort = $('.sort').val();
                var col = $('.column').val();
                var quick_search = $('.quick_search').val();
                if (quick_search != '') {
                    var new_url = '?page=' + 1 + '&row=' + row_count + '&quick_search=' + quick_search + '&sort=' + sort + '&col=' + col;
                } else {
                    var new_url = '?page=' + 1 + '&row=' + row_count + '&sort=' + sort + '&col=' + col;
                }
                $.ajax(
                {
                    url: new_url,
                    type: "get",
                    datatype: "html"
                }).done(function(data){
                    $("#item_list_tbl").empty().html(data);
                    if (quick_search != '') {
                        $('.quick_search').val(quick_search);
                    }
                    var hidden_col = $('#hidden_list_tbl').val();
        
                    if (hidden_col != '') {
                        eval(hidden_col).forEach(function(item) {
                            
                            $('td:nth-child('+item+'),th:nth-child('+item+')').hide();
                        });
                    }
                    $('.select_div').niceSelect();
                }).fail(function(jqXHR, ajaxOptions, thrownError){
                    alert('No response from server');
                    $('.select_div').niceSelect();
                });
            });
        });
    
        var $input = $('.quick_search');
        var inputTimeout;
    
        function checkData() {
            var row_count = $('.row_filter_option').find(":selected").val();
            var quick_search = $('.quick_search').val();
            var sort = $('.sort').val();
            var col = $('.column').val();
            $.ajax(
            {
                url: '?page=' + 1 + '&row=' + row_count + '&quick_search=' + quick_search + '&sort=' + sort + '&col=' + col,
                type: "get",
                datatype: "html"
            }).done(function(data){
                $("#item_list_tbl").empty().html(data);
                $('.quick_search').val(quick_search);
                var hidden_col = $('#hidden_list_tbl').val();
    
                if (hidden_col != '') {
                    eval(hidden_col).forEach(function(item) {
                        
                        $('td:nth-child('+item+'),th:nth-child('+item+')').hide();
                    });
                }
                $('.select_div').niceSelect();
            }).fail(function(jqXHR, ajaxOptions, thrownError){
                alert('No response from server');
                $('.select_div').niceSelect();
            });
        }
    
        $(document).on('keyup', '.quick_search', function(){
            if (inputTimeout) {
                clearTimeout(inputTimeout);
            }
            inputTimeout = setTimeout(checkData, 500);
        });
    });
})(jQuery);
function getData(page, row_count){
    var quick_search = $('.quick_search').val();
    var sort = $('.sort').val();
    var col = $('.column').val();
    if (quick_search != '') {
        var new_url = '?page=' + page + '&row=' + row_count + '&quick_search=' + quick_search + '&sort=' + sort + '&col=' + col;
    } else {
        var new_url = '?page=' + page + '&row=' + row_count + '&sort=' + sort + '&col=' + col;
    }
    $.ajax(
    {
        url: new_url,
        type: "get",
        datatype: "html"
    }).done(function(data){
        $("#item_list_tbl").empty().html(data);
        if (quick_search != '') {
            $('.quick_search').val(quick_search);
        }
        var hidden_col = $('#hidden_list_tbl').val();

        if (hidden_col != '') {
            eval(hidden_col).forEach(function(item) {
                
                $('td:nth-child('+item+'),th:nth-child('+item+')').hide();
            });
        }
        $('.select_div').niceSelect();
        location.hash = page;
    }).fail(function(jqXHR, ajaxOptions, thrownError){
        toastr.info('No response from server');
        $('.select_div').niceSelect();
    });
}