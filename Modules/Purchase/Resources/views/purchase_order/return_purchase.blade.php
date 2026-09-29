@extends('backEnd.master')
@section('mainContent')

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{__('purchase.Purchase Return')}}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <div class="row">
                            <div class="col-md-3 col-lg-3">
                                <table class="table-borderless">
                                    <tr>
                                        <td>{{__('quotation.Date')}}</td>
                                        <td>{{$order->created_at}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('quotation.Reference No')}}</td>
                                        <td>{{$order->ref_no}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('common.Status')}}</td>
                                        <td>{{$order->status == 1 ? 'Ordered' : 'Pending'}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('purchase.Pay Term')}}</td>
                                        <td>{{$order->supplier->pay_term}} {{$order->supplier->pay_term_condition}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                <table class="table-borderless">
                                    <tr>
                                        <td><b>{{__('quotation.Supplier')}}</b></td>
                                    </tr>
                                    <tr>
                                        <td>{{$order->supplier->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="mailto:{{$order->supplier->email}}">{{$order->supplier->email}}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="tel:{{$order->supplier->mobile}}">{{$order->supplier->mobile}}</a>,
                                            <a href="tel:{{$order->supplier->alternate_contact_no}}">{{$order->supplier->alternate_contact_no}}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{$order->supplier->address}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-2 col-lg-2 mr-0">
                                <table class="table-borderless">
                                    <tr>
                                        <td><b>{{__('quotation.Shipping Address')}}</b></td>
                                    </tr>
                                    <tr>
                                        <td>{{$order->shipping_address}}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-2 col-lg-2">
                                <table class="table-borderless">
                                    <tr>
                                        <td><b>{{__('purchase.Download Attachment')}}</b></td>
                                    </tr>
                                    @if ($order->documents && count($order->documents) > 0)
                                        @foreach($order->documents as $document)
                                            @php
                                                $name = explode('/',$document)
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a href="{{asset($document)}}" target="_blank"
                                                       download>{{$name[3]}}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <table class="table-borderless">
                                    <tr>
                                        <td><b>{{__('purchase.Company')}}</b></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('purchase.Company')}}</td>
                                        <td>InfixPos</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('common.Phone')}}</td>
                                        <td><a href="tel:01631102838">01631102838</a></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('common.Email')}}</td>
                                        <td><a href="mailto:infix@pos.com">infix@pos.com</a></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('purchase.Website')}}</td>
                                        <td><a href="#">infix.pos.com</a></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-50">
                            <div class="col-12">
                                <form action="{{route("purchase.return.update",$order->id)}}" method="POST"
                                      enctype="multipart/form-data">
                                    @csrf

                                    <div class="QA_section QA_section_heading_custom check_box_table">
                                        <div class="QA_table">
                                            <table class="table">
                                                <tr>
                                                    <th class="p-2">{{__('quotation.Product Name')}}</th>
                                                    <th class="p-2">{{__('purchase.Unit Price')}}</th>
                                                    <th class="p-2">{{__('quotation.Quantity')}}</th>
                                                    <th class="p-2">{{__('quotation.Tax')}} (%)</th>
                                                    <th class="p-2">{{__('quotation.Discount')}}</th>
                                                    <th class="p-2">{{__('quotation.Subtotal')}}</th>
                                                    <th class="p-2">{{__('purchase.Return Quantity')}}</th>
                                                    <th class="p-2">{{__('purchase.Return Value')}}</th>
                                                </tr>
                                                @php
                                                    $total_return_value = 0;
                                                    $total_return_amount = 0;
                                                @endphp
                                                @foreach($order->items as $item)
                                                @php
                                                    $price_after_discount = $item->price - ($item->price/100 * $item->discount);
                                                    /* for sale return with tax or without tax */
                                                    $return_subtotal = 0;
                                                    if (app('general_setting')->is_tax_return == 'yes') {
                                                        $return_subtotal = $price_after_discount + ($price_after_discount/100 * $item->tax);
                                                    } else {
                                                        $return_subtotal = $price_after_discount;
                                                    }
                                                    $subtotal = $price_after_discount + ($price_after_discount/100 * $item->tax);

                                                    $total_return_value += $item->return_amount;
                                                @endphp
                                                    <tr>
                                                        <td class="p-2">{{@$item->productSku->product->product_name}}</td>
                                                        <td class="p-2 product_price{{$item->id}}">{{@$item->price}}</td>
                                                        <td class="p-2">
                                                            <input type="hidden" class="purchase_item_qty{{$item->id}}" value="{{@$item->quantity}}">
                                                            {{@$item->quantity}}
                                                        </td>
                                                        <td class="p-2">{{@$item->tax}}</td>
                                                        <td class="p-2">{{@$item->discount}}</td>
                                                        <td class="p-2">
                                                            <input type="hidden" class="return_subtotal{{$item->id}}" value="{{$return_subtotal}}">
                                                            {{-- <input type="hidden" class="product_price{{$item->id}}" value="{{ $subtotal * $item->quantity }}"> --}}
                                                            {{single_price(@$item->sub_total)}}
                                                        </td>
                                                        <td class="p-2"><input type="hidden" name="items[]"
                                                                               value="{{$item->id}}">
                                                            <input type="number" onkeyup="addQuantity({{$item->id}})"
                                                                   name="quantity[]" max="{{$item->quantity}}"
                                                                   value="{{$item->return_quantity}}"
                                                                   class="primary_input_field quantity quantity{{$item->id}}">
                                                            <span
                                                                class="text-danger quantity_validate{{$item->id}}"></span>
                                                        </td>

                                                        {{-- <td class="p-2 product_subtotal product_subtotal{{$item->id}}">{{$item->return_amount}}</td> --}}
                                                        <td>
                                                            <input type="number" name="product_subtotal[]"
                                                                    value="{{$item->return_amount}}" readonly
                                                                    class="primary_input_field product_subtotal text-center product_subtotal{{$item->id}}"
                                                                    step="0.01">
                                                        </td>

                                                    </tr>
                                                @endforeach
                                                @php
                                                    $return_discount = ($order->total_discount / $order->amount) * $total_return_value;
                                                    if (app('general_setting')->is_tax_return == 'yes') {
                                                        $return_tax = (($total_return_value / 100) * $order->total_vat);
                                                        $total_return_amount = ($total_return_value + $return_tax) - $return_discount;
                                                    } else {
                                                        $total_return_amount = $total_return_value - $return_discount;
                                                    }

                                                @endphp
                                                <tfoot>
                                                    <input type="hidden" class="purchase_amount" value="{{$order->amount}}">
                                                    <input type="hidden" class="purchase_total_discount" value="{{$order->total_discount}}">
                                                    <input type="hidden" class="purchase_tax" value="{{$order->total_vat}}">
                                                    <input type="hidden" class="total_return_value" value="{{$total_return_value}}">
                                                    <input type="hidden" class="is_tax_return" value="{{app('general_setting')->is_tax_return}}">
                                                    <tr>
                                                        <th colspan="7" class="text-right">{{__('sale.Return Value')}}:</th>
                                                        <th class="text-center total_return_value">{{ single_price($total_return_value) }}</th>
                                                    </tr>
                                                    @if (app('general_setting')->is_tax_return == 'yes')
                                                        <tr>
                                                            <th colspan="7" class="text-right">{{__('sale.Return Order Tax')}}:</th>
                                                            <th class="text-center order_tax">{{ single_price($return_tax) }}</th>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <th colspan="7" class="text-right">(-){{__('sale.Order Discount')}}:</th>
                                                        <th class="text-center order_discount">{{ single_price($return_discount) }}</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="7" class="text-right">{{__('sale.Total Return Amount')}}:</th>
                                                        <input type="hidden" name="total_return_amount" class="total_return_amount_input" value="{{$total_return_amount}}">
                                                        <th class="text-center total_return_amount">{{ single_price($total_return_amount) }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    @if ($order->notes)
                                        <div class="col-12 mt-10">
                                            <h3>{{__('purchase.Purchase Note')}}</h3>
                                            <p>{!! $order->notes !!}</p>
                                        </div>
                                    @endif

                                    <div class="col-12 mt-5">
                                        <div class="submit_btn text-center">
                                            <button class="primary-btn semi_large2 fix-gr-bg"><i
                                                    class="ti-check"></i>{{__('common.Save')}}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push("scripts")
<script type="text/javascript">
    // function addQuantity(id) {
    //     let price = checkNaN(parseFloat($(".product_price" + id).text()));
    //     let item_quantity = checkNaN(parseFloat($('.quantity' + id).val()));
    //     let sub_total = (price * item_quantity);
    //     $('.product_subtotal' + id).text(sub_total.toFixed(2));
    // }

    function addQuantity(id) {
        let purchase_item_qty = parseFloat($(".purchase_item_qty" + id).val()) || 0;
        let item_quantity = parseFloat($('.quantity' + id).val()) || 0;
        if (item_quantity > purchase_item_qty) {
            item_quantity = purchase_item_qty;
            toastr.warning('Return Qty not allow more than Purchase Qty','Info!');
            $('.quantity' + id).val(item_quantity).trigger('input');
        }
        let price = parseFloat($(".return_subtotal" + id).val()) || 0;
        let sub_total = (price * item_quantity).toFixed(2);
        $('.product_subtotal' + id).val(sub_total);
        $('.product_subtotal' + id).val(sub_total).trigger('input');

        totalReturnAmount();
    }
    /* net total return amount calculation */
    function totalReturnAmount() {
        let purchase_amount = parseFloat($('.purchase_amount').val()) || 0;
        let purchase_total_discount = parseFloat($('.purchase_total_discount').val()) || 0;
        let purchase_tax = parseFloat($('.purchase_tax').val()) || 0;
        let total_return_value = 0;
        $(".product_subtotal").each(function() {
            total_return_value += parseFloat($(this).val());

            console.log($(this).val());
        });
        $('.total_return_value').text(total_return_value.toFixed(2));

        let is_tax_return = $('.is_tax_return').val();
        let total_return_amount = 0;
        let return_tax = 0;
        let return_discount = 0;

        return_discount = (purchase_total_discount / purchase_amount) * total_return_value;
        if (is_tax_return == 'yes') {
            return_tax = ((total_return_value / 100) * purchase_tax);
            total_return_amount = (total_return_value + return_tax) - return_discount;
        } else {
            return_discount = (purchase_total_discount / purchase_amount) * total_return_value;
            total_return_amount = total_return_value - return_discount;
        }
        $('.order_tax').text(return_tax.toFixed(2));
        $('.order_discount').text(return_discount.toFixed(2));
        $('.total_return_amount').text(total_return_amount.toFixed(2));
        $('.total_return_amount_input').val(total_return_amount.toFixed(2));
    }
</script>
@endpush
