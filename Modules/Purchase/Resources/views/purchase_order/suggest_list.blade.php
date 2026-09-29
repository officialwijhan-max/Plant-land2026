@extends('backEnd.master')
@section('mainContent')
    <form action="{{ route('convert.purchase') }}" method="get">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="white_box_50px box_shadow_white mb-50 pb-5">
                    <div class="row">
                        <div class="col-3">
                            <div class="box_header common_table_header">
                                <div class="main-title d-md-flex">
                                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('purchase.Stock Alert List')}} </h3>
                                </div>

                            </div>
                        </div>
                        {{-- <form action="{{ route('purchase.suggest') }}" method="get" class="filter_form"> --}}
                            <div class="col-md-12">
                                <div class="primary_input mb-15">
                                    <select class="select2 mb-15 single_select primary_singleSelect supplier" onchange="supplierProducts()" required name="supplier" id="supplier_id">
                                        <option value="0">{{__('purchase.Select Supplier')}}</option>
                                    </select>
                                </div>
                            </div>
                        {{-- </form> --}}
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <ul class="d-flex">
                    <li class="purchase_btn" style="display: none;">
                        <button class="primary-btn radius_30px mb-10 mr-10 fix-gr-bg" type="submit"><i class="ti-plus"></i>{{__('purchase.Purchase Order')}}</button>
                    </li>
                </ul>

                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table ">
                        <div id="item_list_tbl">
                            @include('purchase::purchase_order.paginate.stock_alert_list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <input type="hidden" name="suggest_url" id="suggest_url" value="{{ route('purchase.suggest',['supplier' => ':s_id']) }}">
    <input type="hidden" name="supplier_list_select_option" id="supplier_list_select_option" value="{{ route('supplier.supplier_select_list_option') }}">
    @include('backEnd.partials.approve_modal')
@endsection

@push("scripts")
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('core:contact_select.js') }}"></script>
<script>
    function supplierProducts() {
        let supplier = $('.supplier').val();
        
        $.ajax({
            url: "{{ route('filter.product.supplier') }}",
            method: "POST",
            data: {
                _token: "{{csrf_token()}}",
                supplier: supplier
            },
            success: function (data) {
                $('#item_list_tbl').html(data);
                $('.select_div').niceSelect();
            }
        })
    }

    function selectAllProduct() {
        let supplier = $('.supplier').val();

        if ($('.all_product_select').prop('checked') == true) {
            if (supplier)
                $('.purchase_btn').show();
            $.each($('.product_select'), function () {
                $(this).prop('checked', true)
            })
        } else {
            $('.purchase_btn').hide();
            $.each($('.product_select'), function () {
                $(this).prop('checked', false)
            })
        }
    }

    function selectProduct() {
        let supplier = $('.supplier').val();
        if ($('.product_select').prop('checked') == true && supplier) {
            $('.purchase_btn').show();
        } else
            $('.purchase_btn').hide();
    }
</script>
@endpush
