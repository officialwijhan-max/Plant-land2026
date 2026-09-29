<div id="printablePos" class="invoice_part_iner" @if (session()->has('sale')) style="display: none" @endif>
    <table class="invoice_table invoice_info_table" style="width: 100%;">
        <h3 style="text-align: center;color: black;margin-top: 20px;margin-bottom: 2px; font-size: 25px;">{{app('general_setting')->company_name}}</h3>
        @if ( app('general_setting')->country_name != null || app('general_setting')->country_name != '')
            <h5 style="text-align: center; color: black; margin: 0 0 5px 0;">{{ app('general_setting')->country_name }}</h5>
        @endif
        <p style="text-align: center;color: black; font-size: 12px;margin-top: 0px;">{{__('retailer.Phone')}} :
            :{{app('general_setting')->phone}}
            , {{__('retailer.Email')}}
            : {{app('general_setting')->email}}</p>
        <tbody>

        <tr>
            <td>{{__('sale.Date')}} : </td>
            <td style="text-align: right;">
                {{Carbon\Carbon::parse($sale->date)->format('d-F-Y')}}
            </td>
        </tr>
        @php
            $name = ($sale->customer_id != null) ? $sale->customer->name : $sale->agentuser->name;
            $mobile = ($sale->customer_id != null) ? $sale->customer->mobile : $sale->agentuser->agent->phone;
            $email = ($sale->customer_id != null) ? $sale->customer->email : $sale->agentuser->email;
        @endphp
        <tr>
            <td>{{__('sale.Customer')}} : </td>
            <td style="text-align: right;">{{$name}}</h6>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="table_title">
        <h3 style="font-size: 25px; text-transform: uppercase; text-align: center; color: black; margin: 5px 10px 0px 10px">{{ __('common.Invoice') }}</h3>
        <p style="text-align: center; margin-top: 0px; font-weight: bold; margin-bottom: 5px;">{{__('sale.Invoice No')}} # {{$sale->invoice_no}}</p>
    </div>
    <table class="invoice_table pdf_table_1"
           style="margin-bottom: 0 !important;color: black; width: 100%; font-size: 12px">
        <thead>
        <tr>
            <th scope="col" style="text-align: left;">{{__('common.Name')}}</th>
            <th scope="col" class="text-center" style="text-align: center">{{__('sale.QTY')}}</th>
            <th scope="col" class="text-center" style="text-align: center">{{__('sale.Price')}}</th>
            <th scope="col" class="text-center" style="text-align: center">{{__('sale.Dis')}}</th>
            <th scope="col" class="text-right" style="text-align: right">{{__('sale.Amount')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($sale->items as $key => $item)
            @php
                $product =$item->productable->product ? $item->productable->product->product_name : $item->productable->name;
            @endphp
            <tr>
                <td colspan="5" style="text-align: left;">{{ $key + 1 }}. {{$product}}
                    @if ($item->productable->product)
                        @php
                            $variantName = ''; $v_name = []; $v_value = []; $p_name = []; $p_qty = [];
                            if ($item->productable->product && $item->productable->product_variation) {

                                foreach (json_decode($item->productable->product_variation->variant_id) as $key => $value) {
                                    array_push($v_name, \Modules\Product\Entities\Variant::find($value)->name);
                                }

                                foreach (json_decode($item->productable->product_variation->variant_value_id) as $key => $value) {
                                    array_push($v_value, \Modules\Product\Entities\VariantValues::find($value)->value);
                                }

                                for ($i = 0; $i < count($v_name); $i++) {
                                    $variantName .= $v_value[$i]. ($i < (count($v_name)-1)) ?? ' - ';
                                }
                            }
                        @endphp
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <span style="font-size: 10px">
                        @if ($item->productable->getTable() != "combo_products" && $item->productable->product->product_type == "Variable" && $variantName != null)
                            ({{ $variantName }})
                        @endif
                    </span>
                </td>
            </tr>
            <tr>
                <td style="margin-bottom: 10px">
                    @if (app('general_setting')->origin == 1)
                        {{ $item->productable->product->origin }}
                    @endif
                </td>
                <td  class="text-center" style="text-align: center">{{$item->quantity}}</td>
                <td class="text-center" style="text-align: center">{{$item->price}}</td>
                <td class="text-center" style="text-align: center">{{$item->discount}}%</td>
                <td class="text-right" style="text-align: right">{{ single_price($item->sub_total)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr style="margin: 0;">
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
        $this_due = $sale->payable_amount - $sale->payments()->where('payment_type','pay')->sum('amount') - $sale->payments->sum('advance_amount') - $sale->payments()->where('payment_type','return')->sum('amount');
        $discount = $sale->total_discount;
        $vat = (($sale->amount - $discount) * $sale->total_tax) / 100;
    @endphp
    @php
        $paid =0;
    @endphp
    <table class="invoice_table dashed_table"
           style="margin-bottom: 10px;color: black; width: 100%; font-size: 12px">
        <tbody>
        <tr>
            <td class="w_70" style="text-align: left">{{__('sale.SubTotal')}}:</td>
            <td style="text-align: right;">
                <span>{{single_price($subTotalAmount)}}</span></td>
        </tr>
        <tr>
            <td class="w_70" style="text-align: left">{{__('sale.Discount (Product wise)')}}:</td>
            <td style="text-align: right;">
                <span>{{single_price($discountProductTotal)}}</span></td>
        </tr>
        <tr>
            <td class="w_70" style="text-align: left">{{__('sale.Product Tax')}}:</td>
            <td style="text-align: right;">
                <span>{{single_price($tax)}}</span></td>
        </tr>

        <tr>
            <td class="w_70" style="text-align: left">{{__('sale.Grand Total')}}:</td>
            <td style="text-align: right;">
                <span>{{ single_price($subTotalAmount - $discountProductTotal + $tax) }}</span></td>
        </tr>

        <tr>
            <td class="w_70" style="text-align: left">{{__('sale.Discount')}}:</td>
            <td style="text-align: right;">
                @if ($sale->total_discount > 0)
                    <span>-{{ single_price($sale->total_discount) }}</span></td>
            @else
                <span>{{ single_price(0)}}</span></td>
            @endif
        </tr>
        <tr>
            <td class="w_70" style="text-align: left">{{__('quotation.Other Tax')}} ({{ $sale->total_tax }}%):</td>
            <td style="text-align: right;">
                <span>{{ single_price($vat) }}</span></td>
        </tr>
        @if($sale->shipping_charge > 0)
            <tr>
                <td class="w_70" style="text-align: left">{{__('purchase.Shipping Charge')}}:</td>
                <td style="text-align: right;">
                    <span>{{ single_price($sale->shipping_charge) }}</span></td>
            </tr>
        @endif
        @if($sale->other_charge > 0)
            <tr>
                <td class="w_70" style="text-align: left">{{__('purchase.Other Charge')}}:</td>
                <td style="text-align: right;">
                    <span>{{ single_price($sale->other_charge) }}</span></td>
            </tr>
        @endif
        <tr style="font-size: 20px;
        font-weight: bold;">
            <th class="w_70" style="text-align: left">{{__('sale.Total Amount')}}:</th>
            <td style="text-align: right;">
                <span>{{ single_price($sale->payable_amount) }}</span></td>
        </tr>
        <tr>
            <td class="w_70" style="text-align: left">{{__('sale.Paid Amount')}}:</td>
            <td style="text-align: right;">
                <span>{{ single_price($sale->payments()->where('payment_type','pay')->sum('amount') + $sale->payments->sum('advance_amount'))}}</span>
            </td>
        </tr>
        <tr style="font-size: 18px;
        font-weight: bold;">
            <td class="w_70" style="text-align: left">{{__('sale.Due')}}:</td>
            <td style="text-align: right;">
                <span>{{ single_price($sale->payable_amount - $sale->payments()->where('payment_type','pay')->sum('amount'))}}</span>
            </td>
        </tr>
        @if ($sale->payable_amount - ($sale->payments()->where('payment_type','pay')->sum('amount') + $sale->payments->sum('advance_amount')) < 0)
            <tr>
                <th class="w_70" style="text-align: left">{{__('purchase.Advance Amount')}}:</th>
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
    <p style="text-align: justify;"> {{app('general_setting')->terms_conditions}}</p>
    @if ( app('general_setting')->remarks_title != null || app('general_setting')->remarks_title != '')
        <p>Remarks:</p>
        <p style="text-align: justify;">{{app('general_setting')->remarks_title}}</p>
        <span class="dashed-underline"></span>
        <p style="text-align: justify;">{{app('general_setting')->remarks_body}}</p>
    @endif
</div>
