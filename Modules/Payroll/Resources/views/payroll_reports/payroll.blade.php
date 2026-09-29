@extends('backEnd.master')
@section('mainContent')

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('payroll.Payroll Reports') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class="white_box_50px box_shadow_white">
                        <form class="" action="{{ route('payroll_reports.search') }}" method="GET">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ __('attendance.Select Role') }}</label>
                                        <select class="primary_select mb-15" name="role_id" id="role_id">
                                            <option selected disabled>{{__('attendance.Choose One')}}</option>
                                             @foreach (\Modules\RolePermission\Entities\Role::where('type', 'regular_user')->get() as $role)
                                                @isset($r)
                                                    <option value="{{ $role->id }}"@if ($r == $role->id) selected @endif>{{ $role->name }}</option>
                                                @else
                                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                @endisset
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{$errors->first('role_id')}}</span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ __('attendance.Select Month') }}</label>
                                        <select class="primary_select mb-15" name="month" id="month">
                                            @foreach ($months as $month)
                                                @isset($m)
                                                    <option value="{{ $month }}"@if ($m == $month) selected @endif>{{ __('common.'.$month) }}</option>
                                                @else
                                                    <option value="{{ $month }}">{{ __('common.'.$month) }}</option>
                                                @endisset
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{$errors->first('month')}}</span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ __('attendance.Select Year') }}</label>
                                        <select class="primary_select mb-15" name="year" id="year">
                                            @foreach (range(\carbon\Carbon::now()->year, 2015) as $year)
                                                @isset($y)
                                                    <option value="{{ $year }}"@if ($y == $year) selected @endif>{{ $year }}</option>
                                                @else
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endisset

                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{$errors->first('year')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-2 mt-3 text-right">
                                        <button type="submit" class="primary-btn btn-sm fix-gr-bg"id="save_button_parent"><i class="ti-search"></i>{{ __('attendance.Search') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @isset($items)
                    <div class="col-12">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('payroll.Payroll Reports') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table ">
                                <div id="item_list_tbl">
                                    @include('payroll::payroll_reports.paginates.list')
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'payroll_report_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.ID')}}','id'],['2','{{ __('common.Staff') }}','employee'],['3','{{ __('attendance.Staff ID') }}','staff_id'],['4','{{ __('role.Role') }}','role'],['5','{{ __('attendance.Month') }}','month'],['6','{{ __('payroll.Basic Salary') }}','basic_salary'],['7','{{ __('payroll.Gross Salary') }}','gross_salary'],['8','{{ __('payroll.Earnings') }}','earnings'],['9','{{ __('payroll.Deductions') }}','deductions'],['10','{{ __('payroll.Tax') }}','tax'],['11','{{ __('payroll.Paid Date') }}','paid_date'],['12','{{ __('payroll.Net Salary') }}','net_salary']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['payroll_report_list']) }}">
 
        <input type="hidden" name="current_page_url" id="current_page_url" value="{{ url()->full() }}">
    </section>
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:payroll_table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
@endpush
