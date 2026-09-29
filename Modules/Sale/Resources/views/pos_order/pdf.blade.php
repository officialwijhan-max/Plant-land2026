<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('sale::sale.pos_order') }}</title>
    <style>

        h1, h2, h3, h4, h5, h6 {
            margin: 0;
        }

        .invoice_wrapper {
            max-width: 515px;
            margin: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .border_none {
            border: 0px solid transparent;
            border-top: 0px solid transparent !important;
        }

        .invoice_part_iner {
            background-color: #fff;
            padding: 20px;
        }

        .invoice_part_iner h4 {
            font-size: 30px;
            font-weight: 500;
            margin-bottom: 40px;

        }

        .invoice_part_iner h3 {
            font-size: 25px;
            font-weight: 500;
            margin-bottom: 5px;

        }

        .table_border thead {
            background-color: #F6F8FA;
        }

        .table td, .table th {
            padding: 10px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #79838b;
        }

        .table td, .table th {
            padding: 10px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #79838b;
        }

        .table_border tr {
            border-bottom: 1px solid #000 !important;
        }

        /* .table_border tr:last-child{
            border-bottom: 0 solid transparent !important;
        } */
        th p span, td p span {
            color: #212E40;
        }

        .table th {
            color: #00273d;
            font-weight: 300;
            border-bottom: 1px solid #f1f2f3 !important;
            background-color: #fafafa;
        }

        h5 {
            font-size: 14px;
            font-width: 500;
        }

        h6 {
            font-size: 14px;
            font-weight: 300;
        }

        .mt_40 {
            margin-top: 40px;
        }

        .table_header_logo {
            padding: 10px 0 40px !important;
        }

        .table_header_logo td {
            width: 50%;
            padding: 10px 0 40px !important;
            border-top: 0px solid transparent;
        }

        .table_header_logo img {
            margin-top: 12px;
        }

        .table_header_logo .btn_3 {
            text-align: right;
            float: right;
        }

        .invoice_btn .btn_1 {
            width: 100%;
            margin-bottom: 15px;
            text-align: center;
        }

        .invoice_btn .btn_1 i {
            margin-right: 8px;
        }

        .invoice_btn .btn_1:hover {
            color: #fff;
        }

        .invoice_btn .download {
            background-color: #E63E45;
        }

        .invoice_btn .print {
            background-color: #2EC9B8;
            border: 1px solid #2EC9B8;
        }

        .table_style th, .table_style td {
            padding: 20px;
        }

        .invoice_info_table td {
            font-size: 14px;
            padding: 5px;
        }

        .invoice_info_table td h6 {
            text-align: right;
            color: #6D6D6D;
            font-weight: 400;
        }

        p {
            font-size: 14px;
            color: #454545;
        }

        .invoice_info_table2 tbody {

        }

        .invoice_info_table2 tbody th {
            background: transparent;
            padding: 5px;
            text-align: right;
            border-bottom: 1px dotted #000 !important;
        }

        .invoice_info_table2 tbody td {
            padding: 5px;
        }

        .table_border2 thead {
            border-bottom: 1px solid #000 !important;
        }

        .table_border2 thead th {
            background: transparent;
            border-bottom: 1px solid #000 !important;
            font-size: 14px;
        }

        .table_border2 tbody td {
            padding: 5px;
            font-size: 12px;
        }
    </style>

</head>
<body>


<div>
    <div class="invoice_wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- invoice print part here -->
                    <div class="invoice_print">
                        <div class="container">
                            <div id="printablePos" class="invoice_part_iner">
                                <table class="invoice_table invoice_info_table">
                                    <h3 style="text-align: center; color: black">{{Settings('company_name')}}</h3>
                                    <h5 style="text-align: center; color: black">{{Settings('country_name')}}</h5>
                                    <p style="text-align: center;color: black">{{__('retailer.Phone')}}
                                        :{{Settings('phone')}}
                                        , {{trans('common.Email')}}
                                        : {{Settings('email')}}</p>
                                    <tbody>
                                    <tr>
                                        <td><h6>{{$sale->invoice_no}}</h6></td>
                                        <td style="text-align: right;">
                                            <h6>{{__('sale::sale.invoice_no')}}</h6></td>

                                    </tr>
                                    <tr>
                                        <td><h6>{{trans('common.Date')}}</h6></td>
                                        <td style="text-align: right;">
                                            <h6>{{$sale->date}}</h6>
                                        </td>
                                    </tr>
                                    @php
                                        $name = $sale->customer ? $sale->customer->name : $sale->agentuser->name;
                                        $mobile = $sale->customer ? $sale->customer->mobile : $sale->agentuser->username;
                                        $email = $sale->customer ? $sale->customer->email : $sale->agentuser->email;
                                    @endphp
                                    <tr>
                                        <td><h6>{{trans('common.customer_name')}}</h6></td>
                                        <td style="text-align: right;"><h6>{{$name}}</h6></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="table_title">
                                    <h3 style="font-size: 14px; text-transform: uppercase; text-align: center; color: black">{{ __('sale::sale.invoice') }}</h3>
                                </div>
                                <table class="invoice_table pdf_table_1"
                                       style="margin-bottom: 0 !important;color: black">
                                    <thead>
                                    <tr>
                                        <th scope="col">{{__('common.Sl')}}</th>
                                        <th scope="col">{{trans('common.Name')}}</th>
                                        <th scope="col">{{trans('sale.Qty')}}</th>
                                        <th scope="col">{{__('product.Price')}}</th>
                                        <th scope="col">{{trans('common.Amount')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($sale->items as $key => $item)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$item->productable->name}}</td>
                                            <td>{{$item->quantity}}</td>
                                            <td>{{$item->price}}</td>
                                            <td>{{$item->sub_total}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <hr>
                                <table class="invoice_table dashed_table"
                                       style="margin-bottom: 10px;color: black">
                                    <tbody>
                                    <tr>
                                        <th class="w_70">{{trans('common.Total Amount')}}:</th>
                                        <td style="text-align: right;">
                                            <span>{{$sale->payable_amount}}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="w_70">{{trans('common.Tax')}}:</th>
                                        <td style="text-align: right;">
                                            <span>{{$sale->total_tax}}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="w_70">{{trans('common.Discount')}}:</th>
                                        <td style="text-align: right;">
                                            <span>{{$sale->total_discount}}</span></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div style="text-align: center;">
                                    {!! DNS1D::getBarcodeSVG($sale->id, 'C39') !!}
                                </div>
                                <p style="text-align: center;"> {{Settings('terms_conditions')}}
                                    <br>
                                    !{{__('sale::sale.thank_you_for_choosing_us')}}
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- invoice print part end -->
                    <div class="row justify-content-center">
                        <a href="#" onclick="printDiv('printablePos')"
                           class="primary-btn semi_large2 fix-gr-bg">{{trans('common.Print')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-30 justify-content-center">
    <a href="javascript:void(0)" onclick="printDiv('printablePos')"
       class="primary-btn semi_large2 fix-gr-bg">{{trans('common.Print')}}</a>
    <a href="{{route('pos-order.products')}}"
       class="primary-btn semi_large2 fix-gr-bg ml-2 mr-20">{{__('sale::sale.back_to_pos')}}</a>
</div>

<script type="text/javascript">
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>
</body>
</html>
