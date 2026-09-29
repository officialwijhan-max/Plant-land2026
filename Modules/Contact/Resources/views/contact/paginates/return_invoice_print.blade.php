<!DOCTYPE html>
<html>
<head>

    <title>{{ __('common.Return') }} Print</title>

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
<div class="container-fluid">
    <div class="invoice_heading">
        <div class="invoice_logo">
            <img src="{{asset(app('general_setting')->logo)}}" style="max-height: 220px; max width: 500px" alt="">
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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'return_invoice_from_customer_detail')->first();
        }else {
            $permissions = null;
        }
    @endphp
    <div class="invoice_info">
        <h4 class="text-center">{{ trans('common.Return').' : '. $customer->contact_id }}</h4>
        <table class="table table-bordered billing_info m-0">
            <thead>
                <tr>
                    <th scope="col">{{__('common.No')}}</th>
                    <th scope="col">{{__('sale.Date')}}</th>
                    <th scope="col">{{__('sale.Invoice')}}</th>
                    <th scope="col">{{__('sale.Reference No')}}</th>
                    <th scope="col">{{__('common.Sold By')}}</th>
                    <th scope="col">{{__('common.Approval')}}</th>
                    <th scope="col">{{__('common.Amount')}}</th>
                </tr>
            </thead>
            @php
                $returnTotal = 0;
            @endphp
            <tbody>
                @foreach ($return_invoices as $key => $return)
                    @php
                        $returnTotal += $return->total_return_amount;
                    @endphp
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ showDate($return->date) }}</td>
                        <td>{{$return->invoice_no}}</td>
                        <td>{{$return->ref_no}}</td>
                        <td>{{@$return->user->name}}</td>
                        <td>
                            @if ($return->status == 0)
                            {{__('sale.Unpaid')}}
                            @elseif ($return->status == 2)
                            {{__('sale.Partial')}}
                            @else
                            {{__('sale.Paid')}}
                            @endif
                        </td>
                        <td>{{ single_price($return->total_return_amount) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td>{{ __('common.Total') }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ single_price($returnTotal) }}</td>
                </tr>
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
