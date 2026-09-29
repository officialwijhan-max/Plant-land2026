@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans('account.financial_years'))
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-between">
                <div class="col-xl-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.financial_years') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('proaccount::financial_years.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="closing_by_id" name="closing_by_id" value="{{ route('financial_years.closing_by_id', ":id") }}">
        <div id="close_details"></div>
        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'financial_years_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ trans('account.start_date') }}','start_date'],['3','{{ trans('account.end_date') }}','end_date'],['4','{{ trans('account.is_locked') }}','is_locked'],['5','{{ __('common.Action') }}','action']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['financial_years_list']) }}">
    </section>
@endsection

@push("scripts")
    <script src="{{ Module::asset('account:financial_years.js') }}"></script>
    <script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script src="{{ Module::asset('tables:table.js') }}"></script>
@endpush
