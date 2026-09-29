<!DOCTYPE html>
<html>
<head>

    <title>{{ __('common.Loan Apply') }} Print</title>

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
            font-size: 13px;
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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'loan_list')->first();
        }else {
            $permissions = null;
        }
    @endphp
    <div class="invoice_info">
        <h4 class="text-center">{{ __('common.Loan Apply') }}</h4>
        <table class="table table-bordered billing_info m-0">
            <thead>
                @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'id'))
                            <th scope="col">{{__('common.ID')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'date'))
                            <th scope="col">{{__('common.Date')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'user'))
                            <th scope="col">{{__('common.User')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'department'))
                            <th scope="col">{{__('department.Department')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'type'))
                            <th scope="col">{{__('common.Type')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'amount'))
                            <th scope="col">{{__('common.Amount')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'monthly_installment'))
                            <th scope="col">{{__('common.Monthly Installment')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'due'))
                            <th scope="col">{{__('common.Due')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'status'))
                            <th scope="col">{{__('common.Status')}}</th>
                        @endif
                    </tr>
                @else
                    <tr>
                        <th scope="col">{{ __('common.ID') }}</th>
                        <th scope="col">{{ __('common.Date') }}</th>
                        <th scope="col">{{__('common.User')}}</th>
                        <th scope="col">{{__('department.Department')}}</th>
                        <th scope="col">{{__('common.Type')}}</th>
                        <th scope="col">{{__('common.Amount')}}</th>
                        <th scope="col">{{ __('common.Monthly Installment') }}</th>
                        <th scope="col">{{ __('common.Due') }}</th>
                        <th scope="col">{{ __('common.Status') }}</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @foreach ($items as $key => $item)
                    @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'id'))
                            <td>{{ $key+1 }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'date'))
                            <td>{{ showDate($item->loan_date) }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'user'))
                            <td>{{ $item->user->name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'department'))
                            <td>{{ $item->department->name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'type'))
                            <td>{{ $item->loan_type }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'amount'))
                            <td>{{ single_price($item->amount) }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'monthly_installment'))
                            <td>{{ single_price($item->paid_loan_amount) }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'due'))
                            <td>{{ single_price($item->amount - $item->paid_loan_amount) }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'status'))
                            <td>
                                @if ($item->approval == 0)
                                    {{__('common.Pending')}}
                                @elseif ($item->approval == 1)
                                    {{__('common.Approved')}}
                                @else
                                    {{__('common.Cancelled')}}
                                @endif
                            </td>
                        @endif
                    </tr>
                    @else
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ showDate($item->loan_date) }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>{{ $item->department->name }}</td>
                            <td>{{ $item->loan_type }}</td>
                            <td>{{ single_price($item->amount) }}</td>
                            <td>{{ single_price($item->paid_loan_amount) }}</td>
                            <td>{{ single_price($item->amount - $item->paid_loan_amount) }}</td>
                            <td>
                                @if ($item->approval == 0)
                                    {{__('common.Pending')}}
                                @elseif ($item->approval == 1)
                                    {{__('common.Approved')}}
                                @else
                                    {{__('common.Cancelled')}}
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
