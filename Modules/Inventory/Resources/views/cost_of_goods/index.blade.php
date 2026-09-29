@extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('inventory.Product Costing') }} ({{__('inventory.Sales')}})</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('inventory::cost_of_goods.paginate.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'product_costing_sales')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.ID')}}','id'],['2','{{ __('product.Image') }}','image'],['3','{{ __('sale.Invoice No') }}','invoice_no'],['4','{{ __('common.Address') }}','address'],['5','{{ __('product.Product Name') }}','product_name'],['6','{{ __('inventory.Previous Stock') }}','previous_stock'],['7','{{ __('inventory.Newly added Stock') }}','newly_added_stock'],['8','{{ __('inventory.Last Costing Price (unit)') }}','last_costing_price_'],['9','{{ __('inventory.New Costing Price (unit)') }}','new_costing_price_']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['product_costing_sales']) }}">

    </section>
@endsection
@push("scripts")
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
@endpush
