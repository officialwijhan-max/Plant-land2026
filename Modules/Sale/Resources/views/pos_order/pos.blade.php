<!DOCTYPE html>
<html>

<head>

    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="icon" href="{{ asset(app('general_setting')->favicon) }}" type="image/png" />
    <title>{{ app('general_setting')->site_title }} | {{ __('sale.POS') }} </title>
    <meta name="_token" content="{!! csrf_token() !!}" />
    <!-- Bootstrap CSS -->

    @php
        $setting = app('general_setting');

        Illuminate\Support\Facades\Cache::remember('language', 3600 , function() {
            return Modules\Localization\Entities\Language::where('code', session()->get('locale', Config::get('app.locale')))->first();
        });
    @endphp
    @include('backEnd.partials.style')

    <style>
        span.offer_badge {
            position: absolute;
            right: 0;
            top: 0px;
            background: #efeeee;
            font-size: 10px;
            font-weight: 500;
            padding: 5px 4px 2px 4px;
            line-height: 1;
            display: inline-block;
            border-radius: 0px;
        }

        label.primary-btn.small.fix-gr-bg {
            top: 8px;
            right: 5px;
        }

        .nice-select.has-multiple {
            white-space: inherit;
            height: auto;
            padding: 7px 12px;
            min-height: 40px;
            line-height: 22px;
            width: 120px;
            border-radius: 4px;
            line-height: 40px;
        }

        .primary_select.nice-select.has-multiple:after {
            top: 25% !important;
            transform: translateY(-95%) rotate(0deg) !important;
        }

        .width_pos_130 {
            width: 132px !important;
        }

        .pb_15 {
            padding-bottom: 13px;
        }

        .pointer {
            cursor: pointer;
        }

        .mini_main_content {
            margin-left: 0 !important;
            width: calc(100% - 0px) !important;
        }

        @media (min-width: 1200px) {
            #main-content {
                padding: 0 0 0px 0 !important;
            }
        }

        .header_iner {
            margin: 0 0 0px 0;
            padding: 5px;
        }

        .invoice_table {
            border-collapse: collapse;
        }

        .nice-select.open .list {
            overflow-y: auto;
            /* max-height: 200px; */
            overflow: auto !important;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
        }

        .hide_element {
            display: none !important;
        }

        .dashed-underline {
            display: block;
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }

        .invoice_wrapper {
            max-width: 435px;
            margin: auto;
        }

        .invoice_table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .border_none {
            border: 0px solid transparent;
            border-top: 0px solid transparent !important;
        }

        .invoice_part_iner {
            background-color: #fff;
            padding: 20px;
        }

        .invoice_part_iner h4 {
            font-size: 30px;
            font-weight: 500;
            margin-bottom: 40px;

        }

        .invoice_part_iner h3 {
            font-size: 25px;
            font-weight: 500;
            margin-bottom: 5px;

        }

        .table_border thead {
            background-color: #F6F8FA;
        }

        .red_border {
            color: red !important;
            border: 1px solid red !important;
        }

        .invoice_table td,
        .table th {
            padding: 5px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #79838b;
        }

        .invoice_table td,
        .table th {
            padding: 5px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #79838b;
        }

        .table_border tr {
            border-bottom: 1px solid #000 !important;
        }

        /* .table_border tr:last-child{
            border-bottom: 0 solid transparent !important;
        } */
        th p span,
        td p span {
            color: #212E40;
        }

        .invoice_table th {
            color: #00273d;
            font-weight: 300;
            border-bottom: 1px solid #f1f2f3 !important;
            background-color: #fafafa;
        }

        h5 {
            font-size: 12px;
            font-width: 500;
        }

        h6 {
            font-size: 10px;
            font-weight: 300;
        }

        .mt_40 {
            margin-top: 40px;
        }

        .table_style th,
        .table_style td {
            padding: 20px;
        }

        .invoice_info_table td {
            font-size: 10px;
            padding: 0px;
        }

        .invoice_info_table td h6 {
            color: #6D6D6D;
            font-weight: 400;
        }

        p {
            font-size: 10px;
            color: #454545;
        }

        .invoice_info_table2 tbody {}

        .invoice_info_table2 tbody th {
            background: transparent;
            padding: 0px;
            text-align: right;
            border-bottom: 1px dotted #000 !important;
        }

        .invoice_info_table2 tbody td {
            padding: 0px;
        }

        .table_border2 thead {
            border-bottom: 1px solid #000 !important;
        }

        .table_border2 thead th {
            background: transparent;
            border-bottom: 1px solid #000 !important;
            font-size: 10px;
        }

        .table_border2 tbody td {
            padding: 0px;
            font-size: 10px;
        }

        .w_70 {
            width: 70%;
        }

        .pdf_table_1 {}

        .pdf_table_1 th {
            font-size: 10px;
            padding: 3px;
            background: transparent;
            border-bottom: 1px solid #000 !important;
            border-top: 1px solid #000 !important;
            text-align: left;
        }

        .pdf_table_2 th {
            font-size: 10px;
            padding: 3px;
            background: transparent;
            border-bottom: 1px solid #000 !important;
            border-top: 1px solid #000 !important;
            text-align: left;
        }

        .pdf_table_2 td {
            padding: 0;
        }

        .pdf_table_2 tfoot {}

        .pdf_table_2 tfoot td {
            background: #D2D6DE;
            color: #000 !important;
        }

        .dashed_table {}

        .dashed_table th {
            background: transparent;
            border-bottom: 0 !important;
            text-align: right;
            padding: 0 !important;
            font-size: 10px;
        }

        .dashed_table td {
            padding: 0 !important;
        }

        .dashed_table td span {
            border-bottom: 1px dotted #000;
            padding: 0;
            display: block;
            margin-left: 5px;
            font-size: 10px;
        }

        .balance_text strong {
            font-style: italic;
        }

        hr {
            margin: 0 !important;
        }

        .invoice_wrapper h3,
        .invoice_wrapper h5,
        .invoice_wrapper h6,
        .invoice_wrapper h4 {
            color: #000000;
        }

        .invoice_wrapper table td,
        .invoice_wrapper table th {
            font-size: 10px !important;
        }

        @media print {
            @page {
                size: landscape
            }
        }

        /* Custom */
        .responsive_unset {
            overflow-x: unset !important;
        }

        .overflow-unset2 {
            overflow: unset !important;
            max-height: inherit;

        }

        .right-scroll {
            /* overflow: auto; */
        }

        .mar-top {
            margin-top: 6px;
        }

        .all_padd {
            padding: 9px;
        }

        .clear-button {
            margin-top: 5px;
            margin-right: 15px;
        }
    </style>
    @yield('css')
    <link rel="stylesheet" href="{{ asset('public/backEnd/css/loade.css') }}" />

    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/style.css" />
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/infix.css" />
    <link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/style.css" />
</head>

<body class="pos_admin">
    <div class="main-wrapper">
        <div class="w-100 m-0" id="main-content">

            @php
                $price = $quantity = 0;
                $productTaxTotal = 0;
            @endphp

            <section class="up_st_admin_visitor d-flex flex-row-fluid flex-column">
                <!-- menu  -->
                @include('sale::pos_order.components.header_menu')
                <!--/ menu  -->
                <div class="pos__page__wrapper pos_grid_box flex-column-fluid">
                    @include('sale::pos_order.components.left_section')
                    @include('sale::pos_order.components.right_section')
                </div>
                <!-- pos_fullBtn_grid  -->
                <div class="pos_fullBtn_grid">
                    <div class="pos_full_btn d-flex justify-content-between align-items-center">
                        <span>{{ __('common.Total Amount') }}:</span>
                        <span class="total_amount_display">{{ single_price(0) }}</span>
                    </div>
                    <button class="primary_color_btn pay_now_grn_btn green_btn" type="button"
                        onclick="multiplePaymentModal()">{{ __('sale.Pay Now') }}</button>
                </div>
            </section>

            <div id="draftList_modal"></div>

            <div class="view_modal"></div>
            <div class="add-product-modal"></div>
            @include('sale::pos_order.components.add_customer')

            <div class="invoice_details w-100">

            </div>
            <input type="hidden" id="enable_gst" value="0">
            @if (session()->has('sale'))
                @php
                    $sale = session()->get('sale');
                @endphp
                <input type="hidden" name="invoice" value="{{ $sale->id }}" class="invoice">
                @include('sale::sale._components.pos_print')
            @endif

            @include('backEnd.partials.approve_modal')
            <!-- footer  -->
        </div>
    </div>
    <input type="hidden" name="country_list_select_option" id="country_list_select_option" value="{{ route('country.list_select_option') }}">
    <input type="hidden" name="state_list_select_option" id="state_list_select_option" value="{{ route('state.list_select_option') }}">
    <input type="hidden" name="city_list_select_option" id="city_list_select_option" value="{{ route('city.list_select_option') }}">

    <!--/### CHAT_MESSAGE_BOX  ### -->
    <script>

            const RTL = "{{ (bool)Illuminate\Support\Facades\Cache::get('language')->rtl}}";
            const LANG = "{{ session()->get('locale', Config::get('app.locale')) }}";

    </script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery-ui.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery.data-tables.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/buttons.flash.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/jszip.min.js"></script>

    <script src="{{asset('public/backEnd/vendors/js/pdfmake.min.js')}}"></script>
    <script src="{{asset('public/backEnd/vendors/js/vfs_fonts.js')}}"></script>

    <script src="{{ asset('public/backEnd/') }}/vendors/js/buttons.html5.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/buttons.print.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/dataTables.rowReorder.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/buttons.colVis.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/popper.js"></script>
    <script src="{{ asset('public/backEnd/') }}/css/rtl/bootstrap.min.js"></script>


    <script src="{{ route('assets.lang.js') }}"></script>
    <script src="{{ asset('public/backEnd/vendors/js/loadah.min.js') }}"></script>
    <script>
        function trans(string, args){
            let value = _.get(window.jsi18n, string);
    
            _.eachRight(args, (paramVal, paramKey) => {
                value = paramVal.replace(`:${paramKey}`, value);
            });
    
            if(typeof value == 'undefined'){
                return string;
            }
    
            return value;
        }
    </script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/nice-select.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery.magnific-popup.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/fastselect.standalone.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/raphael-min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/morris.min.js"></script>

    <script type="text/javascript" src="{{ asset('public/backEnd/') }}/vendors/js/toastr.min.js"></script>

    <script type="text/javascript" src="{{ asset('public/backEnd/') }}/vendors/js/moment.min.js"></script>

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="{{ asset('public/backEnd/') }}/vendors/js/bootstrap-datepicker.min.js"></script>
    <!-- tagsinput  -->
    <script src="{{ asset('public/frontend/') }}/vendors/tagsinput/tagsinput.js"></script>
    <!-- summernote  -->
    <script src="{{ asset('public/frontend/') }}/vendors/text_editor/summernote-bs4.js"></script>

    <!-- nestable  -->
    <script src="{{ asset('public/frontend/') }}/vendors/nestable/jquery.nestable.js"></script>

    <!-- multy select  -->
    <script src="{{ asset('public/backEnd/vendors/multiselect/jquery.multiselect.js') }}"></script>
    <script src="{{ asset('public/frontend/') }}/vendors/chartlist/Chart.min.js"></script>
    <script src="{{ asset('public/frontend/') }}/js/active_chart.js"></script>
    <!-- chage  -->
    <!-- //  <script type="text/javascript" src="{{ asset('public/backEnd/') }}/vendors/js/fullcalendar.min.js"></script> -->

    <!-- metisMenu js  -->
    <script src="{{ asset('public/frontend/') }}/js/metisMenu.js"></script>

    <!-- CALENDER JS  -->
    <script src="{{ asset('public/frontend/') }}/vendors/calender_js/core/main.js"></script>
    <script src="{{ asset('public/frontend/') }}/vendors/calender_js/daygrid/main.js"></script>
    <script src="{{ asset('public/frontend/') }}/vendors/calender_js/timegrid/main.js"></script>
    <script src="{{ asset('public/frontend/') }}/vendors/calender_js/interaction/main.js"></script>
    <script src="{{ asset('public/frontend/') }}/vendors/calender_js/list/main.js"></script>
    <script src="{{ asset('public/frontend/') }}/vendors/calender_js/activation.js"></script>
    <!-- progressbar  -->
    <script src="{{ asset('public/frontend/') }}/vendors/progressbar/circle-progress.min.js"></script>
    <!-- color picker  -->
    <script src="{{ asset('public/frontend/') }}/vendors/color_picker/colorpicker.min.js"></script>
    <script src="{{ asset('public/frontend/') }}/vendors/color_picker/examples_colorpicker.js"></script>

    <script type="text/javascript" src="{{ asset('public/backEnd/') }}/js/jquery.validate.min.js"></script>
    {{-- <script src="{{ asset('public/backEnd/') }}/vendors/js/select2/select2.min.js"></script> --}}
    <script src="{{asset('public/backEnd/js/select2.min.js')}}"></script>

    <script src="{{ asset('public/backEnd/') }}/js/main.js"></script>
    <script src="{{ asset('public/backEnd/') }}/js/custom.js"></script>
    <script src="{{ asset('public/backEnd/') }}/js/developer.js"></script>


    <script type="text/javascript">
        // for select2 multiple dropdown in send email/Sms in Individual Tab
        $("#selectStaffss").select2();
        $("#checkbox").click(function() {
            if ($("#checkbox").is(':checked')) {
                $("#selectStaffss > option").prop("selected", "selected");
                $("#selectStaffss").trigger("change");
            } else {
                $("#selectStaffss > option").removeAttr("selected");
                $("#selectStaffss").trigger("change");
            }
        });


        // for select2 multiple dropdown in send email/Sms in Class tab
        $("#selectSectionss").select2();
        $("#checkbox_section").click(function() {
            if ($("#checkbox_section").is(':checked')) {
                $("#selectSectionss > option").prop("selected", "selected");
                $("#selectSectionss").trigger("change");
            } else {
                $("#selectSectionss > option").removeAttr("selected");
                $("#selectSectionss").trigger("change");
            }
        });
    </script>

    <script>
        $('.close_modal').on('click', function() {
            $('.custom_notification').removeClass('open_notification');
        });
        $('.notification_icon').on('click', function() {
            $('.custom_notification').addClass('open_notification');
        });
        $(document).click(function(event) {
            if (!$(event.target).closest(".custom_notification").length) {
                $("body").find(".custom_notification").removeClass("open_notification");
            }
        });

        $(document).ready(function() {
            $('#languageChange').on('change', function() {
                var str = $('#languageChange').val();
                var url = $('#url').val();
                var formData = {
                    id: $(this).val()
                };
                // get section for student
                $.ajax({
                    type: "POST",
                    data: formData,
                    dataType: 'json',
                    url: url + '/' + 'language-change',
                    success: function(data) {
                        url = url + '/' + 'locale' + '/' + data[0].language_universal;
                        window.location.href = url;
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('public/backEnd/') }}/js/search.js"></script>

    {{-- @include('core::products.product.product_js_modal')
    
    @include('core::products.product.edit_product_js') --}}
    @yield('script')

    <script type="text/javascript">
        var baseUrl = $('#app_base_url').val();
        let credit_limit = 0;
        let url = 0;

        $(document).on('change', '.gst_group', function() {
            setPricewithTax();
        });

        $(document).on('click', '.serial_key_add_btn', function(){
            var sku_type_with_id = $(this).attr('data-id');
            $('#'+sku_type_with_id).modal('show');
        });
        
        $(window).on('load', function() {
            var modal = $('.invoice').val();
            if (modal > 0) {
                $('#invoice').modal('show');
            }
        });

        $(document).ready(function() {
            saleDetails()
            $(document).on('click', '.due_btn', function(e) {
                e.preventDefault()
                var a = $("input[name='min_sell_qty[]']").map(function() {
                    return $(this).val();
                }).get();
                if ($.inArray('1', a) == -1) {
                    $('.due_btn').prop('disabled', true);
                    $('#due').val(1);
                    $("#post_form_submit").submit();
                } else {
                    toastr.warning('Opps!! You cannot sale below min price !');
                }
            });

            $(document).on('click', '#save_button_note', function(e) {
                $('#noteModal').modal('hide');
            });

            $(document).on('click', '.product_info', function() {
                let id = $(this).data('id');
                let product_id = $(this).data('value');
                let sku_quantity = parseInt($('.quantity_sku' + product_id).val());
                let combo_quantity = parseInt($('.quantity_combo' + product_id).val());
                let customer = $('.customer').val();
                let gst = $('.gst_group').find(":selected").val();
                $.post('{{ route('pos.product_modal_for_select') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    customer: customer,
                    gst: gst
                }, function(data) {
                    if (data.product_id == 1) {
                        if (data.product_type == "Single" || data.product_type == "Variable") {
                            let totalQTY = sku_quantity + 1;
                            let productPrice = parseFloat($(".product_price_sku" + product_id)
                            .val());
                            $('.quantity_sku' + product_id).val(totalQTY);
                            let totalBillAmount = totalQTY * productPrice;
                            $('.product_subtotal_sku' + product_id).text(totalBillAmount.toFixed(
                            2));
                            let tr = $('.quantity_sku' + product_id).parent().parent();
                            calcutionTaxDiscount(tr)
                            productTax();
                            addTotalDiscount();
                            addQuantity(product_id, "sku");
                        } else {
                            $('.quantity_combo' + product_id).val(combo_quantity + 1);
                            addQuantity(product_id, "combo");
                        }
                        if ($("select[multiple].active.multypol_check_select").length > 0) {
                            $("select[multiple].active.multypol_check_select").multiselect({
                                columns: 1,
                                placeholder: "Select",
                                search: true,
                                searchOptions: {
                                    default: "Select",
                                },
                                selectAll: true,
                            });
                        }
                        setPricewithTax()
                        totalCalcuationAfterChange()

                    } else {
                        $('#product_details').prepend(data.product_id);
                        if ($("select[multiple].active.multypol_check_select").length > 0) {
                            $("select[multiple].active.multypol_check_select").multiselect({
                                columns: 1,
                                placeholder: "Select",
                                search: true,
                                searchOptions: {
                                    default: "Select",
                                },
                                selectAll: true,
                            });
                        }
                        $('.last_price_td').show();
                        setPricewithTax()
                        totalCalcuationAfterChange()
                        addTotalDiscount();
                    }
                });

            });

            $(document).on('click', '.delete_product', function() {
                var whichtr = $(this).closest("tr");
                var id = $(this).data('id');

                whichtr.remove();
                let total_quantity = 0;
                let total_amount = 0;
                $.each($('.quantity'), function(index, value) {
                    let amount = $(this).val();
                    total_quantity += parseFloat(amount);
                });
                $.each($('.product_subtotal'), function(index, value) {
                    let amount = $(this).text();
                    total_amount += parseFloat(amount);
                });
                $('.total_price').text(total_amount);
                $('.total_amount').val(total_amount);
                let discount = parseFloat($('.total_discount').val());
                let tax = parseFloat($('.total_tax').val());
                let shipping_charge = parseFloat($('.shipping_charge').val());
                let other_charge = parseFloat($('.other_charge').val());
                let ProductTaxTotal = parseFloat($('.product_tax_input').val());

                let vat = parseFloat($('.total_vat').val());
                let calculated_discount = 0;
                let calculated_vat = 0;
                if (vat > 0) {
                    calculated_vat = ((total_amount - discount) / 100) * vat;
                }

                if (discount > 0) {
                    calculated_discount = discount;
                }
                let final_amount = (total_amount + calculated_vat) - calculated_discount;
                $('.total_amount_tr').text((final_amount).toFixed(2));
                $('.total_amount_display').text((final_amount).toFixed(2));
                if (total_quantity > 0 || !isNaN(total_quantity)) {
                    $('.total_quantity').text(total_quantity);
                    $('.total_quantity').val(total_quantity);
                } else {
                    $('.total_quantity').text(0);
                    $('.total_quantity').val(0);
                }

                productTax();

                $.ajax({
                    url: "{{ route('item.session.delete') }}",
                    method: "POST",
                    data: {
                        id: id,
                        _toke: "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        toastr.success(result.success);
                    }
                })
            });

            var timer = '';

            $(document).on('click', '.reset-product-list', function() {
                window.location.reload();
            });

            $(document).on('input', '#search_keyword_id', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    loadProductBySearch();
                }, 1000);
            });

            $(document).on('change', '#category_select_id', function() {
                loadProductBySearch();
            });

            $(document).on('change', '#brand_select_id', function() {
                loadProductBySearch();
            });

            $(document).on('change', '#model_select_id', function() {
                loadProductBySearch();
            });

            $(document).on('click', '.loadmore_btn1', function() {
                var totalCurrentResult = $('.total_product_count').val();
                var totalComboResult = $('.total_combo_count').val();
                let category_id = $('.product_category').val();
                let brand_id = $('.product_brand').val();
                let model_id = $('.product_model').val();
                let value = $('.search_product').val();
                let pos_view = 2;
                $.ajax({
                    url: "{{ route('pos-order.load.Product') }}",
                    method: "POST",
                    data: {
                        skip: totalCurrentResult,
                        comboSkip: totalComboResult,
                        category_id: category_id,
                        brand_id: brand_id,
                        model_id: model_id,
                        search_keyword: value,
                        pos_view: pos_view,
                        _token: "{{ csrf_token() }}",
                    },
                    beforeSend: function() {
                        $(".demo_wait").show();
                    },
                    success: function(response) {
                        $(".demo_wait").hide()
                        $('.total_product_count').val(response.single_skip);
                        $('.total_combo_count').val(response.combo_skip);
                        if (response.products == '')
                            $(".loadmore_btn1").addClass('d-none');
                        else
                            $(".loadmore_btn1").removeClass('d-none');

                        $("#all_products_tbl").append(response.products);
                    }
                })
            });
            $(document).on('click', '.loadmore_btn', function() {
                var totalCurrentResult = $('.all_products div.product_thumb').length;
                let category_id = $('.product_category').val();
                let brand_id = $('.product_brand').val();
                let model_id = $('.product_model').val();
                let value = $('.product_info').val();
                let pos_view = 1;

                $.ajax({
                    url: "{{ route('pos-order.load.ProductGrid') }}",
                    method: "POST",
                    data: {
                        skip: totalCurrentResult,
                        category_id: category_id,
                        brand_id: brand_id,
                        model_id: model_id,
                        value: value,
                        pos_view: pos_view,
                        _token: "{{ csrf_token() }}",
                    },
                    beforeSend: function() {
                        $(".demo_wait").show();
                    },
                    success: function(response) {
                        $(".demo_wait").hide()

                        if (response.loadBtn == 0)
                            $(".loadmore_btn").hide();
                        else
                            $(".loadmore_btn").show();

                        $(".all_products").append(response.products);
                    }
                })
            })
        });


        function modal_close() {
            $('#invoice').remove();
            $('.modal-backdrop').remove();
        }

        function loadProductBySearch() {

            let category_id = $('.product_category').val();
            let brand_id = $('.product_brand').val();
            let model_id = $('.product_model').val();
            let value = $('.search_product').val();

            let wait = $('.demo_wait');
            if (wait.is(":hidden"))
                wait = wait.show();
            let selector = $('.all_products');
            let selector2 = $('#all_products_tbl');

            selector.children().hide();
            selector2.children().hide();
            wait.show();

            $.ajax({
                method: "POST",
                url: "{{ route('pos-order.find.Product') }}",
                data: {
                    search_keyword: value,
                    brand_id: brand_id,
                    category_id: category_id,
                    model_id: model_id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    wait.hide();
                    if ($('#pos_view_config').val() == 1) {
                        $('.loadmore_btn').hide();
                        $('.all_products').html('');
                        $(".all_products").append(response.products);
                        if (response.products == '')
                            $(".loadmore_btn1").addClass('d-none');
                        else
                            $(".loadmore_btn1").removeClass('d-none');
                    } else {
                        $('#all_products_tbl').html('');
                        $("#all_products_tbl").append(response.products);
                        if (response.products == '')
                            $(".loadmore_btn").addClass('d-none');
                        else
                            $(".loadmore_btn").removeClass('d-none');
                    }


                }
            })
        }

        function multiplePaymentModal() {
            $('.pay_now_grn_btn').prop('disabled', true);
            var a = $("input[name='min_sell_qty[]']").map(function() {
                return $(this).val();
            }).get();
            downPaymentAmount();
            if ($.inArray('1', a) == -1) {
                var customer_id = $('.customer').val();
                if ($('#installment_status').is(':checked') == true) {
                    var paying_amount = parseFloat($('.down_payment_money').val());
                } else {
                    var paying_amount = parseFloat($('.total_amount').val());
                }
                var total_amount = parseFloat($('.total_amount').val());

                var total_qty = $('.total_quantity').val();
                $('#due_btn').val(0);
                if (total_amount > 0) {
                    $.post('{{ route('pos_multiple_payment') }}', {
                        _token: '{{ csrf_token() }}',
                        customer_id: customer_id,
                        total_amount: total_amount,
                        paying_amount: paying_amount,
                        total_qty: total_qty
                    }, function(data) {
                        $('.pay_now_grn_btn').prop('disabled', false);
                        $('#multiple_payment_form').html(data);
                        $('#Pos_Payment_Multiple').modal('show');
                        $('.quick_cash_number').hide();
                        $('select').niceSelect();
                    });
                } else {
                    $('.pay_now_grn_btn').prop('disabled', false);
                    toastr.warning('At least select one Item first !');
                }
            } else {
                $('.pay_now_grn_btn').prop('disabled', false);
                toastr.warning('Opps!! You cannot sale below min price !');
            }
        }

        $(document).on('keyup', '.down_payment', function(e) {
            downPaymentAmount();
        });

        function downPaymentAmount() {
            var total_amount = parseFloat($('.total_amount').val());
            var down_payment_percentage = parseFloat($('.down_payment').val());
            var down_payment_money = ((total_amount * down_payment_percentage) / 100);
            if (!isNaN(down_payment_money)) {
                $('.down_payment_money').val((down_payment_money).toFixed(2));
            } else {
                $('.down_payment_money').val(0);
            }

        }

        $(document).on('click', '#installment_status', function(e) {
            if ($('#installment_status').is(':checked') == true) {
                $('.installment_rld_div').removeClass('d-none');
                $('.down_payment_div').removeClass('d-none');
                downPaymentAmount();
            } else {
                $('.installment_rld_div').addClass('d-none');
                $('.down_payment_div').addClass('d-none');
            }
        });

        function cashPaymentModal() {
            var customer_id = $('.customer').val();
            var total_amount = $('.total_amount').val();
            var total_qty = $('.total_quantity').val();
            if (total_amount > 0) {
                $.post('{{ route('pos_cash_payment') }}', {
                    _token: '{{ csrf_token() }}',
                    customer_id: customer_id,
                    total_amount: total_amount,
                    total_qty: total_qty
                }, function(data) {
                    $('#multiple_payment_form').html(data);
                    $('#Pos_Payment_Multiple').modal('show');
                    $('select').niceSelect();
                });
            } else {
                toastr.warning('At least select one Item first !');
            }
        }

        function makeitDraft() {
            event.preventDefault();
            var a = $("input[name='min_sell_qty[]']").map(function() {
                return $(this).val();
            }).get();
            if ($.inArray('1', a) == -1) {
                $('#draft').val('draft');
                var total_amount = $('.total_amount').val();
                if (total_amount > 0) {
                    $('.pos_form').submit();
                }
            } else {
                toastr.warning('Opps!! You cannot make it draft below min selling price !');
            }
        }

        function draftListModal() {
            event.preventDefault();
            $.get('{{ route('get_draft_list') }}', function(data) {
                $('#draftList_modal').html(data);
                $('#draft_list').modal('show');
                $('select').niceSelect();
            });
        }

        function disableAll() {
            $('.draft_btn').prop('disabled', true);
            $('.draft_list_btn').prop('disabled', true);
            $('.cash_btn').prop('disabled', true);
            $('.multyPay_btn').prop('disabled', true);
            $('.due_btn').prop('disabled', true);
        }

        function enableAll() {
            $('.draft_btn').prop('disabled', false);
            $('.draft_list_btn').prop('disabled', false);
            $('.cash_btn').prop('disabled', false);
            $('.multyPay_btn').prop('disabled', false);
            $('.due_btn').prop('disabled', false);
        }

        function demo(el) {
            $('#product_details tr').remove();
            $.post('{{ route('get_draft_list_product_info') }}', {
                _token: '{{ csrf_token() }}',
                order_id: el
            }, function(data) {
                $('#draft_list').modal('hide');
                $('#product_details').append(data);
                if ($("select[multiple].active.multypol_check_select").length > 0) {
                    $("select[multiple].active.multypol_check_select").multiselect({
                        columns: 1,
                        placeholder: "Select",
                        search: true,
                        searchOptions: {
                            default: "Select",
                        },
                        selectAll: true,
                    });
                }
                setPricewithTax();
            });
        }

        function saleDetails() {
            let customer_id = $('.customer').val();

            if (customer_id != 'customer-1') {
                $('.due_btn').show();
                $('.installment_mod_div').removeClass('hide_element');
                $.ajax({
                    method: 'POST',
                    url: "{{ route('customer.details') }}",
                    data: {
                        customer_id: customer_id,
                        pos: 'pos',
                        _token: "{{ csrf_token() }}",
                    },

                    success: function(result) {
                        $('.due_row').show();
                        if (result.credit_limit)
                            credit_limit = parseFloat(result.credit_limit);

                        if (customer_id != 1 && result.due > credit_limit) {
                            toastr.warning('Your due is greater than your credit limit');
                        }
                        $('.customer_due').show();
                        $('.balance_due').text(' ' + result.due);

                        if (result.url) {
                            $('.customer_invoice').show();
                            url = 1;
                            $('.invoice_link').text(' ' + result.invoice);
                        } else {
                            $('.customer_invoice').hide();
                            url = 0;
                            let tfoot = $('.pos_tfoot tr th').length;
                            if (tfoot == 7) {
                                $('.pos_tfoot tr th:last').remove();
                            }
                        }
                    }
                })
            } else {
                credit_limit = 0;
                $('.due_row').hide();
                $('.installment_mod_div').addClass('hide_element');
                // $('.last_price').hide();
                $('.due_btn').hide();
            }
            setPricewithTax()
        }

        function invoiceDetail() {
            let customer_id = $('.customer').val();
            if (url == 1) {
                $.ajax({
                    method: 'POST',
                    url: "{{ route('get_sale_details') }}",
                    data: {
                        customer_id: customer_id,
                        pos: 'pos',
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(result) {
                        $('.invoice_details').html(result);
                        $('#sale_info_modal').modal('show');
                    }
                })
            }
        }

        function specificInvoiceDetail(el) {
            let customer_id = $('.contact_type').val();
            let id = el;
            $.ajax({
                method: 'POST',
                url: "{{ route('get_sale_details_specific') }}",
                data: {
                    customer_id: customer_id,
                    id: id,
                    _token: "{{ csrf_token() }}",
                },

                success: function(result) {
                    $('.invoice_details').html(result);
                    $('#sale_info_modal').modal('show');
                }
            })
        }

        function setPricewithTax() {
            let amount = 0;
            $.each($('.product_price'), function(index, value) {
                var whichtr = $(this).closest("tr");
                var qty = parseFloat(whichtr.find('.quantity').val());
                var discount_percent = parseFloat(whichtr.find('.discount').val());
                var min_price = parseFloat(whichtr.find('.product_min_price').val());

                if (parseInt($('#enable_gst').val()) == 0) {
                    var a = parseFloat(whichtr.find('.product_price').attr("data-product-price"));
                    var b = parseFloat(whichtr.find('.tax').val()) / 100;
                } else {
                    if ($('.gst_group').find(":selected").val() == "igst") {
                        var a = parseFloat(whichtr.find('.product_price').attr("data-product-price"));
                        var b = parseFloat(whichtr.find('.igst').val()) / 100;
                    }
                    if ($('.gst_group').find(":selected").val() == "cgst") {
                        var a = parseFloat(whichtr.find('.product_price').attr("data-product-price"));
                        var b = parseFloat(whichtr.find('.cgst').val()) / 100;
                    }
                    if ($('.gst_group').find(":selected").val() == "sgst") {
                        var a = parseFloat(whichtr.find('.product_price').attr("data-product-price"));
                        var b = parseFloat(whichtr.find('.sgst').val()) / 100;
                    }
                    if ($('.gst_group').find(":selected").val() == "cess") {
                        var a = parseFloat(whichtr.find('.product_price').attr("data-product-price"));
                        var b = parseFloat(whichtr.find('.cess').val()) / 100;
                    }
                }
                if (discount_percent > 0) {
                    var discount = parseFloat(discount_percent / 100 * a);
                } else {
                    var discount = 0;
                }
                var subtotalamount = parseFloat(a * qty);
                var only_subtotal = parseFloat(a * qty - discount * qty);
                var only_min = parseFloat(min_price * qty);
                if (only_subtotal < only_min) {
                    var max_discount = 100 * parseFloat((subtotalamount - only_min)) / subtotalamount;
                    whichtr.find('.discount').val(max_discount.toFixed(2));
                    discount = (max_discount > 0) ? parseFloat(discount_percent / 100 * a) : 0;
                }
                amount = (a + (b * (a - discount)) - discount) * qty;
                whichtr.find('.product_price').val(a.toFixed(2));
                whichtr.find('.product_subtotal').text(amount.toFixed(2));
                priceCalc(whichtr.find('.product_price').attr('data-sku-id'), whichtr.find('.product_price').attr(
                    'data-type'));
            });
        }

        function totalCalcuationAfterChange() {
            let total_quantity = 0;
            let total_amount = 0;
            $.each($('.quantity'), function(index, value) {
                let amount = $(this).val();
                total_quantity += parseFloat(amount);
            });
            $.each($('.product_subtotal'), function(index, value) {
                let amount = $(this).text();
                total_amount += parseFloat(amount);
            });
            $('.total_price').text(total_amount);
            $('.payable_price').text(total_amount);
            $('.total_amount').val(total_amount);
            let vat = parseFloat($('.total_vat').val());
            let discount = parseFloat($('.total_discount').val());
            let ProductTaxTotal = parseFloat($('.product_tax_input').val());
            let calculated_discount = 0;
            let calculated_vat = 0;
            if (vat > 0) {
                calculated_vat = (total_amount * vat) / 100;
            }

            if (discount > 0) {
                calculated_discount = (total_amount * discount) / 100;
            }

            let final_amount = (total_amount + calculated_vat) - calculated_discount;

            $('.total_amount_tr').text((final_amount).toFixed(2));
            $('.total_amount_display').text((final_amount).toFixed(2));
            $('.amount').val(total_amount);
            let total_tax = 0;
            $.each($('.tax'), function(index, value) {
                let amount = $(this).val();
                if (amount)
                    total_tax += parseInt(amount);
            });

            if (total_tax > 0 || !isNaN(total_tax)) {
                $('.total_tax').text(total_tax);
            } else {
                total_tax = $('.total_tax').text();
                $('.total_tax').text(total_tax);
            }
            if (total_quantity > 0 || !isNaN(total_quantity)) {
                $('.total_quantity').text(total_quantity);
                $('.total_quantity').val(total_quantity);
            } else {
                $('.total_quantity').text(0);
                $('.total_quantity').val(0);
            }
            productTax();
        }

        function addTotalDiscount() {
            disableAll();
            addTotalVat();
            enableAll();
        }

        function addShippingCharge() {
            disableAll();
            addTotalVat();
            enableAll();
        }

        function getPosViewUpdate(el, value) {
            $.post('{{ route('get_change_pos_view') }}', {
                _token: '{{ csrf_token() }}',
                pos_view: value
            }, function(data) {
                if (data == 1) {
                    location.reload();
                    toastr.success("Pos view has been changed successfully");
                } else {
                    toastr.error("Pos view has been failed to change");
                }
            });
        }

        function printPostInvoice(divName) {
            var preview = window.open("", "MsgWindow", "width=400,height=800");
            preview.document.getElementsByTagName('body')[0].innerHTML = '';
            var printContents = document.getElementById(divName).innerHTML;
            preview.document.write(printContents);
        }

        // POS CALCULATION

        function productTax() {
            let product_tax = 0;
            let product_dis = 0;
            if (parseInt($('#enable_gst').val()) == 0) {
                $.each($('.tax'), function(index, value) {
                    let amount = $(this).attr('net-sub-total');
                    product_tax += parseFloat(amount);
                });
            } else {
                if ($('.gst_group').find(":selected").val() == "igst") {
                    $.each($('.igst'), function(index, value) {
                        let amount = $(this).attr('net-sub-total');
                        product_tax += parseFloat(amount);
                    });
                } else if ($('.gst_group').find(":selected").val() == "cgst") {
                    $.each($('.cgst'), function(index, value) {
                        let amount = $(this).attr('net-sub-total');
                        product_tax += parseFloat(amount);
                    });
                } else if ($('.gst_group').find(":selected").val() == "sgst") {
                    $.each($('.sgst'), function(index, value) {
                        let amount = $(this).attr('net-sub-total');
                        product_tax += parseFloat(amount);
                    });
                } else {
                    $.each($('.cess'), function(index, value) {
                        let amount = $(this).attr('net-sub-total');
                        product_tax += parseFloat(amount);
                    });
                }
            }
            if (product_tax >= 0) {
                $('.product_tax_input').val(product_tax.toFixed(2));
            } else {
                $('.product_tax_input').val(0);
            }

            $.each($('.discount'), function(index, value) {
                let Disamount = $(this).attr('net-sub-total');
                product_dis += parseFloat(Disamount);
            });
            if (product_dis >= 0) {
                $('.product_discount').val(product_dis.toFixed(2));
            } else {
                $('.product_discount').val(0);
            }
        }

        function calcutionTaxDiscount(tr) {
            if (parseInt($('#enable_gst').val()) == 0) {
                let taxParcentage = parseFloat(tr.find('.tax').val());
                let productPrice = parseFloat(tr.find('.product_price').attr("data-product-price"));
                let discount = parseFloat(tr.find('.discount ').val());
                let qty = parseInt(tr.find('.quantity').val());

                let discount_price = (productPrice * discount) / 100;
                let productTax = (productPrice - discount_price) * taxParcentage / 100;
                let netSubTotal = ((productPrice - discount_price) * qty) * taxParcentage / 100;
                tr.find('.tax').attr('net-sub-total', netSubTotal)
                tr.find('.product_tax_amount').val(productTax.toFixed(2))
                if (discount > 0) {
                    let netDisTotal = discount_price * qty;
                    tr.find('.discount').attr('net-sub-total', netDisTotal)
                } else {
                    tr.find('.discount').attr('net-sub-total', 0);
                }
            } else {
                if ($('.gst_group').find(":selected").val() == "igst") {
                    let taxParcentage = parseFloat(tr.find('.igst').val());
                    let productPrice = parseFloat(tr.find('.product_price').attr("data-product-price"));
                    let discount = parseFloat(tr.find('.discount ').val());
                    let qty = parseInt(tr.find('.quantity').val());

                    let productTax = productPrice * taxParcentage / 100;
                    let discount_price = (productPrice * discount) / 100;
                    let netSubTotal = ((productPrice - discount_price) * qty) * taxParcentage / 100;
                    tr.find('.igst').attr('net-sub-total', netSubTotal)
                    tr.find('.product_tax_amount').val(productTax.toFixed(2))
                    if (discount > 0) {
                        let netDisTotal = discount_price * qty;
                        tr.find('.discount').attr('net-sub-total', netDisTotal)
                    } else {
                        tr.find('.discount').attr('net-sub-total', 0);
                    }
                }
                if ($('.gst_group').find(":selected").val() == "cgst") {
                    let taxParcentage = parseFloat(tr.find('.cgst').val());
                    let productPrice = parseFloat(tr.find('.product_price').attr("data-product-price"));
                    let discount = parseFloat(tr.find('.discount ').val());
                    let qty = parseInt(tr.find('.quantity').val());
                    let productTax = productPrice * taxParcentage / 100;
                    let discount_price = (productPrice * discount) / 100;
                    let netSubTotal = ((productPrice - discount_price) * qty) * taxParcentage / 100;
                    tr.find('.cgst').attr('net-sub-total', netSubTotal)
                    tr.find('.product_tax_amount').val(productTax.toFixed(2))
                    if (discount > 0) {
                        let netDisTotal = discount_price * qty;
                        tr.find('.discount').attr('net-sub-total', netDisTotal)
                    } else {
                        tr.find('.discount').attr('net-sub-total', 0);
                    }
                }
                if ($('.gst_group').find(":selected").val() == "sgst") {
                    let taxParcentage = parseFloat(tr.find('.sgst').val());
                    let productPrice = parseFloat(tr.find('.product_price').attr("data-product-price"));
                    let discount = parseFloat(tr.find('.discount ').val());
                    let qty = parseInt(tr.find('.quantity').val());
                    let productTax = productPrice * taxParcentage / 100;
                    let discount_price = (productPrice * discount) / 100;
                    let netSubTotal = ((productPrice - discount_price) * qty) * taxParcentage / 100;
                    tr.find('.sgst').attr('net-sub-total', netSubTotal)
                    tr.find('.product_tax_amount').val(productTax.toFixed(2))
                    if (discount > 0) {
                        let netDisTotal = discount_price * qty;
                        tr.find('.discount').attr('net-sub-total', netDisTotal)
                    } else {
                        tr.find('.discount').attr('net-sub-total', 0);
                    }
                }
                if ($('.gst_group').find(":selected").val() == "cess") {
                    let taxParcentage = parseFloat(tr.find('.cess').val());
                    let productPrice = parseFloat(tr.find('.product_price').attr("data-product-price"));
                    let discount = parseFloat(tr.find('.discount ').val());
                    let qty = parseInt(tr.find('.quantity').val());
                    let productTax = productPrice * taxParcentage / 100;
                    let discount_price = (productPrice * discount) / 100;
                    let netSubTotal = ((productPrice - discount_price) * qty) * taxParcentage / 100;
                    tr.find('.cess').attr('net-sub-total', netSubTotal)
                    tr.find('.product_tax_amount').val(productTax.toFixed(2))
                    if (discount > 0) {
                        let netDisTotal = discount_price * qty;
                        tr.find('.discount').attr('net-sub-total', netDisTotal)
                    } else {
                        tr.find('.discount').attr('net-sub-total', 0);
                    }
                }
            }
        }

        function addDiscount(id, type) {
            setPricewithTax();
        }

        function productDiscount() {
            let product_discounts = 0;
            $.each($('.discount'), function(index, value) {
                let discountPercentage = $(this).val();
                let tr = $(this).parent().parent();
                let qty = parseInt(tr.find('.quantity').val());
                let product_price = parseFloat(tr.find('.product_price').attr("data-product-price"));
                let discount_amount = product_price * discountPercentage / 100;
                let totalDiscount = discount_amount * qty;
                product_discounts += parseInt(totalDiscount);
                calcutionTaxDiscount(tr);
                productTax(1, 2)
            });
        }


        //for total price calculation
        function priceCalc(id, type) {
            disableAll();
            let price = $(".product_price_" + type + id).val();
            let min_sell_qty = parseInt($("#min_sell_qty").val());
            let taxRate = 0;
            let quantity = $('.quantity_' + type + id).val();
            let totalAmountProduct = (price / (100 + taxRate)) * taxRate
            let productTaxValue = 0;
            let min_price = $(".product_min_price_" + type + id).val();
            let discountProduct = $('.discount_' + type + id).val();

            if (parseInt($('#enable_gst').val()) == 0) {
                taxRate = parseFloat($('.tax_' + type + id).val());
            } else {
                if ($('.gst_group').find(":selected").val() == "igst") {
                    taxRate = parseFloat($('.igst_' + type + id).val());
                }
                if ($('.gst_group').find(":selected").val() == "cgst") {
                    taxRate = parseFloat($('.cgst_' + type + id).val());
                }
                if ($('.gst_group').find(":selected").val() == "sgst") {
                    taxRate = parseFloat($('.sgst_' + type + id).val());
                }
                if ($('.gst_group').find(":selected").val() == "cess") {
                    taxRate = parseFloat($('.cess_' + type + id).val());
                }
            }

            if (taxRate > 0) {
                let totalAmountProduct = (price / (100 + taxRate)) * taxRate;
                if (parseInt($('#enable_gst').val()) == 0) {
                    $('.tax_' + type + id).attr('net-sub-total', totalAmountProduct);
                    productTaxValue = $('.tax_' + type + id).attr('net-sub-total');
                } else {
                    if ($('.gst_group').find(":selected").val() == "igst") {
                        $('.igst_' + type + id).attr('net-sub-total', totalAmountProduct);
                        productTaxValue = $('.igst_' + type + id).attr('net-sub-total');
                    }
                    if ($('.gst_group').find(":selected").val() == "cgst") {
                        $('.cgst_' + type + id).attr('net-sub-total', totalAmountProduct);
                        productTaxValue = $('.cgst_' + type + id).attr('net-sub-total');
                    }
                    if ($('.gst_group').find(":selected").val() == "sgst") {
                        $('.sgst_' + type + id).attr('net-sub-total', totalAmountProduct);
                        productTaxValue = $('.sgst_' + type + id).attr('net-sub-total');
                    }
                    if ($('.gst_group').find(":selected").val() == "cess") {
                        $('.cess_' + type + id).attr('net-sub-total', totalAmountProduct);
                        productTaxValue = $('.cess_' + type + id).attr('net-sub-total');
                    }
                }

            }
            let sub_total = 0;
            let productDiscountAmount = 0;
            if (parseFloat(price) < parseFloat(min_price)) {
                $(".product_price_" + type + id).addClass('red_border');
                $(".min_sell_qty_" + type + id).val(1);
            } else {
                $(".product_price_" + type + id).removeClass('red_border');
                $(".min_sell_qty_" + type + id).val(2);
            }
            if (discountProduct > 0) {
                let basePrice = price;
                let discountAmountCal = (basePrice / 100) * discountProduct;
                if (taxRate > 0) {
                    let productNewTax = (basePrice - discountAmountCal) * taxRate / 100;
                    let pricseAfterDiscount = basePrice - discountAmountCal + productNewTax;
                    sub_total = pricseAfterDiscount * quantity;
                } else {
                    sub_total = (basePrice - discountAmountCal) * quantity;
                }
            } else {
                sub_total = (price + productTaxValue) * quantity;
            }

            let total_quantity = 0;
            $.each($('.quantity'), function(index, value) {
                let amount = $(this).val();
                total_quantity += parseInt(amount);
            });

            if (total_quantity > 0 || !isNaN(total_quantity)) {
                $('.total_quantity').text(total_quantity);
                $('.total_quantity').val(total_quantity);
            } else {
                total_quantity = $('.total_quantity').text();
                $('.total_quantity').text(total_quantity);
                $('.total_quantity').val(total_quantity);
            }

            let total_amount = 0;
            let discount = parseFloat($('.total_discount').val());
            let vat = parseFloat($('.total_vat').val());

            let ProductTaxTotal = parseFloat($('.product_tax_input').val());
            $.each($('.product_subtotal'), function(index, value) {
                let amount = $(this).text();
                total_amount += parseFloat(amount);
            });
            $('.amount').val(total_amount);

            let calculated_discount = 0;
            let calculated_vat = 0;
            if (vat > 0) {
                calculated_vat = ((total_amount - discount) / 100) * vat;
            }
            if (discount > 0) {
                calculated_discount = discount;
            }
            let final_amount = (total_amount + calculated_vat) - calculated_discount;

            $('.payable_price').text((final_amount).toFixed(2));
            $('.total_amount').val((final_amount).toFixed(2));
            $('.total_amount_tr').text((final_amount).toFixed(2));
            $('.total_amount_display').text((final_amount).toFixed(2));
            let tr = $('.quantity_' + type + id).parent().parent();
            calcutionTaxDiscount(tr);
            productTax()
            enableAll();
        }

        //quantity increase and decrease after add
        function addQuantity(id, type) {

            let datatype = $('.quantity_' + type + id).data('type');
            let price = $(".product_price_" + type + id).val();
            let producttax = 0;
            let quantity = $('.quantity_' + type + id).val();
            let house = $('.house').val();
            let discountProduct = $('.discount_' + type + id).val();
            let sub_total = 0;
            let taxRate = 0;
            if (parseInt($('#enable_gst').val()) == 0) {
                taxRate = parseFloat($('.tax_' + type + id).val());
                producttax = $('.tax_' + type + id).attr('net-sub-total');
            } else {
                if ($('.gst_group').find(":selected").val() == "igst") {
                    taxRate = parseFloat($('.igst_' + type + id).val());
                    producttax = $('.igst_' + type + id).val();
                }
                if ($('.gst_group').find(":selected").val() == "cgst") {
                    taxRate = parseFloat($('.cgst_' + type + id).val());
                    producttax = $('.cgst_' + type + id).val();
                }
                if ($('.gst_group').find(":selected").val() == "sgst") {
                    taxRate = parseFloat($('.sgst_' + type + id).val());
                    producttax = $('.sgst_' + type + id).val();
                }
                if ($('.gst_group').find(":selected").val() == "cess") {
                    taxRate = parseFloat($('.cess_' + type + id).val());
                    producttax = $('.cess_' + type + id).val();
                }
            }

            $.ajax({
                method: 'POST',
                url: "{{ route('check.quantity') }}",
                data: {
                    id: id,
                    type: datatype,
                    house: house,
                    quantity: quantity
                },
                beforeSend: function() {
                    disableAll();
                },
                success: function(result) {
                    if ($.isNumeric(result.stock)) {
                        toastr.error(result.msg);
                        $('.quantity_' + type + id).val(result.stock);
                        addQuantity(id, type);
                    } else {
                        setPricewithTax()
                    }
                }
            })
        }

        //for tax amount calucation
        function addTotalVat() {
            disableAll();
            let vat = parseFloat($('.total_vat').val());
            let product_tax_total = parseFloat($('.product_tax_input').val());
            let discount = parseFloat($('.total_discount').val());

            let total_amount = 0;
            $.each($('.product_subtotal'), function(index, value) {
                var whichtr = $(this).closest("tr");
                let amount = parseFloat(whichtr.find('.product_subtotal').text());
                total_amount += amount;
            });
            // total_amount += product_tax_total;
            $('.amount').val(total_amount);

            let calculated_discount = 0;
            let calculated_vat = 0;

            if (vat > 0) {
                if (vat > 0) {
                    calculated_vat = ((total_amount - discount) * vat) / 100;
                }
            }

            if (discount > 0) {
                calculated_discount = discount;
            }
            let shipping_charge = parseFloat($('.shipping_charge').val());

            let final_amount = (total_amount + calculated_vat + shipping_charge) - calculated_discount;

            $('.payable_price').text((final_amount).toFixed(2));
            $('.other_product_tax_input').val((calculated_vat).toFixed(2));
            $('.total_amount').val((final_amount).toFixed(2));
            $('.total_amount_tr').text((final_amount).toFixed(2));
            $('.total_amount_display').text((final_amount).toFixed(2));
            enableAll();
        }

        // for change branch or showroom with clear session product data
        $(document).on('change', '.select_showroom', function() {
            window.location = '{{ route('clear.products') }}';
            let id = $(this).val();
            $.ajax({
                method: "POST",
                url: "{{ route('change.showroom') }}",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}",
                },
                success: function(result) {
                    window.location.reload();
                }
            })
        });
    </script>
    @if (session()->has('sale'))
        <script>
            $(document).ready(function() {
                printPostInvoice('printablePos');
            });
        </script>
        {{ session()->forget('sale') }}
    @endif
    <script src="{{ Module::asset('core:country_select.js') }}"></script>
    <script src="{{ Module::asset('core:state_select.js') }}"></script>
    <script src="{{ Module::asset('core:city_select.js') }}"></script>
</body>

</html>
