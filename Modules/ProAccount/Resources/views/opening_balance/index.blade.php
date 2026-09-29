@extends('backEnd.master')
@section('page-title', Settings("site_title") .' | '. trans('account.Opening Balance'))
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('account.Opening Balance') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('proaccount::opening_balance.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="edit_form"></div>
        @include('proaccount::opening_balance.create_contact')
        @include('proaccount::modals._deleteModalForAjax',['item_name' => trans('account.income')])
        @include('proaccount::modals._approved_voucher_delete',['item_name' => trans('account.income')])
        <input type="hidden" value="{{ route("pro-opening-balance.store") }}" id="store_url">
        <input type="hidden" value="{{ route("pro-opening-balance.edit", ":id") }}" id="edit_url">
        <input type="hidden" value="{{ route("pro-opening-balance.update",":id") }}" id="update_url">
        <input type="hidden" id="delete_url_1" value="{{ route('vouchers.destroy') }}">
        <input type="hidden" id="delete_url_2" value="{{ route('vouchers.destroy_approved') }}">
        <input type="hidden" id="details_url" name="details_url" value="{{ route('pro-get_voucher_details', ":id") }}">
        <div class="showModalHideColumn"></div>
        <div id="Voucher_info"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'opening_balance_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ __('account.Date') }}','date'],['3','{{ trans('account.txn_id') }}','txn_id'],['4','{{ __('sale.Reference No') }}','reference_no'],['5','{{ trans('account.amount') }}','amount'],['6','{{ trans('account.approved') }}','approved'],['7','{{ __('common.Action') }}','action']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['opening_balance_list']) }}">
    </section>
@endsection
@push('scripts')
    <script src="{{ Module::asset('account:voucher_view.js') }}"></script>
    <script src="{{ Module::asset('account:expense.js') }}"></script>
    <script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script src="{{ Module::asset('tables:table.js') }}"></script>
@endpush
