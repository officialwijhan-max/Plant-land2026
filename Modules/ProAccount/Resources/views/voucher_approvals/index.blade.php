@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans('account.all_pending_voucher_lists'))
@push('styles')
    <style>
        .dataTables_filter > label {
            top: -5px!important;
        }
        div.dt-buttons {
            top: 2px!important;
        }
        .table_modal thead th {
            border-bottom: 2px solid var(--border_color);
            padding: 5px;
            text-align: center;
            font-weight: 500 !important;
            white-space: nowrap;
            margin-top: 5px;
            border-top: 2px solid var(--border_color) !important;
        }
        .table_modal_upper tbody td {
            padding: 5px 5px 5px 5px !important;
            font-weight: 400 !important;
            max-width: 5px;
        }
        .table_modal tbody td {
            padding: 5px 5px 5px 5px !important;
            font-weight: 400 !important;
            text-align: center;
            max-width: 5px;
        }
    </style>
@endpush
@section('mainContent')
    <form action="{{ route('voucher.all.approval') }}" method="post">
        @csrf
        <section class="admin-visitor-area">
            <div class="container-fluid p-0">
                <div class="row justify-content-between">
                    <div class="col-xl-12">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.all_pending_voucher_lists') }}</h3>
                                <a class="primary-btn radius_30px mb-10 mr-10 fix-gr-bg" href="{{ route('approved_voucher.index') }}" target="_blank">{{trans('account.approved_vouchers')}}</a>
                                <button class="primary-btn radius_30px mb-10 mr-10 fix-gr-bg d-none approve_btn" type="submit"><i class="ti-check"></i>{{ trans('account.approve_now') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table ">
                                <div id="item_list_tbl">
                                    @include('proaccount::voucher_approvals.paginates.list')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>

    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'pending_voucher_lists')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','#','checkbox'],['2','{{__('common.Sl')}}','id'],['3','{{ __('account.Date') }}','date'],['4','{{ trans('account.txn_id') }}','txn_id'],['5','{{ __('sale.Reference No') }}','reference_no'],['6','{{ trans('account.amount') }}','amount'],['7','{{ trans('account.approved') }}','approved'],['8','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['pending_voucher_lists']) }}">
    <input type="hidden" id="currency_sym" name="currency_sym" value="{{ Settings('currency_symbol') }}">
    <input type="hidden" id="details_url" name="details_url" value="{{ route('pro-get_voucher_details', ":id") }}">
    <input type="hidden" id="voucher_approval_url" value="{{ route('set_voucher_approval') }}">
    <div id="Voucher_info"></div>
    @include('backEnd.partials.approve_modal')
@endsection

@push("scripts")
    <script src="{{ Module::asset('account:voucher_app.js') }}"></script>
    <script src="{{ Module::asset('account:voucher_view.js') }}"></script>
    <script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script src="{{ Module::asset('tables:table.js') }}"></script>
@endpush
