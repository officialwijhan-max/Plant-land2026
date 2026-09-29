<!DOCTYPE html>
<html>
<head>

    <title>Report Print</title>

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
            if (isset($type)) {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'supplier_list')->first();
            }else {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'customer_list')->first();
            }
        }else {
            $permissions = null;
        }
    @endphp
    <div class="invoice_info">
        <h4 class="text-center">{{ (!isset($type)) ? trans('common.Customers') : trans('common.Suppliers') }}</h4>
        <table class="table table-bordered billing_info m-0">
            <thead>
                @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'id'))
                            <th scope="col">{{__('common.sl')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'contact_id'))
                            <th scope="col">{{__('common.Contact ID')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'is_active'))
                            <th scope="col">{{__('setting.Active')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'name'))
                            <th scope="col">{{__('common.Customer Name')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'email'))
                            <th scope="col">{{__('common.Email')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'mobile'))
                            <th scope="col">{{__('common.Phone')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'pay_term_condition'))
                            <th scope="col">{{__('common.Pay Term')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'tax_number'))
                            <th scope="col">{{trans('common.Tax Number')}}</th>
                        @endif
                    </tr>
                @else
                    <tr>
                        <th scope="col">{{__('common.sl')}}</th>
                        <th scope="col">{{__('common.Contact ID')}}</th>
                        <th scope="col">{{__('setting.Active')}}</th>
                        <th scope="col">{{__('common.Customer Name')}}</th>
                        <th scope="col">{{__('common.Email')}}</th>
                        <th scope="col">{{__('common.Phone')}}</th>
                        <th scope="col">{{__('common.Pay Term')}}</th>
                        <th scope="col">{{__('common.Tax Number')}}</th>
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
                        @if (str_contains($permissions->export_column, 'contact_id'))
                            <td>{{ $item->contact_id }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'is_active'))
                            <td>{{ $item->is_active }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'name'))
                            <td>{{ $item->name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'email'))
                            <td>{{ $item->email }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'mobile'))
                            <td>{{ $item->mobile }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'pay_term_condition'))
                            <td>{{ $item->pay_term ? $item->pay_term : '' }} {{ $item->pay_term_condition ? $item->pay_term_condition : '' }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'tax_number'))
                            <td>{{ $item->tax_number }}</td>
                        @endif
                    </tr>
                    @else
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $item->contact_id }}</td>
                            <td>{{ $item->is_active }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->mobile }}</td>
                            <td>{{ $item->pay_term ? $item->pay_term : '' }} {{ $item->pay_term_condition ? $item->pay_term_condition : '' }}</td>
                            <td>{{ $item->tax_number }}</td>
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
