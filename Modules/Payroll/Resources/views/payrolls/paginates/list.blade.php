

<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
    </x-slot>

    <x-slot name='table_btns'>
        @if(strpos($_SERVER['REQUEST_URI'], '?') == true)
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=print" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=csv" target="_blank" title="Export">
                <i class="ti-export"></i>
            </a>
        @else
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=print" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=csv" target="_blank" title="Export">
                <i class="ti-export"></i>
            </a>
        @endif
        <a href="#" title="Col Show/Hide" class="hide_show_click_btn">
            <i class="ti-layout-column3"></i>
        </a>
    </x-slot>

    <x-slot name="table">

        <x-table.head>
            <tr>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.ID') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="name" href="#">
                        @if (request('sort') == 'asc' && request('col') == "name")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "name")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Staff') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('attendance.Staff ID') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id= "id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('department.Department') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id= "id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('role.Role') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id= "id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Phone') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id= "id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Basic Salary') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id= "id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Total Loan') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id= "id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('payroll.Paid Loan Amount') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id= "id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('payroll.Due Loan Amount') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id= "id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Status') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title" data-id="" href="#"> <i
                            class="ti-arrow-down"></i>{{ __('common.Action') }}</a> </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key => $user)
                <x-table.tr>
                    @php
                        if ($user->staff) {
                            $getPayrollDetails = \Modules\Payroll\Entities\Payroll::with(['staff:id,user_id,department_id,employee_id,basic_salary,phone','staff.user:id,name','staff.department:id,name'])->where('staff_id', $user->staff->id)->where('payroll_month', $m)->where('payroll_year', $y)->first();
                        }
                    @endphp
                    <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                    <x-table.td><a href="#">{{ $user->name }}</a></x-table.td>
                    <x-table.td>{{ @$user->staff->employee_id }}</x-table.td>
                    <x-table.td>{{ @$user->staff->department->name }}</x-table.td>
                    <x-table.td>{{ @$user->role->name }}</x-table.td>
                    <x-table.td>{{ @$user->staff->phone }}</x-table.td>
                    <x-table.td>{{ single_price(@$user->staff->basic_salary) }}</x-table.td>
                    <x-table.td>{{ single_price($user->LoanInfo['total_loan']) }}</x-table.td>
                    <x-table.td>{{ single_price($user->LoanInfo['total_paid']) }}</x-table.td>
                    <x-table.td>{{ single_price($user->LoanInfo['total_due']) }}</x-table.td>
                    <x-table.td>
                        @if(!empty($getPayrollDetails))
                            @if($getPayrollDetails->payroll_status == 'G')
                            <button class="primary-btn small bg-warning text-white border-0"> {{ __('payroll.Generated') }}</button>
                            @endif

                            @if($getPayrollDetails->payroll_status == 'P')
                            <button class="primary-btn small bg-success text-white border-0"> {{ __('payroll.Paid') }}</button>
                            @endif
                        @else
                            <button class="primary-btn small bg-danger text-white border-0">{{ __('payroll.Not generated') }}</button>
                        @endif
                    </x-table.td>
                    <x-table.td>
                        @if(!empty($getPayrollDetails))
                            @if($getPayrollDetails->payroll_status == 'G')
                                <a title="Proceed to pay" onclick="payrollPayment({{ $getPayrollDetails->id }},{{ $user->role_id }})"><button class="primary-btn small tr-bg ">{{ __('payroll.Proceed To Pay') }}</button></a>
                                <a onclick="viewSlip({{ $getPayrollDetails }})"><button class="primary-btn small tr-bg ml-2">{{ __('payroll.View PaySlip') }}</button></a>
                            @endif
                            @if($getPayrollDetails->payroll_status == 'P')
                                <a onclick="viewSlip({{ $getPayrollDetails }})"><button class="primary-btn small tr-bg">{{ __('payroll.View PaySlip') }}</button></a>
                            @endif
                        @else
                            <a class="" href="{{ url('hr/payroll/generate-Payroll/'.$user->id.'/'.$m.'/'.$y)}}"><button class="primary-btn small tr-bg"> {{ __('payroll.Generate Payroll') }}</button></a>
                        @endif
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
