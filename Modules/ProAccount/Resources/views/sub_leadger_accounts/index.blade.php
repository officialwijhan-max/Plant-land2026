@extends('backEnd.master')
@section('page-title', Settings('site_title') . ' | ' . trans('account.sub_leadger_account'))
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.partner_account') }}</h3>

                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div class="">
                                <div id="item_list_tbl">
                                    @include('proaccount::sub_leadger_accounts.components.ledger_list')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()
                ->user()
                ->user_col_permissions->where('table_name', 'sub_leadger_list')
                ->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl"
                    value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name"
            value="[['1','{{ __('common.Sl') }}','id'],['2','{{ __('common.Type') }}','type'],['3','{{ trans('account.ledger') }}','ledger'],['4','{{ __('common.Code') }}','code'],['5','{{ __('common.Name') }}','name'],['6','{{ __('account.Balance') }}','balance'],['7','{{ __('common.Status') }}','status'],['8','{{ __('common.Action') }}']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self"
            value="{{ route('user_column_permission.show_with_self', ['sub_leadger_list']) }}">

        <input type="hidden" value="{{ route('leadger.get_data_list') }}" id="get_leadger_data_list">
    </section>
@endsection
@push('scripts')
    <script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script src="{{ Module::asset('tables:table.js') }}"></script>
    <script src="{{ Module::asset('account:sub-leadger.js') }}"></script>
@endpush
