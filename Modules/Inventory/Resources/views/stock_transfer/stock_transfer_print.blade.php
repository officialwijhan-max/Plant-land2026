<!DOCTYPE html>
<html>
<head>

    <title>Stock Transfer</title>

    <!-- Required meta tags -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/css/rtl/bootstrap.min.css"/>

    <style>
        @font-face {
            font-family: 'Cerebri Sans',
            url('storage/fonts/Poppins-Regular.ttf');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cerebri Sans',
            url('storage/fonts/Poppins-Medium.ttf');
            font-weight: 500;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cerebri Sans',
            url('storage/fonts/Poppins-SemiBold.ttf');
            font-weight: 600;
            font-style: normal;
        }
        .invoice_heading {
            border-bottom: 1px solid black;
            padding: 20px;
            text-transform: capitalize;
        }
        body{
            font-family: "Poppins", sans-serif;
        }
        .invoice_logo {
            width: 33.33%;
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
        }
        .t-100{
            min-height: 100px;
        }
        .text-right {
            text-align: right !important;
        }
        .billing_info {
            margin-top: 0px;
        }
        span.td_cln {
            margin-left: 12px;
            margin-right: 12px;
        }

        table {
            text-align: left;
            font-family: "Poppins", sans-serif;
        }

        td, th {
            color: #000000;
            font-size: 12px;
            padding: 0;
            font-weight: 400;
            font-family: "Poppins", sans-serif;
        }

        th {
            font-weight: 600;
            font-family: "Poppins", sans-serif;
        }

        .table-bordered td, .table-bordered th {
            border: 1px solid #000000 !important;
        }

        li {
            list-style-type: none;
            text-align: right;
        }

        .sale_note {
            width: 45%;
            float: left;
            text-align: left;
        }

        .notes {
            color: #415094;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .note_details {
            font-size: 12px;
            font-weight: 600;
            color: #000000 !important;
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
            color: #000000;
            line-height: 16px;
        }
        .extra_div {
            height:40px;
        }
        .a4_width {
           max-width: 1145.28px;
           margin: auto;
        }
        .nowrap{
            white-space: nowrap;
        }

        h5 {
            font-size: 13px !important;
            font-weight: 500;
            line-height: 12px;
        }
        .hpb-1{
            padding: 0;
        }
        .width_custom{
            max-width: 200px;
        }
    </style>
</head>
<body>
@php
    $setting = app('general_setting');
    $totalAmount = 0;
@endphp
<div class="container-fluid ">
    <div class="invoice_heading">
        <div class="invoice_logo">
            <img src="{{asset($setting->logo)}}" width="80%" alt="">
        </div>
        <div class="invoice_no">
            <h5 class="hpb-1">{{$setting->company_name}}</h5>
            <h5 class="hpb-1">{{$setting->phone}}</h5>
            <h5 class="hpb-1">{{$setting->email}}</h5>
            <h5>{{$setting->address}}</h5>
        </div>
    </div>
    <div class="invoice_info">
        <div class="invoice_logo" style="width:75%">
            <table class="table-borderless">
                <tr>
                    <td>{{__('common.Sent From')}}</td>
                    <td><span class="td_cln">:</span> {{$data->sendable->name}}</td>
                </tr>
                <tr>
                    <td>{{__('common.Sent Date')}}</td>
                    <td><span class="td_cln">:</span> {{showDate($data->date)}}</td>
                </tr>
                <tr>
                    <td>{{__('common.Sent By')}}</td>
                    <td><span class="td_cln">:</span> {{@$data->sent_by->name}}</td>
                </tr>
            </table>
        </div>
        <div class="invoice_logo" style="width:25%">
            <table class="table-borderless mr_0 ml_auto">
                <tr>
                    <td>{{__('common.Recieved At')}}</td>
                    <td><span class="td_cln">:</span> {{$data->receivable->name}}</td>
                </tr>
                <tr>
                    <td>{{__('common.Recieved Date')}}</td>
                    <td><span class="td_cln">:</span> {{ ($data->received_at != null) ? showDate($data->received_at) : ''}}</td>
                </tr>
                <tr>
                    <td>{{__('common.Recieved By')}}</td>
                    <td><span class="td_cln">:</span> {{@$data->recieved_by->name}}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="extra_div">

    </div>
    <div class="invoice_info">
        <table class="table table-bordered billing_info">
            <thead>
                <tr>
                    <th class="text-center">{{__('sale.Product')}}</th>
                    <th class="text-center">{{__('product.SKU')}}</th>
                    <th class="text-center">{{__('product.Brand')}}</th>
                    <th class="text-center">{{__('product.Model')}}</th>
                    <th class="text-center">{{__('product.Price')}}</th>
                    <th class="text-center">{{__('product.QTY')}}</th>
                    <th class="text-right">{{__('sale.Total Amount')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->items as $item)
                    @php
                        $v_name = [];
                        $v_value = [];
                        $variantName = null;
                        if ($item->productSku->product_variation) {
                            foreach (json_decode($item->productSku->product_variation->variant_id) as $key => $value) {
                                array_push($v_name , \Modules\Product\Entities\Variant::find($value)->name);
                            }
                            foreach (json_decode($item->productSku->product_variation->variant_value_id) as $key => $value) {
                                array_push($v_value , \Modules\Product\Entities\VariantValues::find($value)->value);
                            }

                            for ($i=0; $i < count($v_name); $i++) {
                                $variantName .= $v_name[$i] . ' : ' . $v_value[$i];
                            }
                        }
                        $subtotal = $item->price * $item->quantity;
                        $totalAmount += $subtotal;
                    @endphp
                    <tr>
                        <td>{{@$item->productSku->product->product_name}}
                            <br>
                            @if ($variantName)
                                ({{ $variantName }})
                            @endif
                        </td>
                        <td>{{$item->productable->sku}}</td>
                        <td>{{@$item->productSku->product->brand->name}}</td>
                        <td>{{@$item->productSku->product->model->name}}</td>
                        <td>{{@$item->price}}</td>
                        <td class="text-center">{{@$item->quantity}}</td>
                        <td class="text-right">{{single_price($subtotal)}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right">{{__('common.Total Qty')}}</td>
                    <td  class="text-right"><span class="total_quantity">{{number_format($data->items->sum('quantity'),2)}}</span></td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right">{{__('sale.SubTotal')}}</td>
                    <td class="text-right"><span class="total_amount">{{single_price($totalAmount)}}</span></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="invoice_info margin_12 custom_margin"  style="display: flex;justify-content: space-between; width:100%;" >
        <div class="sale_note" style="">
            <div class="sale_note_inner text-justify" >
                @if ($data->notes)
                    <h3 class="notes">{{__('common.Note')}}</h3>
                    <div class="note_details">{!! $data->notes !!}</div>
                @endif
            </div>
        </div>
        <div class="sale_note" @if ($data->notes)style="display: flex;justify-content: flex-end; padding-left: 60px" @else style="display: flex;justify-content: flex-end;" @endif>
            <div class="sale_note_inner text-justify">
                @if ($setting->terms_conditions)
                    <h3 class="notes">{{__('setting.Terms & Condition')}}</h3>
                    <div class="note_details">{{$setting->terms_conditions}}</div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="extra_div">

</div>
<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
window.print();
});
</script>
</body>
</html>
