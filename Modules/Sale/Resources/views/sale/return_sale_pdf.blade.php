<!DOCTYPE html>
<html>

<head>

    <title>Invoice</title>

    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/rtl/bootstrap.min.css" />

    <style>
        <?php
        $font_name = !$pdf_font ? 'DejDejaVu Sans' : $pdf_font->name;
        ?> @font-face {
            font-family: <?php echo $font_name; ?>;

            @if ($pdf_font)
                src: url("{{ asset('public/fonts/' . $pdf_font->font_file) }}") format('truetype');
            @endif
            font-style: normal;
        }

        body {
            font-family: <?php echo $font_name; ?>, sans-serif;
        }

        .invoice_heading {
            border-bottom: 1px solid black;
            padding: 20px;
            text-transform: capitalize;
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
            margin-top: 100px;
        }

        table {
            text-align: left;
            font-family: <?php echo $font_name; ?>, sans-serif;
            font-weight: normal;
        }

        td,
        th {
            color: #828bb2;
            font-size: 10px;
            padding: 0;
            font-family: <?php echo $font_name; ?>, sans-serif;
            font-weight: normal;
        }

        th {
            font-family: <?php echo $font_name; ?>, sans-serif;
            font-weight: normal;
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
            font-weight: normal;
            text-transform: uppercase;
        }

        .note_details {
            font-size: 12px;
            font-weight: normal;
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
            height: 40px;
        }

        .a4_width {
            max-width: 1145.28px;
            margin: auto;
        }

        .nowrap {
            white-space: nowrap;
        }

        h5 {
            font-size: 13px !important;
            font-weight: 500;
            line-height: 12px;
        }

        .hpb-1 {
            padding: 0;
        }

        .width_custom {
            max-width: 200px;
        }
    </style>
</head>

<body>
    @php
        $setting = app('general_setting');
    @endphp
    <div class="container-fluid a4_width">
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
                        $name = $data->customer_id != null ? $data->customer->name : $data->agentuser->name;
                        $mobile = $data->customer_id != null ? $data->customer->mobile : $data->agentuser->agent->phone;
                        $email = $data->customer_id != null ? $data->customer->email : $data->agentuser->email;
                        $address = $data->customer_id != null ? $data->customer->address : $data->agentuser->address;
                        $total_return_amount = 0;
                    @endphp
                    <tr>
                        <td>{{ __('common.Bill No') }}</td>
                        <td>: {{ $data->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Bill Date') }}</td>
                        <td>: {{ showDate($data->created_at) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Party Name') }}</td>
                        <td>: {{ @$name }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Party Address') }}</td>
                        <td>: {{ @$address }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Phone') }}</td>
                        <td>: {{ @$mobile }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Email') }}</td>
                        <td>: {{ @$email }}</td>
                    </tr>
                </table>
            </div>
            <div class="invoice_logo" style="width:25%">
                <table class="table-borderless mr_0 ml_auto">
                    <tr>
                        <td>{{ __('common.Served By') }}</td>
                        <td>: {{ $data->creator->name }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Entry Time') }}</td>
                        <td>: {{ date('m-d-Y H:i:s', strtotime($data->created_at)) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('sale.Ref. No') }}</td>
                        <td>: {{ $data->ref_no }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('common.Status') }}</td>
                        <td>: {{ $data->status == 1 ? trans('sale.Paid') : trans('sale.Unpaid') }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('sale.Branch') }}</td>
                        <td>: {{ @$data->quotationable->name }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="extra_div">

        </div>
        <br>
        <br>
        <div class="invoice_info">
            <table class="table table-bordered billing_info" style="width: 100%; margin-top: 100px;">
                <tr class="m-0">
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
                        <td class="p-2" style="text-align: right">{{ single_price($item->price) }}</span>
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
                        $return_tax = (($total_return_value - $return_discount)/100 * $data->total_tax);
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
                    @if ($data->notes) style="display: flex;justify-content: flex-end; padding-left: 60px"
                 @else style="display: flex;justify-content: flex-end;" @endif>
                    <div class="sale_note_inner text-justify">
                        <h3 class="notes">{{ __('setting.Terms & Condition') }}</h3>
                        <div class="note_details">{{ app('general_setting')->terms_conditions }}</div>

                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="extra_div">

    </div>
    <footer class="invoice_footer">
        <div class="invoice_info_footer">
            <div class="invoice_logo text-center">
                <img src="{{ asset('public/frontend/img/signature.png') }}" alt="">
                <p>--------------------------</p>
                <p style="margin-bottom:0; line-height:14px;">{{ __('sale.Customer') }} {{ __('sale.Signature') }}</p>
            </div>
            <div class="invoice_logo text-center">
                <img src="{{ $data->creator->signature ? asset($data->creator->signature) : asset('public/frontend/img/signature.png') }}"
                    alt="">
                <p>--------------------------</p>
                <p style="margin-bottom:0; line-height:14px;">{{ __('sale.Accountant') }} {{ __('sale.Signature') }}
                </p>
            </div>
            <div class="invoice_logo text-center">
                <img src="{{ $data->updater->signature ? asset($data->updater->signature) : asset('public/frontend/img/signature.png') }}"
                    alt="">
                <p>--------------------------</p>
                <p style="margin-bottom:0; line-height:14px;">{{ __('sale.Authorized') }} {{ __('sale.Signature') }}
                </p>
            </div>
        </div>
    </footer>
</body>

</html>
