@extends('backEnd.master')
@section('mainContent')

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{__('sale.Sale Return')}}</h3>
                            <ul class="d-flex">
                                <li><a class="primary-btn radius_30px mr-10 fix-gr-bg"
                                        href="{{route('sale.return.print',$sale->id)}}" target="_blank">{{__('common.Print')}}</a>
                                </li>
                                <li><a class="primary-btn radius_30px mr-10 fix-gr-bg"
                                        href="{{route('sale.return.pdf',$sale->id)}}" target="_blank">{{__('sale.Pdf')}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{__('sale.Select Customer')}}</label>
                                    <input type="text" name="customer_name" class="primary_input_field"
                                           value="{{$sale->customer->name}}" placeholder="Reference No" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label"
                                           for="">{{__('sale.Select Warehouse or Branch')}}</label>
                                    <select class="primary_select mb-15 contact_type" name="warehouse_id">
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}-warehouse" {{$sale->ware_house_id == $warehouse->id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                        @endforeach
                                        @foreach($showrooms as $showroom)
                                            <option value="{{$showroom->id}}-showroom" {{$showroom->name == $sale->saleable->name ? 'selected' : ''}}>{{$showroom->name}}
                                                ({{__('sale.Branch')}})
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('warehouse_id')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{__('sale.Reference No')}}</label>
                                    <input type="text" name="ref_no" class="primary_input_field"
                                           value="{{$sale->ref_no}}" placeholder="Reference No" readonly>
                                    <span class="text-danger">{{$errors->first('ref_no')}}</span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{__('sale.Date')}}</label>
                                    <input type="text" name="date" class="primary_input_field"
                                           value="{{date('m/d/Y', strtotime($sale->date))}}" placeholder="Date"
                                           readonly>
                                    <span class="text-danger">{{$errors->first('date')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{__('sale.User')}}</label>
                                    <input type="text" name="ref_no" class="primary_input_field"
                                           value="{{$sale->user->name}}" readonly>
                                    <span class="text-danger">{{$errors->first('user_id')}}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-50">
                            <div class="col-12">
                                <table class="table">
                                    <tr>
                                        <th class="p-2" width="15%">{{__('sale.Product')}}</th>
                                        <th class="p-2">{{__('sale.Category')}}</th>
                                        <th class="p-2">{{__('sale.Unit Price')}} </th>
                                        <th class="p-2">{{__('sale.Quantity')}}</th>
                                        <th class="p-2">{{__('sale.Tax')}} (%)</th>
                                        <th class="p-2">{{__('sale.Discount')}} </th>
                                        <th class="p-2">{{__('sale.SubTotal')}} </th>
                                        <th class="p-2">{{__('sale.Returned Quantity')}}</th>
                                        <th class="p-2 text-right">{{__('sale.Return Amount')}} </th>
                                    </tr>
                                    @php
                                        $total_return_value = 0;
                                        $total_return_amount = 0;
                                    @endphp
                                    @foreach($sale->items as $item)
                                        @php
                                            $v_name = [];
                                            $v_value = [];
                                            $variantName = null;
                                            if ($item->productSku->product_variation) {
                                                foreach (json_decode($item->productSku->product_variation->variant_id) as $key => $value) {
                                                    array_push($v_name , Modules\Product\Entities\Variant::find($value)->name);
                                                }
                                                foreach (json_decode($item->productSku->product_variation->variant_value_id) as $key => $value) {
                                                    array_push($v_value , Modules\Product\Entities\VariantValues::find($value)->value);
                                                }

                                                for ($i=0; $i < count($v_name); $i++) {
                                                    $variantName .= $v_name[$i] . ' : ' . $v_value[$i] . ' ; ';
                                                }
                                            }
                                            $price_after_discount = $item->price - ($item->price/100 * $item->discount);
                                            $subtotal = $price_after_discount + ($price_after_discount/100 * $item->tax);
                                            $total_return_value += $item->return_amount;
                                        @endphp
                                        <tr>
                                            <td class="p-2">
                                                @if ($item->productable_type == "Modules\Product\Entities\ComboProduct")
                                                    {{ $item->productable->name }}
                                                @else
                                                    {{@$item->productSku->product->product_name}}
                                                    <br>
                                                    {{ $variantName }}
                                                @endif
                                            </td>
                                            <td class="p-2">
                                                {{@$item->productSku->product->category->name}}
                                            </td>
                                            <td class="p-2">{{single_price($item->price) }}</td>
                                            <td class="p-2">{{$item->quantity}}</td>
                                            <td class="p-2">{{$item->tax}}</td>
                                            <td class="p-2">{{$item->discount}}</td>
                                            <td class="p-2">{{ single_price($subtotal * $item->quantity) }}</td>
                                            <td class="p-2">{{$item->return_quantity}}</td>
                                            <td class="p-2 text-right">{{ single_price($item->return_amount) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    @php
                                        $return_discount = ($sale->total_discount / $sale->amount) * $total_return_value;
                                        if (app('general_setting')->is_tax_return == 'yes') {
                                            $return_tax = (($total_return_value-$return_discount)/100 * $sale->total_tax);
                                            $total_return_amount = ($total_return_value + $return_tax) - $return_discount;
                                        } else {
                                            $total_return_amount = $total_return_value - $return_discount;
                                        }
                                    @endphp

                                    <tfoot>
                                        <tr>
                                            <th colspan="8" class="text-right">{{__('sale.Return Value')}}:</th>
                                            <th class="text-right total_return_value">{{ single_price($total_return_value) }}</th>
                                        </tr>
                                        @if (app('general_setting')->is_tax_return == 'yes')
                                            <tr>
                                                <th colspan="8" class="text-right">{{__('sale.Return Order Tax')}}:</th>
                                                <th class="text-right order_tax">{{ single_price($return_tax) }}</th>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th colspan="8" class="text-right">(-){{__('sale.Order Discount')}}:</th>
                                            <th class="text-right order_discount">{{ single_price($return_discount) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="8" class="text-right">{{__('sale.Total Return Amount')}}:</th>
                                            <th class="text-right total_return_amount">{{ single_price($total_return_amount) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="row mt-30 mb-60 pb-50">
                                    @if ($sale->return_note)
                                        <div class="col-lg-6 col-md-6 col-sm-6 mt-10 text-justify">
                                            <h3>{{__("sale.Return Note")}}</h3>
                                            <p>{!! $sale->return_note !!}</p>
                                        </div>
                                    @endif
                                    @if ($sale->notes)
                                        <div class="col-lg-6 col-md-6 col-sm-6 mt-10 text-justify">
                                            <h3>{{__("sale.Staff Note")}}</h3>
                                            <p>{!! $sale->notes !!}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
