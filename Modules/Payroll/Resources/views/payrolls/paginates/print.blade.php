<!DOCTYPE html>
<html>
<head>

    <title>{{ __('payroll.Payroll') }} Print</title>

    <!-- Required meta tags -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/css/rtl/bootstrap.min.css"/>

    <style>
        .invoice_heading {
            border-bottom: 1px solid black;
            padding: 20px;
            text-transform: capitalize;
        }
        body{
            font-family: "Poppins", sans-serif;
        }
        .invoice_logo {
            width: 50%;
            float: left;
            text-align: left;
        }

        .invoice_no {
            text-align: right;
            color: #415094;
        }

        .invoice_info {
            padding: 20px;
            width: 100%;
            text-transform: capitalize;
            min-height: 100px;
        }
        table {
            text-align: left;
            font-family: "Poppins", sans-serif;
        }

        td, th {
            color: #828bb2;
            font-size: 10px;
            font-weight: 400;
            font-family: "Poppins", sans-serif;
        }

        th {
            font-weight: 600;
            font-family: "Poppins", sans-serif;
        }
        .margin_120{
            margin-top: 120px;
            font-size: 12px;
        }.margin_12{
            margin-bottom: 120px;
            font-size: 12px;
        }
        .invoice_footer{
            position: absolute;
            left: 0;
            bottom: 180px;
            width: 100%;
        }

        .invoice_info_footer {
            padding: 0px;
            width: 100%;
            left: 0;
            text-transform: capitalize;
            position: inherit;
        }

        p {
            font-size: 10px;
            color: #454545;
            line-height: 16px;
        }
        .extra_div {
            height:100;
        }
        .a4_width {
           max-width: 210mm;
           margin: auto;
        }
        h5 {
            font-size: 13px !important;
            font-weight: 500;
            line-height: 12px;
        }
        @media print{@page {size: landscape}}
    </style>
</head>
<body>
<div class="container-fluid ">
    <div class="invoice_heading">
        <div class="invoice_logo">
            <img src="{{asset(app('general_setting')->logo)}}" style="max-height: 110px; max width: 500px" alt="">
        </div>
        <div class="invoice_no">
            <h5 class="hpb-1">{{app('general_setting')->company_name}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->phone}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->email}}</h5>
            <h5>{{app('general_setting')->address}}</h5>
            <h5>{{trans("common.Print")}} : {{date('m-d-Y')}}</h5>
        </div>
    </div>
    @php
        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'payroll_list')->first();
        }else {
            $permissions = null;
        }
        $max_col = 0;
    @endphp
    <div class="invoice_info">
        <h4 class="text-center">{{ __('payroll.Payroll') }}</h4>
        <table class="table table-bordered billing_info m-0">
            <thead>
                @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'id'))
                        <th scope="col">{{ __('common.ID') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'employee'))
                        <th scope="col">{{ __('common.Staff') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'staff_id'))
                        <th scope="col">{{ __('attendance.Staff ID') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'department'))
                        <th scope="col">{{ __('department.Department') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'role'))
                        <th scope="col">{{ __('role.Role') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'phone'))
                        <th scope="col">{{ __('common.Phone') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'basic_salary'))
                        <th scope="col">{{ __('common.Basic Salary') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'total_loan'))
                        <th scope="col">{{ __('common.Total Loan') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'paid_loan_amount'))
                        <th scope="col">{{ __('payroll.Paid Loan Amount') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'due_loan_amount'))
                        <th scope="col">{{ __('payroll.Due Loan Amount') }}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'status'))
                        <th scope="col">{{ __('common.Status') }}</th>
                        @endif
                    </tr>
                @else
                    <tr>
                        <th scope="col">{{ __('common.ID') }}</th>
                        <th scope="col">{{ __('common.Staff') }}</th>
                        <th scope="col">{{ __('attendance.Staff ID') }}</th>
                        <th scope="col">{{ __('department.Department') }}</th>
                        <th scope="col">{{ __('role.Role') }}</th>
                        <th scope="col">{{ __('common.Phone') }}</th>
                        <th scope="col">{{ __('common.Basic Salary') }}</th>
                        <th scope="col">{{ __('common.Total Loan') }}</th>
                        <th scope="col">{{ __('payroll.Paid Loan Amount') }}</th>
                        <th scope="col">{{ __('payroll.Due Loan Amount') }}</th>
                        <th scope="col">{{ __('common.Status') }}</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @foreach ($items as $key => $user)
                    @php
                        if ($user->staff) {
                            $getPayrollDetails = $user->staff->payrolls->where('staff_id', $user->staff->id)->where('payroll_month', $m)->where('payroll_year', $y)->first();
                        }
                    @endphp
                    @if ($permissions)
                        <tr>
                            @if (str_contains($permissions->export_column, 'id'))
                                <td>{{ $key+1 }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'employee'))
                            <td>{{ $user->name }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'staff_id'))
                            <td>{{ @$user->staff->employee_id }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'department'))
                            <td>{{ @$user->staff->department->name }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'role'))
                            <td>{{ @$user->role->name }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'phone'))
                            <td>{{ @$user->staff->phone }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'basic_salary'))
                            <td>{{ single_price(@$user->staff->basic_salary) }}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'total_loan'))
                            <td>{{single_price($user->LoanInfo['total_loan'])}}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'paid_loan_amount'))
                            <td>{{single_price($user->LoanInfo['total_paid'])}}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'due_loan_amount'))
                            <td>{{single_price($user->LoanInfo['total_due'])}}</td>
                            @endif
                            @if (str_contains($permissions->export_column, 'status'))
                                <td>
                                    @if(!empty($getPayrollDetails))
                                        @if($getPayrollDetails->payroll_status == 'G')
                                        {{ __('payroll.Generated') }}
                                        @endif
    
                                        @if($getPayrollDetails->payroll_status == 'P')
                                        {{ __('payroll.Paid') }}
                                        @endif
                                    @else
                                        {{ __('payroll.Not generated') }}
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @else
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ @$user->staff->employee_id }}</td>
                            <td>{{ @$user->staff->department->name }}</td>
                            <td>{{ @$user->role->name }}</td>
                            <td>{{ @$user->staff->phone }}</td>
                            <td>{{ single_price(@$user->staff->basic_salary) }}</td>
                            <td>{{single_price($user->LoanInfo['total_loan'])}}</td>
                            <td>{{single_price($user->LoanInfo['total_paid'])}}</td>
                            <td>{{single_price($user->LoanInfo['total_due'])}}</td>
                            <td>
                                @if(!empty($getPayrollDetails))
                                    @if($getPayrollDetails->payroll_status == 'G')
                                    {{ __('payroll.Generated') }}
                                    @endif

                                    @if($getPayrollDetails->payroll_status == 'P')
                                    {{ __('payroll.Paid') }}
                                    @endif
                                @else
                                    {{ __('payroll.Not generated') }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>

<script type="text/javascript">
    $( document ).ready(function() {
        window.print();
    });
</script>
</body>
</html>
