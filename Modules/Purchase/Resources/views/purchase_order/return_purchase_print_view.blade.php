<!DOCTYPE html>
<html>

<head>

    <title>{{ $data->invoice_no }}</title>

    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/rtl/bootstrap.min.css" />

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

        body {
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

        .t-100 {
            min-height: 100px;
        }

        .billing_info {
            margin-top: 115px;
        }

        table {
            text-align: left;
            font-family: "Poppins", sans-serif;
        }

        td,
        th {
            color: #828bb2;
            font-size: 13px;
            font-weight: 400;
            font-family: "Poppins", sans-serif;
        }

        th {
            font-weight: 600;
            font-family: "Poppins", sans-serif;
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
            color: #828BB2 !important;
        }

        .margin_120 {
            margin-top: 120px;
            font-size: 12px;
        }

        .margin_12 {
            margin-bottom: 120px;
            font-size: 12px;
        }

        .invoice_footer {
            /* position: absolute;
            left: 0;
            bottom: 180px; */
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

        /* .extra_div {
            height:500px;
        } */
        .a4_width {
            max-width: 210mm;
            margin: auto;
        }

        .nowrap {
            white-space: nowrap;
        }

        .hpb-1 {
            padding-bottom: 5px;
        }

        .dashed-underline {
            display: block;
            border-bottom: 1px dashed #000;
            margin: 5px 0;
            width: 100%;
        }

        html {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .invoice_info {
            padding: 20px;
            width: 100%;
            flex: 1 0 auto;
            text-transform: capitalize;
        }

        .flex_auto_div {
            padding: 20px;
            display: flex;
            width: 100%;
            flex: 1 0 auto;
            text-transform: capitalize;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;

        }
    </style>
</head>

<body>
    @php
        $setting = app('general_setting');
    @endphp
    <div class="container-fluid ">
        <div class="invoice_heading">
            <div class="invoice_logo">
                @if ($setting->logo)
                    <img src="{{ asset($setting->logo) }}" width="100px" alt="">
                @else
                    <img src="{{ asset('public/frontend/') }}/img/logo.png" width="100px" alt="">
                @endif
            </div>
            <div class="invoice_no">
                <h5 class="hpb-1">{{ $setting->company_name }}</h5>
                <h5 class="hpb-1">{{ $setting->phone }}</h5>
                <h5 class="hpb-1">{{ $setting->email }}</h5>
                <h5>{{ $setting->address }}</h5>
            </div>
        </div>
        <div class="invoice_info">
            <div class="invoice_logo" style="width:75%">
                <table class="table-borderless">
                    @php
                        $name = $data->customer_id != null ? $data->supplier->name : null;
                        $mobile = $data->supplier_id != null ? $data->supplier->mobile : null;
                        $email = $data->supplier_id != null ? $data->supplier->email : null;
                        $address = $data->supplier_id != null ? $data->supplier->address : null;
                        $total_return_amount = 0;
                    @endphp
                    <tr>
                        <td>{{ __('common.Bill No') }}</td>
                        <td> <span class="p-1">:</span> {{ $data->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Bill Date') }}</td>
                        <td><span class="p-1">:</span> {{ showDate($data->created_at) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Party Name') }}</td>
                        <td><span class="p-1">:</span> {{ @$name }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Party Address') }}</td>
                        <td><span class="p-1">:</span> {{ @$address }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Phone') }}</td>
                        <td><span class="p-1">:</span> {{ @$mobile }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Email') }}</td>
                        <td><span class="p-1">:</span> {{ @$email }}</td>
                    </tr>
                </table>
            </div>
            <div class="invoice_logo" style="width:25%">
                <table class="table-borderless mr_0 ml_auto">
                    <tr>
                        <td>{{ __('common.Served By') }}</td>
                        <td><span class="p-1">:</span> {{ $data->user->name }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Entry Time') }}</td>
                        <td><span class="p-1">:</span> {{ date('m-d-Y H:i:s', strtotime($data->created_at)) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('sale.Ref. No') }}</td>
                        <td><span class="p-1">:</span> {{ $data->ref_no }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Status') }}</td>
                        <td><span class="p-1">:</span>

                            @if ($data->is_paid == 2)
                                {{ trans('sale.Paid') }}
                            @elseif ($data->is_paid == 1)
                                {{ trans('sale.Partial') }}
                            @else
                                {{ trans('sale.Unpaid') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('sale.Branch') }}</td>
                        <td><span class="p-1">:</span> {{ @$data->purchasable->name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="invoice_info">
            <table class="table table-bordered billing_info">
                <tr>
                    <th class="p-2" width="15%">{{ __('sale.Product') }}</th>
                    <th class="p-2">{{ __('sale.Category') }}</th>
                    <th class="p-2">{{ __('sale.Unit Price') }} </th>
                    <th class="p-2">{{ __('sale.Quantity') }}</th>
                    <th class="p-2">{{ __('sale.Tax') }} (%)</th>
                    <th class="p-2">{{ __('sale.Discount') }} </th>
                    <th class="p-2">{{ __('sale.SubTotal') }} </th>
                    <th class="p-2">{{ __('sale.Returned Quantity') }}</th>
                    <th class="p-2">{{ __('sale.Return Amount') }} </th>
                </tr>
                @php
                    $total_return_value = 0;
                    $total_return_amount = 0;
                @endphp
                @foreach ($data->items as $item)
                    @php
                        $v_name = [];
                        $v_value = [];
                        $variantName = null;
                        if ($item->productSku->product_variation) {
                            foreach (json_decode($item->productSku->product_variation->variant_id) as $key => $value) {
                                array_push($v_name, Modules\Product\Entities\Variant::find($value)->name);
                            }
                            foreach (
                                json_decode($item->productSku->product_variation->variant_value_id)
                                as $key => $value
                            ) {
                                array_push($v_value, Modules\Product\Entities\VariantValues::find($value)->value);
                            }

                            for ($i = 0; $i < count($v_name); $i++) {
                                $variantName .= $v_name[$i] . ' : ' . $v_value[$i] . ' ; ';
                            }
                        }
                        $total_return_value += $item->return_amount;
                    @endphp
                    <tr>
                        <td class="p-2">
                            @if ($item->productable_type == 'Modules\Product\Entities\ComboProduct')
                                {{ $item->productable->name }}
                            @else
                                {{ @$item->productSku->product->product_name }}
                                <br>
                                {{ $variantName }}
                            @endif
                        </td>
                        <td class="p-2">
                            {{ @$item->productSku->product->category->name }}
                        </td>
                        <td class="p-2" style="text-align: right">$ <span class="">{{ $item->price }}</span>
                        </td>
                        <td class="p-2" style="text-align: right">{{ $item->quantity }}</td>
                        <td class="p-2" style="text-align: right">{{ $item->tax }}</td>
                        <td class="p-2" style="text-align: right">{{ $item->discount }}</td>
                        <td class="p-2" style="text-align: right">{{ single_price($item->sub_total) }}</td>
                        <td class="p-2" style="text-align: right">{{ $item->return_quantity }}</td>
                        <td class="p-2" style="text-align: right">{{ single_price($item->return_amount) }}</td>
                    </tr>
                @endforeach
                @php
                    $return_discount = ($data->total_discount / $data->amount) * $total_return_value;
                    if (app('general_setting')->is_tax_return == 'yes') {
                        $return_tax = (($total_return_value / 100) * $data->total_vat);
                        $total_return_amount = ($total_return_value + $return_tax) - $return_discount;
                    } else {
                        $total_return_amount = $total_return_value - $return_discount;
                    }
                @endphp

                <tfoot>
                    <tr>
                        <th colspan="8" style="text-align: right">{{__('sale.Return Value')}}:</th>
                        <th style="text-align: right">{{ single_price($total_return_value) }}</th>
                    </tr>
                    @if (app('general_setting')->is_tax_return == 'yes')
                        <tr>
                            <th colspan="8" style="text-align: right">{{__('sale.Return Order Tax')}}:</th>
                            <th style="text-align: right">{{ single_price($return_tax) }}</th>
                        </tr>
                    @endif
                    <tr>
                        <th colspan="8" style="text-align: right">(-){{__('sale.Order Discount')}}:</th>
                        <th style="text-align: right">{{ single_price($return_discount) }}</th>
                    </tr>
                    <tr>
                        <th colspan="8" style="text-align: right">{{__('sale.Total Return Amount')}}:</th>
                        <th style="text-align: right">{{ single_price($total_return_amount) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @php
            $class = '';
            if ($data->notes and app('general_setting')->terms_conditions) {
                $class = 'sale_note';
            }
        @endphp
        <div class="invoice_info margin_12 custom_margin"
            style="display: flex;justify-content: space-between; width:100%;">
            @if ($data->notes)
                <div class="{{ $class }}" style="">
                    <div class="sale_note_inner text-justify">
                        <h3 class="notes">{{ __('common.Note') }}</h3>
                        <div class="note_details">{!! $data->notes !!}</div>
                    </div>
                </div>
            @endif
            @if (app('general_setting')->terms_conditions)
                <div class="{{ $class }}"
                    @if ($data->notes) style="display: flex;justify-content: flex-end; padding-left: 60px" @else style="display: flex;justify-content: flex-end;" @endif>
                    <div class="sale_note_inner text-justify">

                        <h3 class="notes">{{ __('setting.Terms & Condition') }}</h3>
                        <div class="note_details">{{ app('general_setting')->terms_conditions }}</div>

                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="flex_auto_div">
        <p class="text-center">Remarks: {{ app('general_setting')->remarks_title }}</p>
        <span class="dashed-underline"></span>
        <p class="text-center">{{ app('general_setting')->remarks_body }}</p>
    </div>
    <footer class="invoice_footer">
        <div class="invoice_info_footer">
            <div class="invoice_logo text-center">
                <img src="{{ asset('public/frontend/img/signature.png') }}" alt="">
                <p>--------------------------</p>
                <p style="margin-bottom:0; line-height:14px;">{{ __('sale.Customer') }}</p>
                <p>{{ __('sale.Signature') }}</p>
            </div>
            <div class="invoice_logo text-center">
                <img src="{{ $data->user->signature ? asset($data->user->signature) : asset('public/frontend/img/signature.png') }}"
                    alt="">
                <p>--------------------------</p>
                <p style="margin-bottom:0; line-height:14px;">{{ __('sale.Accountant') }}</p>
                <p>{{ __('sale.Signature') }}</p>
            </div>
            <div class="invoice_logo text-center">
                <img src="{{ $data->updater->signature ? asset($data->updater->signature) : asset('public/frontend/img/signature.png') }}"
                    alt="">
                <p>--------------------------</p>
                <p style="margin-bottom:0; line-height:14px;">{{ __('sale.Authorized') }}</p>
                <p>{{ __('sale.Signature') }}</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('public/backEnd/vendors/js/jquery-3.6.0.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            window.print();
        });
    </script>
</body>

</html>
