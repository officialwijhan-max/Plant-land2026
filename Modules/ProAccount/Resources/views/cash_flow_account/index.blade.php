@extends('backEnd.master')
@section('page-title', Settings("site_title") .' | '. trans('account.cash_flow_account'))
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.cash_flow_account') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('proaccount::cash_flow_account.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="edit_form"></div>
        @include('proaccount::cash_flow_account.create')
        @include('proaccount::modals._deleteModalForAjax',['item_name' => __("proaccount::account.cash_flow_account")])
        <input type="hidden" value="{{ route("cash_flow_account.store") }}" id="store_url">
        <input type="hidden" value="{{ route("cash_flow_account.edit", ":id") }}" id="edit_url">
        <input type="hidden" value="{{ route("cash_flow_account.update",":id") }}" id="update_url">
        <input type="hidden" value="{{route('cash_flow_account.delete')}}" id="delete_url">
        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'cash_flow_account_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ trans('account.type') }}','type'],['3','{{ trans('account.code') }}','code'],['4','{{ trans('account.name') }}','name'],['5','{{ trans('account.status') }}','status'],['6','{{ __('common.Action') }}','action']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['cash_flow_account_list']) }}">
    </section>
@endsection
@push('scripts')
    <script src="{{ Module::asset('account:cash_flow_account.js') }}"></script>
    <script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script src="{{ Module::asset('tables:table.js') }}"></script>
@endpush
