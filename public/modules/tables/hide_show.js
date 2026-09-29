
(function($){
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    var baseUrl = $('#app_base_url').val();
    $(document).ready(function () {
        var hidden_col = $('#hidden_list_tbl').val();

        if (hidden_col != '') {
            eval(hidden_col).forEach(function(item) {
                $('td:nth-child('+item+'),th:nth-child('+item+')').hide();
            });
        }
        $(document).on('click', '.hide_show_click_btn', function(){
            $('#pre-loader').removeClass('d-none');
            $('.showModalHideColumn').html('');
            var hide_show_permission_by_self = $('#hide_show_permission_by_self').val();
            var div = '';
            var th_names = $('#th_name').val();

            $.post(hide_show_permission_by_self, {_token:_token}, function(data){
                $('.showModalHideColumn').html(data);
                $('.dynamically_append_tr').html('');
                var show_column = $('#show_column_no_by_self').val();
                var hide_column = $('#hide_column_no_by_self').val();

                if (typeof show_column === "undefined" && typeof hide_column === "undefined") {
                    eval(th_names).forEach(function(item) {
                        div = '<tr><td>'+item[1]+'</td><td class="text-center">'+
                                    '<ul class="permission_list sms_list">'+
                                        '<li>'+
                                            '<label class="primary_checkbox d-flex mr-12 ">'+
                                                '<input name="'+item[0]+'" type="radio" id="'+item[0]+'" value="hide-'+item[2]+'" class="'+item[0]+'">'+
                                                '<span class="checkmark"></span>'+
                                            '</label>'+
                                            '<p>No</p>'+
                                        '</li>'+
                                        '<li>'+
                                            '<label class="primary_checkbox d-flex mr-12 ">'+
                                                '<input name="'+item[0]+'" type="radio" id="'+item[0]+'" value="show-'+item[2]+'" class="'+item[0]+'" checked>'+
                                                '<span class="checkmark"></span>'+
                                            '</label>'+
                                            '<p>Yes</p>'+
                                        '</li>'+
                                    '</ul>'+
                                    '</td></tr>';
                        $('.dynamically_append_tr').append(div);
                    });
                    $('#pre-loader').addClass('d-none');
                }else {
                    eval(th_names).forEach(function(item) {
                        var show_col = eval(show_column);
                        var hide_col = eval(hide_column);
                        if ((typeof show_col === "undefined" && typeof hide_col === "undefined")) {
                            div = '<tr><td>'+item[1]+'</td><td class="text-center">'+
                                        '<ul class="permission_list sms_list">'+
                                            '<li>'+
                                                '<label class="primary_checkbox d-flex mr-12 ">'+
                                                    '<input name="'+item[0]+'" type="radio" id="'+item[0]+'" value="hide-'+item[2]+'" class="'+item[0]+'">'+
                                                    '<span class="checkmark"></span>'+
                                                '</label>'+
                                                '<p>No</p>'+
                                            '</li>'+
                                            '<li>'+
                                                '<label class="primary_checkbox d-flex mr-12 ">'+
                                                    '<input name="'+item[0]+'" type="radio" id="'+item[0]+'" value="show-'+item[2]+'" class="'+item[0]+'" checked>'+
                                                    '<span class="checkmark"></span>'+
                                                '</label>'+
                                                '<p>Yes</p>'+
                                            '</li>'+
                                        '</ul>'+
                                        '</td></tr>';
                            $('.dynamically_append_tr').append(div);
                        }else {
                            if ($.inArray(parseInt(item[0]), hide_col) != -1) {
                                var hide_col_checked = "checked";
                            }else {
                                var hide_col_checked = "";
                            }
                            if ($.inArray(parseInt(item[0]), show_col) != -1) {
                                var show_col_checked = "checked";
                            }else {
                                var show_col_checked = "";
                            }
                            div = '<tr><td>'+item[1]+'</td><td class="text-center">'+
                                        '<ul class="permission_list sms_list">'+
                                            '<li>'+
                                                '<label class="primary_checkbox d-flex mr-12 ">'+
                                                    '<input name="'+item[0]+'" type="radio" id="'+item[0]+'" value="hide-'+item[2]+'" class="'+item[0]+'"'+hide_col_checked+'>'+
                                                    '<span class="checkmark"></span>'+
                                                '</label>'+
                                                '<p>No</p>'+
                                            '</li>'+
                                            '<li>'+
                                                '<label class="primary_checkbox d-flex mr-12 ">'+
                                                    '<input name="'+item[0]+'" type="radio" id="'+item[0]+'" value="show-'+item[2]+'" class="'+item[0]+'"'+show_col_checked+'>'+
                                                    '<span class="checkmark"></span>'+
                                                '</label>'+
                                                '<p>Yes</p>'+
                                            '</li>'+
                                        '</ul>'+
                                        '</td></tr>';
                            $('.dynamically_append_tr').append(div);
                        }
                    });
                }
                
                $('#hide_show_modal_for_self').modal('show');
                $('#pre-loader').addClass('d-none');
            });

        });
    });

})(jQuery);
