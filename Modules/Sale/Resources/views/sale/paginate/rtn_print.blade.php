<!DOCTYPE html>
<html>
<head>

    <title>{{__('sale.Sale Return List')}} Print</title>

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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'return_sale_list')->first();
        }else {
            $permissions = null;
        }
    @endphp
    <div class="invoice_info">
        <h4 class="text-center">{{__('sale.Sale Return List')}}</h4>
        <table class="table table-bordered billing_info m-0">
            <thead>
                @if ($permissions)
                    <tr>
                        @if (str_contains($permissions->export_column, 'id'))
                        <th scope="col">{{__('common.No')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'invoice'))
                        <th scope="col">{{__('sale.Invoice')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'branch'))
                        <th scope="col">{{__('sale.Branch')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'biller'))
                        <th scope="col">{{__('sale.Biller')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'customer'))
                        <th scope="col">{{__('sale.Customer')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'qty'))
                        <th scope="col">{{__('sale.Quantity')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'total_amount'))
                        <th scope="col">{{__('common.Total Amount')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'rtn_amount'))
                        <th scope="col">{{__('sale.Return Amount')}}</th>
                        @endif
                        @if (str_contains($permissions->export_column, 'status'))
                            <th scope="col">{{__('common.Status')}}</th>
                        @endif
                    </tr>
                @else
                    <tr>
                        <th scope="col">{{__('common.No')}}</th>
                        <th scope="col">{{__('sale.Invoice')}}</th>
                        <th scope="col">{{__('sale.Branch')}}</th>
                        <th scope="col">{{__('sale.Biller')}}</th>
                        <th scope="col">{{__('sale.Customer')}}</th>
                        <th scope="col">{{__('sale.Quantity')}}</th>
                        <th scope="col">{{__('common.Total Amount')}}</th>
                        <th scope="col">{{__('sale.Return Amount')}}</th>
                        <th scope="col">{{__('common.Status')}}</th>
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
                        @if (str_contains($permissions->export_column, 'invoice'))
                        <td>{{ @$item->invoice_no }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'branch'))
                        <td>{{ @$item->saleable->name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'biller'))
                        <td>{{ @$item->user->name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'customer_name'))
                        <td>{{ @$item->customer->name }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'qty'))
                        <td>{{ @$item->items->sum('return_quantity') }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'total_amount'))
                            <td>{{ single_price($item->payable_amount) }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'rtn_amount'))
                            <td>{{ single_price(@$item->items->sum('return_amount')) }}</td>
                        @endif
                        @if (str_contains($permissions->export_column, 'status'))
                            <td>
                                @if (@$item->return_status == 0)
                                    {{__('sale.Pending')}}
                                @else
                                    {{__('sale.Approved')}}
                                @endif
                            </td>
                        @endif
                    </tr>
                    @else
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{ @$item->invoice_no }}</td>
                        <td>{{ @$item->saleable->name }}</td>
                        <td>{{ @$item->user->name }}</td>
                        <td>{{ @$item->customer->name }}</td>
                        <td>{{ @$item->items->sum('return_quantity') }}</td>
                        <td>{{ single_price($item->payable_amount) }}</td>
                        <td>
                            @php
                                $total_return_amount = 0;
                                $total_return_value = @$item->items->sum('return_amount');
                                $return_discount = ($item->total_discount / $item->amount) * $total_return_value;
                                if (app('general_setting')->is_tax_return == 'yes') {
                                    $return_tax = (($total_return_value-$return_discount)/100 * $item->total_tax);
                                    $total_return_amount = ($total_return_value + $return_tax) - $return_discount;
                                } else {
                                    $total_return_amount = $total_return_value - $return_discount;
                                }
                            @endphp
                            {{ single_price($total_return_amount) }}
                        </td>
                        <td>
                            @if (@$item->return_status == 0)
                                {{__('sale.Pending')}}
                            @else
                                {{__('sale.Approved')}}
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
