@extends('backEnd.master')
@section('page-title', app('general_setting')->site_title .' | Quotations List')
@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('quotation.Quotations')}}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">
                    <!-- table-responsive -->
                    <div id="item_list_tbl">
                        @include('quotation::quotation.paginate.list')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'quotation_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.No')}}','id'],['2','{{ __('quotation.Date') }}','date'],['3','{{ __('quotation.Reference No') }}','invoice_no'],['4','{{ __('quotation.Customer') }}','customer'],['5','{{ __('quotation.Branch') }}','showroom'],['6','{{ __('quotation.User') }}','user'],['7','{{ __('common.Convert Status') }}','convert_status'],['8','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['quotation_list']) }}">
    
    @include('backEnd.partials.delete_modal')
    @include('backEnd.partials.approve_modal')

@endsection

@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
@endpush
