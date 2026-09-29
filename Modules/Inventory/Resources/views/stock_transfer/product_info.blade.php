@extends('backEnd.master')
@section('mainContent')
    <div class="row">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('report.Product Information')}}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">
                    <div id="item_list_tbl">
                        @include('inventory::stock_transfer.paginate.info_list')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'product_info_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ __('product.Image') }}','image'],['3','{{ __('common.Name') }}','name'],['4','{{ __('common.SKU') }}','SKU'],['5','{{ __('product.Model') }}','model'],['6','{{ __('product.Brand') }}','brand'],['7','{{ __('product.In Stock') }}','in_stock'],['8','{{ __('product.Purchase Price') }}','purchase_price'],['9','{{ __('product.Selling Price') }}','sell_price']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['product_info_list']) }}">

@endsection
@push("scripts")
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
@endpush
