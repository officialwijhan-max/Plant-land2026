@extends('backEnd.master')
@section('mainContent')

<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="box_header common_table_header">
                    <div class="main-title d-md-flex">
                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('payroll.Payroll') }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="white_box_50px box_shadow_white">
                    <form class="" action="{{ route('staff_search_for_payroll') }}" method="GET">
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
                                                <option value="{{ $month }}" {{date('F') == $month ? 'selected' : ''}} >{{ __('common.'.$month) }}</option>
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

                                        @endforeach'
                                    </select>
                                    <span class="text-danger">{{$errors->first('year')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 mb-2 mt-3 text-center">
                                    <button type="submit" class="primary-btn btn-sm fix-gr-bg"id="save_button_parent"><i class="ti-search"></i>{{ __('attendance.Search') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @isset($items)
                <div class="col-lg-12 mt-4">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table payroll">
                            <!-- table-responsive -->
                            <div id="item_list_tbl">
                                @include('payroll::payrolls.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            @endisset
        </div>
    </div>
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'payroll_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{ __('common.ID') }}','id'],['2','{{ __('common.Staff') }}','employee'],['3','{{ __('attendance.Staff ID') }}','staff_id'],['4','{{ __('department.Department') }}','department'],['5','{{ __('role.Role') }}','role'],['6','{{ __('common.Phone') }}','phone'],['7','{{ __('common.Basic Salary') }}','basic_salary'],['8','{{ __('common.Total Loan') }}','total_loan'],['9','{{ __('payroll.Paid Loan Amount') }}','paid_loan_amount'],['10','{{ __('payroll.Due Loan Amount') }}','due_loan_amount'],['11','{{ __('common.Status') }}','status'],['12','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self', ['payroll_list']) }}">
    <input type="hidden" name="current_page_url" id="current_page_url" value="{{ url()->full() }}">
</section>
<div class="form_payment"></div>
<div class="modal fade admin-query" id="SlipForm">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('payroll.View Payslip Details') }}</h4>
                <button type="button" onclick="modal_close()" class="close" data-dismiss="modal">
                    <i class="ti-close"></i>
                </button>
            </div>

            <div class="modal-body">
                <div id="printablePos">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-25">
                        <div class="col-lg-12 text-center">
                            <h3>{{ app('general_setting')->company_name }}</h3>
                            <h6>{{ app('general_setting')->address }}</h6>
                        </div>
                        <div class="col-lg-12 text-center">
                            <h5>{{ __('payroll.Payslip for the period of') }}<span class="period"></span></h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5 payslip">
                            <p>{{ __('payroll.Payslip') }} - <span class="payroll_id"></span></p>
                        </div>
                        <div class="col-md-7 text-right">
                            <p class="payment_date"></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-md-5">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ __('common.Staff ID') }}:</label>
                                <label class="primary_input_label" for="">{{ __('common.Name') }}:</label>
                                <label class="primary_input_label" for="">{{ __('department.Department') }}:</label>
                                <label class="primary_input_label" for="">{{ __('payroll.Payment Method') }}:</label>
                                <label class="primary_input_label" for="">{{ __('payroll.Basic Salary') }}:</label>
                                <label class="primary_input_label" for="">{{ __('payroll.Total Earning') }}:</label>
                                <label class="primary_input_label" for="">{{ __('payroll.Total Deduction') }}:</label>
                                <label class="primary_input_label" for="">{{ __('payroll.Net Salary') }}:</label>
                                <label class="primary_input_label" for="">{{ __('payroll.Gross Salary') }}:</label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label employee_id" for=""></label>
                                <label class="primary_input_label user_name" for=""></label>
                                <label class="primary_input_label department_name" for=""></label>
                                <label class="primary_input_label payment_mode" for=""></label>
                                <label class="primary_input_label basic_salary" for=""></label>
                                <label class="primary_input_label total_earning" for=""></label>
                                <label class="primary_input_label total_deduction" for=""></label>
                                <label class="primary_input_label net_salary" for=""></label>
                                <label class="primary_input_label gross_salary" for=""></label>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <a href="" target="_blank" class="primary-btn fix-gr-bg mr-2 pdf_btn">{{__('payroll.PDF')}}</a>
                    <button type="button" onclick="printDiv('printablePos')" class="primary-btn fix-gr-bg mr-2">{{__('pos.Print')}}</button>
                    <button type="button" onclick="modal_close()" class="primary-btn fix-gr-bg">{{__('common.Close')}}</button>
                </div>
            </div>

        </div>
    </div>
</div>
<input type="hidden" name="app_base_url" id="app_base_url" value="{{ URL::to('/') }}">
@include('backEnd.partials.delete_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:payroll_table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script type="text/javascript">
    function payrollPayment(el, role_id)
    {
        $.post('{{ route('payroll_payment_modal') }}',{_token:'{{ csrf_token() }}', id:el, role_id:role_id}, function(data){
            $(".form_payment").html(data);
            $('#PaymentForm').modal('show');
            $('select').niceSelect();
            $('.date').datepicker({
                autoclose: true,
                setDate: new Date(),
                rtl : RTL,
                language: LANG
            });
        });
    }
    function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
        function modal_close(){
            $('#SlipForm').remove();
            $('.modal-backdrop').remove();
        }

    function viewSlip(payroll)
    {
        $('#SlipForm').modal('show');
        let currency = '{{app('general_setting')->currency_symbol}}';
        $('.period').text(' '+payroll.payroll_month+' - '+payroll.payroll_year);
        $('.payroll_id').text(payroll.id);
        $('.payment_date').text(payroll.payment_date);
        $('.employee_id').text(payroll.staff.employee_id);
        $('.user_name').text(payroll.staff.user.name);
        $('.department_name').text(payroll.staff.department.name);
        if (payroll.payment_mode ==  null) {
            $('.payment_mode').text("Not Payment Yet.");
        }
        else {
            $('.payment_mode').text(payroll.payment_mode);
        }
        $('.basic_salary').text(currency +' '+payroll.basic_salary);
        $('.total_earning').text(currency +' '+payroll.total_earning);
        $('.total_deduction').text(currency +' '+payroll.total_deduction);
        $('.net_salary').text(currency +' '+payroll.net_salary);
        $('.gross_salary').text(currency +' '+payroll.gross_salary);
        var baseUrl = $('#app_base_url').val();
        var url = baseUrl + "/hr/payroll/pdf/" + payroll.id;
        $('.pdf_btn').attr('href', url);
    }
</script>
@endpush
