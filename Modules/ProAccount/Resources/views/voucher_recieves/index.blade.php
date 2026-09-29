@extends('backEnd.master',['datatable' => TRUE])
@section('page-title', Settings('site_title') .' | '. trans('account.voucher_recieve_list'))
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
            max-width: 5px;
            text-align: center;
        }
        .nowrap {
            white-space: nowrap;
        }
    </style>
    <link rel="stylesheet" href="{{asset('public/backEnd/css/daterangepicker.css')}}"/>
@endpush
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-between">
                <div class="col-xl-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.voucher_recieve_list') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('proaccount::voucher_recieves.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="currency_sym" name="currency_sym" value="{{ Settings('currency_symbol') }}">
        <input type="hidden" id="details_url" name="details_url" value="{{ route('pro-get_voucher_details', ":id") }}">
        <input type="hidden" id="delete_url_1" value="{{ route('vouchers.destroy') }}">
        <input type="hidden" id="delete_url_2" value="{{ route('vouchers.destroy_approved') }}">
        <div id="Voucher_info"></div>
        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'voucher_recieve_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" id="voucher_approval_url" value="{{ route('set_voucher_approval') }}">
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ __('account.Date') }}','date'],['3','{{ trans('account.txn_id') }}','txn_id'],['4','{{ __('sale.Reference No') }}','reference_no'],['5','{{ trans('account.amount') }}','amount'],['6','{{ trans('account.approved') }}','approved'],['7','{{ __('common.Action') }}','action']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['voucher_recieve_list']) }}">
    </section>

@include('proaccount::modals._deleteModalForAjax',['item_name' => trans("account.voucher_recieve")])
@include('proaccount::modals._approved_voucher_delete',['item_name' => trans('account.voucher_recieve')])
@endsection

@push("scripts")
    <script type="text/javascript" src="{{asset('public/backEnd/js/daterangepicker.min.js')}}"></script>
    <script src="{{ Module::asset('account:voucher_view.js') }}"></script>
    <script src="{{ Module::asset('account:journal_index.js') }}"></script>
    <script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script src="{{ Module::asset('tables:table.js') }}"></script>
@endpush
