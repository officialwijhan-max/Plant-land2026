<!DOCTYPE html>
<html>
<head>

    <!-- Required meta tags -->
    <meta charset="utf-8"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>




        <link rel="stylesheet" href="{{asset('public/backEnd/vendors/css/bootstrap.min.css')}}"/>







        <link rel="stylesheet" href="{{asset('public/backEnd/css/style.css')}}"/>
        <link rel="stylesheet" href="{{asset('public/backEnd/css/infix.css')}}"/>

    <link rel="stylesheet" href="{{asset('public/frontend/css/style.css')}}"/>


    <link rel="stylesheet" href="{{asset('public/css/app.css')}}"/>
    <style>
        .invoice_table {
            border-collapse: collapse;
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 0;
        }

        .invoice_wrapper {
            max-width: 435px;
        }

        .invoice_table {
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

        .invoice_table td, .table th {
            padding: 5px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #79838b;
        }

        .invoice_table td, .table th {
            padding: 5px 0;
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

        .invoice_table th {
            color: #00273d;
            font-weight: 300;
            border-bottom: 1px solid #f1f2f3 !important;
            background-color: #fafafa;
        }

        h5 {
            font-size: 16px;
            font-weight: 500;
            line-height: 23px;
        }

        h6 {
            font-size: 10px;
            font-weight: 300;
        }

        .mt_40 {
            margin-top: 40px;
        }

        .table_style th, .table_style td {
            padding: 20px;
        }

        .invoice_info_table td {
            font-size: 10px;
            padding: 0px;
        }

        .invoice_info_table td h6 {
            color: #6D6D6D;
            font-weight: 400;
        }

        p {
            font-size: 10px;
            color: #454545;
            line-height: 16px;
        }

        .invoice_info_table2 tbody {

        }

        .invoice_info_table2 tbody th {
            background: transparent;
            padding: 0px;
            text-align: right;
            border-bottom: 1px dotted #000 !important;
        }

        .invoice_info_table2 tbody td {
            padding: 0px;
        }

        .table_border2 thead {
            border-bottom: 1px solid #000 !important;
        }

        .table_border2 thead th {
            background: transparent;
            border-bottom: 1px solid #000 !important;
            font-size: 10px;
        }

        .table_border2 tbody td {
            padding: 0px;
            font-size: 10px;
        }

        .w_70 {
            width: 70%;
        }

        .pdf_table_1 {

        }

        .pdf_table_1 th {
            font-size: 10px;
            padding: 3px;
            background: transparent;
            border-bottom: 1px solid #000 !important;
            border-top: 1px solid #000 !important;
            text-align: left;
        }

        .pdf_table_2 th {
            font-size: 10px;
            padding: 3px;
            background: transparent;
            border-bottom: 1px solid #000 !important;
            border-top: 1px solid #000 !important;
            text-align: left;
        }

        .pdf_table_2 td {
            padding: 0;
        }

        .pdf_table_2 tfoot {

        }

        .pdf_table_2 tfoot td {
            background: #D2D6DE;
            color: #000 !important;
        }

        .dashed_table {
        }

        .dashed-underline {
            display: block;
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        .dashed_table th {
            background: transparent;
            border-bottom: 0 !important;
            text-align: right;
            padding: 0 !important;
            font-size: 10px;
        }

        .dashed_table td {
            padding: 0 !important;
        }

        .dashed_table td span {
            border-bottom: 1px dotted #000;
            padding: 0;
            display: block;
            margin-left: 5px;
            font-size: 10px;
        }

        .balance_text strong {
            font-style: italic;
        }

        hr {
            margin: 0 !important;
        }

        .invoice_wrapper h3,
        .invoice_wrapper h5,
        .invoice_wrapper h6,
        .invoice_wrapper h4 {
            color: #000000;
        }

        .invoice_wrapper table td,
        .invoice_wrapper table th {
            font-size: 10px !important;
        }


        .invoice_logo {
            width: 30%;
            float: left;
            text-align: left;
        }

        .invoice_no {
            text-align: right;
            color: #415094;
        }

        .invoice_info {
            padding: 20px;
            text-transform: capitalize;
        }

        table.dataTable tbody td {
            text-align: left;
        }

        @page
        {
            /* this affects the margin in the printer settings */
            margin-top: 1in;
        }
        .a4_width {
            max-width: 793.71px;
            min-height: 1122.52px;
            margin: auto;
        }
        .a4_width_modal {
            max-width: 210mm;
        }
        .modal-content .modal-body {
            border-radius: 15px;
        }
        .signature_bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            bottom: 30px;
        }
        .extra-margin {
            height: 70px;
        }
        .QA_section .QA_table tbody th, .QA_section .QA_table tbody td {
            padding: 3px;
        }
        .nowrap{
            white-space: nowrap;
        }
        .hpb-1{
            padding-bottom: 5px;
        }
        body,html{
            width: 100%;
            height: 100%;
        }
    </style>

</head>

<body>


@php
    $subtotal = $sale->items->sum('price') * $sale->items->sum('quantity');
    $total_due = 0;
    $this_due = 0;
    $tax = 0;
    $discountProductTotal = 0;
    $subTotalAmount = 0;
    foreach ($sale->items as $product) {

        $prductDiscount = $product->price * $product->discount / 100;

        $tax +=(($product->price - $prductDiscount) * $product->quantity ) * $product->tax / 100;

        if ($product->discount > 0) {
            $discountProductTotal += $prductDiscount * $product->quantity;
        }
        $subTotalAmount += $product->price * $product->quantity;
    }
    $this_due = $sale->payable_amount - $sale->payments()->where('payment_type','pay')->sum('amount') - $sale->payments()->where('payment_type','return')->sum('amount');
    $discount = $sale->total_discount;
    $vat =(($sale->amount - $discount) * $sale->total_tax) / 100
@endphp
@php
    $paid =0
@endphp

<div id="printablePos" class="invoice_part_iner" style="width: 110mm; margin: 0;">
    <table class="invoice_table invoice_info_table">
        <h3 style="text-align: center; color: black">{{app('general_setting')->company_name}}</h3>
        <h5 style="text-align: center; color: black">{{app('general_setting')->country_name}}</h5>
        <p style="text-align: center;color: black">{{__('retailer.Phone')}}
            :{{app('general_setting')->phone}}
            , {{__('retailer.Email')}}
            : {{app('general_setting')->email}}</p>
        <tbody>
        <tr>
            <td><h6>{{$sale->invoice_no}}</h6></td>
            <td style="text-align: right;">
                <h6>{{__('sale.Invoice No')}}</h6></td>

        </tr>
        <tr>
            <td><h6>{{__('sale.Date')}}</h6></td>
            <td style="text-align: right;">
                <h6>{{$sale->date}}</h6>
            </td>
        </tr>
        @php
            $name = ($sale->customer_id != null) ? $sale->customer->name : $sale->agentuser->name;
            $mobile = ($sale->customer_id != null) ? $sale->customer->mobile : $sale->agentuser->agent->phone;
            $email = ($sale->customer_id != null) ? $sale->customer->email : $sale->agentuser->email
        @endphp
        <tr>
            <td><h6>{{__('sale.Customer')}}</h6></td>
            <td style="text-align: right;"><h6>{{$name}}</h6>
            </td>
        </tr>
        {{--<tr>
            <td>
                <h6>{{__('sale.Customer')}} {{__('retailer.Phone')}}</h6>
            </td>
            <td style="text-align: right;"><h6>{{$mobile}}</h6></td>
        </tr>--}}
        </tbody>
    </table>
    <div class="table_title">
        <h3 style="font-size: 14px; text-transform: uppercase; text-align: center; color: black">{{ __('common.Invoice') }}</h3>
    </div>
    <table class="invoice_table pdf_table_1"
           style="margin-bottom: 0 !important;color: black">
        <thead>
        <tr>
            <th scope="col">{{__('common.No')}}</th>
            <th scope="col">{{__('common.Name')}}</th>
            <th scope="col">{{__('quotation.Qty')}}</th>
            <th scope="col" class="text-right">{{__('sale.Price')}}</th>
            <th scope="col" class="text-right">{{__('sale.Discount')}} (%)</th>
            <th scope="col" class="text-right">{{__('sale.Amount')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($sale->items as $key => $item)
            <tr>
                <td>{{$key+1}}</td>
                @php
                    $product =$item->productable->product ? $item->productable->product->product_name : $item->productable->name
                @endphp
                <td>{{$product}}</td>
                <td>{{$item->quantity}}</td>
                <td class="text-right">{{$item->price}}</td>
                <td class="text-right">{{$item->discount}}</td>
                <td class="text-right">{{ single_price($item->sub_total)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr>
    <table class="invoice_table dashed_table"
           style="margin-bottom: 10px;color: black">
        <tbody>
        <tr>
            <th class="w_70">{{__('sale.SubTotal')}}:</th>
            <td style="text-align: right;">
                <span>{{single_price($subTotalAmount)}}</span></td>
        </tr>
        <tr>
            <th class="w_70">{{__('quotation.Product wise Total Discount')}}:</th>
            <td style="text-align: right;">
                <span>{{single_price($discountProductTotal)}}</span></td>
        </tr>
        <tr>
            <th class="w_70">{{__('sale.Product Tax')}}:</th>
            <td style="text-align: right;">
                <span>{{single_price($tax)}}</span></td>
        </tr>

        <tr>
            <th class="w_70">{{__('sale.Grand Total')}}:</th>
            <td style="text-align: right;">
                <span>{{ single_price($subTotalAmount - $discountProductTotal + $tax) }}</span></td>
        </tr>

        <tr>
            <th class="w_70">{{__('sale.Discount')}}:</th>
            <td style="text-align: right;">
                @if ($sale->total_discount > 0)
                    <span>-{{ single_price($sale->total_discount) }}</span></td>
            @else
                <span>{{ single_price(0)}}</span></td>
            @endif
        </tr>
        <tr>
            <th class="w_70">{{__('quotation.Other Tax')}} ({{ $sale->total_tax }}%):</th>
            <td style="text-align: right;">
                <span>{{ single_price($vat) }}</span></td>
        </tr>
        @if($sale->shipping_charge > 0)
            <tr>
                <th class="w_70">{{__('purchase.Shipping Charge')}}:</th>
                <td style="text-align: right;">
                    <span>{{ single_price($sale->shipping_charge) }}</span></td>
            </tr>
        @endif
        @if($sale->other_charge > 0)
            <tr>
                <th class="w_70">{{__('purchase.Other Charge')}}:</th>
                <td style="text-align: right;">
                    <span>{{ single_price($sale->other_charge) }}</span></td>
            </tr>
        @endif
        <tr>
            <th class="w_70">{{__('sale.Total Amount')}}:</th>
            <td style="text-align: right;">
                <span>{{ single_price($sale->payable_amount) }}</span></td>
        </tr>
        <tr>
            <th class="w_70">{{__('sale.Paid Amount')}}:</th>
            <td style="text-align: right;">
                <span>{{ single_price($sale->payments()->where('payment_type','pay')->sum('amount'))}}</span>
            </td>
        </tr>
        <tr>
            <th class="w_70">{{__('sale.Due')}}:</th>
            <td style="text-align: right;">
                <span>{{ single_price($sale->payable_amount - $sale->payments()->where('payment_type','pay')->sum('amount'))}}</span>
            </td>
        </tr>
        @if ($sale->payable_amount - ($sale->payments()->where('payment_type','pay')->sum('amount') + $sale->payments->sum('advance_amount')) < 0)
            <tr>
                <th class="w_70">{{__('purchase.Advance Amount')}}:</th>
                <td style="text-align: right;">
                    <span>{{ single_price($sale->payments()->where('payment_type','pay')->sum('amount') + $sale->payments->sum('advance_amount') - $sale->payable_amount)}}</span>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
    <div style="text-align: center;">
        {!! DNS1D::getBarcodeSVG($sale->id, 'C39') !!}
    </div>
    <p style="text-align: center;"> {{app('general_setting')->terms_conditions}}</p>
    <p>Remarks:</p>
    <p style="text-align: center;">{{app('general_setting')->remarks_title}}</p>
    <span class="dashed-underline"></span>
    <p style="text-align: center;">{{app('general_setting')->remarks_body}}</p>
</div>

<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>
<script>
    $(document).ready(function () {
        window.print();
        setTimeout(function () {
            window.history.back();
        }, 3000);
    });
</script>
</body>
</html>
