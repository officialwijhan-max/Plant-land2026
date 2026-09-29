
@extends('backEnd.master')
@section('mainContent')
    <div id="add_product">
        <section class="admin-visitor-area up_st_admin_visitor">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{__('purchase.Edit Purchase Order')}}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="white_box_50px box_shadow_white">
                            <form action="{{route("purchase_order.update",$order->id)}}" method="POST"
                                  enctype="multipart/form-data">@method('put')
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('purchase.Select Supplier')}} *</label>
                                            <select class="select2 mb-15 single_select primary_singleSelect supplier contact_type" required name="supplier_id" id="supplier_id">
                                                <option value="{{$order->supplier_id}}" selected>{{$order->supplier->name}} @if ($order->supplier->mobile != null) ({{ $order->supplier->mobile }}) @endif</option>
                                            </select>
                                            <span class="text-danger">{{$errors->first('supplier_id')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for="">{{__('purchase.Select Warehouse or Branch')}} *</label>
                                            <select class="primary_select mb-15 house" name="showroom">
                                                @if (Auth::user()->role->type == "system_user")
                                                    <option selected disabled>{{__('common.Select')}}</option>
                                                    @foreach($warehouses as $warehouse)
                                                        <option
                                                            value="warehouse-{{$warehouse->id}}" {{$warehouse->purchases()->exists() && $warehouse->id == $order->purchasable_id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                                    @endforeach
                                                    @foreach($showrooms as $showroom)
                                                        <option
                                                            value="showroom-{{$showroom->id}}" {{$showroom->purchases()->exists() && $showroom->id == $order->purchasable_id ? 'selected' : ''}}>{{$showroom->name}}</option>
                                                    @endforeach
                                                @else
                                                    <option value="showroom-{{ $order->purchasable_id }}"
                                                            selected> {{showroomName()}}</option>
                                                @endif
                                            </select>
                                            <span class="text-danger">{{$errors->first('showroom')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for="">{{__('quotation.Attach Document')}} <span class="text-muted">({{__('quotation.pdf,csv,jpg,png,doc,docx,xlsx,zip')}})</span></label>
                                            <div class="primary_file_uploader">
                                                <input class="primary-input" type="text" id="placeholderFileOneName"
                                                       placeholder="Browse file" readonly="">
                                                <button class="" type="button">
                                                    <label class="primary-btn small fix-gr-bg"
                                                           for="document_file_1">{{__("common.Browse")}} </label>
                                                    <input type="file" class="d-none" name="documents[]"
                                                           id="document_file_1" multiple="multiple">
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{ __('sale.Date') }} *</label>
                                            <div class="primary_datepicker_input">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="">
                                                            <input placeholder="Date"
                                                                   class="primary_input_field primary-input date form-control"
                                                                   id="startDate" type="text" name="date"
                                                                   value="{{date('m/d/Y',strtotime($order->date))}}"
                                                                   autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <button class="" type="button">
                                                        <i class="ti-calendar" id="start-date-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-lg-4 col-sm-12">
                                        <div class="primary_input">
                                            <label class="primary_input_label"
                                                   for="">{{__("quotation.Shipping Address")}} </label>

                                            <input type="text" class="primary_input_field" name="shipping_address" value="{{$order->shipping_address}}">
                                            <span class="text-danger">{{$errors->first('shipping_address')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for="">{{__('sale.Reference No')}}</label>
                                            <input type="text" class="primary_input_field"
                                                   placeholder="{{__('sale.Reference No')}}" name="ref_no"
                                                   value="{{ $order->ref_no }}">
                                            <span class="text-danger">{{$errors->first('ref_no')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for="">{{__('purchase.LC No.')}}</label>
                                            <input type="text" class="primary_input_field"
                                                   placeholder="{{__('purchase.LC No.')}}" name="lc_no"
                                                   value="{{ $order->lc_no }}">
                                            <span class="text-danger">{{$errors->first('lc_no')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('purchase.CNF Agent')}}</label>
                                               <select class="primary_select mb-15 house cnf_agent" name="cnf_agent">
                                                   <option value="null">{{ __('purchase.choose one') }}</option>
                                                   @foreach ($cnfs->where('status', 1) as $key => $cnf)
                                                       <option value="{{ $cnf->id }}" @if ($order->cnf_id == $cnf->id) selected @endif>{{ $cnf->name }}</option>
                                                   @endforeach
                                               </select>
                                            <span class="text-danger">{{$errors->first('cnf_agent')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('sale.Discount')}}</label>
                                            <input onkeyup="billingInfo()" name="total_discount"
                                                   type="number" step="0.01"
                                                   value="{{$order->total_discount}}"
                                                   class="primary_input_field billing_inputs total_discount">
                                            <span class="text-danger">{{$errors->first('discount_type')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for="">{{__('quotation.Discount Type')}}</label>
                                            <select class="primary_select mb-15 discount_type"
                                                    onchange="billingInfo()" id="discount_type"
                                                    name="discount_type">
                                                <option
                                                    value="2" {{$order->discount_type == 2 ? 'selected' : ''}}>{{ __('quotation.Percentage') }}</option>
                                                <option
                                                    value="1" {{$order->discount_type == 1 ? 'selected' : ''}}>{{ __('quotation.Amount') }}</option>
                                            </select>
                                            <span class="text-danger">{{$errors->first('discount_type')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-12 col-sm-12">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label">{{__('quotation.Select Product')}}*</label>
                                            <select class="single_select primary_singleSelect mb-15 product_info" name="product" id="product_info">
                                                <option>{{__('quotation.Select Product')}}</option>
                                            </select>
                                            <span class="text-danger">{{$errors->first('product')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-sm-12">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('sale.Payment Method')}}
                                                *</label>
                                            <select class="primary_select mb-15 payment_method" name="payment_method[]"
                                                    onchange="showDiv()">
                                                <option value="due-00">{{__('sale.Due')}}</option>
                                                <option
                                                    value="cash-00" {{app('general_setting')->payment_gateway == 1 ? 'selected' : ''}}>{{__('pos.Cash')}}</option>
                                                @foreach (\Modules\Account\Entities\ChartAccount::where('configuration_group_id', 2)->get() as $bank_account)
                                                    <option
                                                        value="bank-{{ $bank_account->id }}" {{app('general_setting')->payment_gateway == 2 ? 'selected' : ''}}>{{ $bank_account->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{$errors->first('payment_method')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-sm-12 amount_div">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('sale.Amount')}}</label>
                                            <input type="text" name="amount[]"
                                                   class="primary_input_field amount_payment"
                                                   placeholder="Enter Amount">
                                        </div>
                                    </div>
                                    <input type="hidden" name="already_paid_amount" id="already_paid_amount" value="{{ $order->payments()->where('payment_type','pay')->sum('amount') }}">
                                    <div class="col-md-4 col-lg-4 col-sm-6 amount_div">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('sale.Due')}}</label>
                                            <input type="text" name="due_amount[]"
                                                   class="primary_input_field due_amount"
                                                   placeholder="{{ __('sale.Due') }}" value="{{ $order->payable_amount - $order->payments()->where('payment_type','pay')->sum('amount') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row bank_info_div"
                                     @if(app('general_setting')->payment_gateway != 2 ) style="display: none" @endif>
                                    <div class="col-md-6 col-lg-6 col-sm-12">
                                        <div class="primary_input mb-15"><label class="primary_input_label"
                                                                                for="">{{__('sale.Bank Name')}}</label><input
                                                type="text" name="bank_name[]" class="primary_input_field"
                                                placeholder="Bank Name"></div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-sm-12">
                                        <div class="primary_input mb-15"><label class="primary_input_label"
                                                                                for="">{{__('sale.Branch')}}</label><input
                                                type="text" name="branch[]" class="primary_input_field"
                                                placeholder="Branch"></div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-sm-12">
                                        <div class="primary_input mb-15"><label class="primary_input_label"
                                                                                for="">{{__('sale.Account No')}}</label><input
                                                type="text" name="account_no[]" class="primary_input_field"
                                                placeholder="Account No"></div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-sm-12">
                                        <div class="primary_input mb-15"><label class="primary_input_label"
                                                                                for="">{{__('sale.Account Owner')}}</label><input
                                                type="text" name="account_owner[]" class="primary_input_field"
                                                placeholder="Account Owner"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <ul id="sms_setting" class="permission_list sms_list">
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="delete_previous_amount" type="checkbox">
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ __('sale.Delete Previously Paid Amount Records') }}</p>
                                        </ul>
                                    </div>
                                </div>
                                <table class="table ">
                                    <thead>
                                    <tr>
                                        <th width="20%" scope="col">{{__('sale.Product Name')}}</th>
                                        @if (app('general_setting')->origin == 1)
                                            <th width="10%" scope="col">{{__('common.Part Number')}}</th>
                                        @else
                                            <th width="10%" scope="col">{{__('sale.SKU')}}</th>
                                        @endif
                                        <th width="10%" scope="col">{{__('product.Model')}}</th>
                                        <th width="10%" scope="col">{{__('product.Brand')}}</th>
                                        <th width="10%" scope="col">{{__('sale.Price')}}</th>
                                        <th width="10%" scope="col">{{__('sale.Sell Price')}}</th>
                                        <th width="5%" scope="col">{{__('sale.Quantity')}}</th>
                                        <th width="5%" scope="col">{{__('sale.Tax')}} (%)</th>
                                        <th width="5%" scope="col">{{__('sale.Discount')}}</th>
                                        <th width="10%" scope="col">{{__('sale.SubTotal')}}</th>
                                        <th width="5%" scope="col">{{__('common.Action')}}</th>
                                    </tr>
                                    </thead>
                                    @php
                                        $sub_total = 0;
                                        $vat = 0;
                                    @endphp
                                    <tbody id="product_details">
                                    @foreach($order->items as $key=> $item)
                                        @php
                                            $v_name = [];
                                            $v_value = [];
                                            $p_name = [];
                                            $p_qty = [];
                                            $variantName = variantName($item);
                                            $sub_total += $item->price * $item->quantity;
                                            $vat += ($item->price * $item->quantity * $item->tax) / 100;
                                            $type =$item->product_sku_id.",'sku'" ;
                                        @endphp
                                        <tr>
                                            <td><input type="hidden" name="items[]" value="{{$item->product_sku_id}}">
                                                {{$item->productable->product->product_name}} <br>
                                                @if ($variantName)
                                                    ({{ $variantName }})
                                                @endif
                                            </td>
                                            <td>
                                                @if (app('general_setting')->origin == 1)
                                                    {{@$item->productable->product->origin}}
                                                @else
                                                    {{@$item->productable->sku}}
                                                @endif
                                            </td>
                                            <td>{{@$item->productable->product->model->name}}</td>
                                            <td>{{@$item->productable->product->brand->name}}</td>
                                            <td><input min="{{$item->productSku->purchase_price}}"
                                                       onkeyup="priceCalc({{$type}})"
                                                       class="primary_input_field product_price product_price_sku{{$item->product_sku_id}}"
                                                       type="number"
                                                       value="{{$item->price}}" name="item_price[]"></td>
                                           <td><input min="{{$item->productSku->purchase_price}}"
                                                      class="primary_input_field"
                                                      type="number"
                                                      value="{{$item->productSku->selling_price}}" name="item_selling_price[]"></td>
                                            <td>
                                                <input type="number" onkeyup="addQuantity({{$type}})"
                                                       name="item_quantity[]" value="{{$item->quantity}}"
                                                       class="primary_input_field quantity quantity_sku{{$item->product_sku_id}}">
                                            </td>
                                            <td>
                                                <input type="number" name="item_tax[]" step="0.01"
                                                       value="{{$item->tax}}" onkeyup="addTax({{$type}})"
                                                       net-sub-total="{{ ($item->price - $item->discount) * $item->quantity * $item->tax  / 100 }}"
                                                       class="primary_input_field tax tax_sku{{$item->product_sku_id}}">
                                            </td>
                                            <td>
                                                <input type="number" onkeyup="addDiscount({{$type}})" step="0.01"
                                                       name="item_discount[]" value="{{$item->discount}}"
                                                       class="primary_input_field discount discount_sku{{$item->product_sku_id}}">
                                            </td>
                                            <td style="text-align: center;" class="product_subtotal product_subtotal_sku{{$item->product_sku_id}}">{{$item->sub_total}}</td>
                                            <td><a data-id="{{$item->id}}" data-product="{{$item->id}}"
                                                   class="new_delete_product primary-btn primary-circle fix-gr-bg" href="javascript:void(0)">
                                                    <i class="ti-trash"></i></a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="col-12 mb-50">
                                    <div class="row justify-content-end">
                                        <div class="col-lg-4 col-md-6">
                                            <div class="primary_input grid_input">
                                                <label class="font_13 theme_text f_w_500 mb-0" for="">{{__('sale.Total Quantity')}}</label>
                                                    <input type="number" class="primary_input_field total_quantity"
                                                    value="{{$order->total_quantity}}" name="total_quantity" readonly>
                                            </div>
                                            <div class="primary_input grid_input">
                                                <label class="font_13 theme_text f_w_500 mb-0" for="">{{__('sale.SubTotal')}}</label>
                                                    <input type="text" class="primary_input_field total_sub_total"
                                                    value="{{$sub_total}}" name="item_amount" readonly="readonly">

                                            </div>
                                            <div class="primary_input grid_input product_discounts" @if ($order->items->sum('discount') == 0)
                                                style="display: none;"
                                            @endif >
                                                @php
                                                    $totalProductwiseDiscount = 0;
                                                    foreach ($order->items as $key => $itemData) {
                                                        $totalProductwiseDiscount += $itemData->quantity * $itemData->discount;
                                                    }
                                                @endphp

                                                <label class="font_13 theme_text f_w_500 mb-0" for="">{{__('sale.Product Wise Discount')}}</label>
                                                    <input type="text" class="primary_input_field product_discounts"
                                                    value="{{$totalProductwiseDiscount}}" name="item_amount" readonly="readonly">

                                            </div>
                                            <div class="primary_input grid_input product_tax" @if ($order->items->sum('tax') == 0)
                                                style="display: none;"
                                            @endif >
                                                @php
                                                    $totalProductwiseTax = 0;
                                                    foreach ($order->items as $key => $itemData) {
                                                        $totalProductwiseTax += ($itemData->price - $itemData->discount) * $itemData->quantity * $itemData->tax  / 100;
                                                    }
                                                @endphp
                                                <label class="font_13 theme_text f_w_500 mb-0" for="">{{__('sale.Product Wise Tax')}}</label>
                                                <div class="input-group primary_input_coupon">
                                                    <input type="text" class="primary_input_field billing_inputs product_tax product_tax_input"
                                                    value="{{ $totalProductwiseTax }}" name="item_amount" readonly="readonly">
                                                    <div class="input-group-append">
                                                        <button> </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="primary_input grid_input">
                                                <label class="font_13 theme_text f_w_500 mb-0" for="">{{__('sale.Grand Total')}}</label>
                                                    <input type="text" class="primary_input_field total_price"
                                                        value="{{$order->amount}}" name="item_amount" readonly="readonly">

                                            </div>
                                            <div class="primary_input grid_input">
                                                <label class="font_13 theme_text f_w_500 mb-0" for="">{{__('purchase.Order Tax')}}</label>
                                                    <select onchange="billingInfo()" name="total_tax" id=""
                                                    class="primary_select  total_tax">
                                                        <option value="0-0">{{__('pos.No Tax')}}</option>
                                                        @foreach($taxes as $key=>$tax)
                                                            <option value="{{$tax->rate}}-{{ $tax->id }}" {{$order->total_vat == $tax->rate ? 'selected' : ''}}>{{ $tax->rate }}% {{ $tax->name }}</option>
                                                        @endforeach
                                                    </select>
                                            </div>
                                            <div class="primary_input grid_input total_discount_tr" @if ($order->total_discount == 0)
                                                 style="display: none;"
                                            @endif>
                                                <label class="font_13 theme_text f_w_500 mb-0" for="">{{__('sale.Discount')}}</label>
                                                    <input style="width: 100px !important" name="total_discount_amount" type="number" value="{{$order->total_discount}}"
                                                    class="primary_input_field total_discount_amount total_discount " readonly>

                                            </div>
                                            <div class="primary_input grid_input">
                                                <label class="font_13 theme_text f_w_500 mb-0" for="">{{__('purchase.Shipping Charge')}}</label>
                                                    <input onkeyup="billingInfo()" name="shipping_charge" type="number" step="0.01" value="{{$order->shipping_charge}}" class="primary_input_field shipping_charge">

                                            </div>
                                            <div class="primary_input grid_input">
                                                <label class="font_13 theme_text f_w_500 mb-0" for="">{{__('purchase.Other Charge')}}</label>
                                                    <input onkeyup="billingInfo()" name="other_charge" type="number" step="0.01" value="{{$order->other_charge}}" class="primary_input_field other_charge">

                                            </div>
                                            <div class="primary_input grid_input">
                                                <label class="font_13 theme_text f_w_500 mb-0" for="">{{__('sale.Payable Amount')}}</label>
                                                    <input type="text" value="{{$order->payable_amount}}" class="primary_input_field total_amount" name="total_amount" readonly>

                                            </div>
                                        </div>
                                    </div>
                            </div>

                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="primary_input">
                                            <label class="primary_input_label"
                                                   for="">{{__("purchase.Order Note")}} </label>

                                            <textarea class="summernote3" name="notes">{{$order->notes}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="paid_amount" class="paid_amount" value="{{$order->payments()->where('payment_type','pay')->sum('amount') - $order->payments()->where('payment_type','return')->sum('amount')}}">
                                <div class="row mt-20 justify-content-center text-center">
                                    <button type="button" onclick="approve_modal('{{route('purchase_order.index')}}')"
                                            class="primary-btn mr-2 fix-gr-bg"><i
                                            class="fa fa-times"></i> {{__('common.Cancel')}}</button>
                                    <button type="button" class="primary-btn mr-2 fix-gr-bg reset_btn"><i
                                            class="fa fa-refresh"></i> {{ __('report.Reset') }}</button>

                                    <button type="submit" class="primary-btn mr-2 fix-gr-bg">{{__('common.Submit')}}</button>
                                </div>
                                <div class="payment_modal"></div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <input type="hidden" data-id="{{$order->id}}" value="{{urlShortener()}}" class="url">
        <div class="view_modal">

        </div>
        @include('backEnd.partials.approve_modal')
        <input type="hidden" name="supplier_list_select_option" id="supplier_list_select_option" value="{{ route('supplier.supplier_select_list_option') }}">
        <input type="hidden" name="products_list_select_option" id="products_list_select_option" value="{{ route('purchase_product_select_list_option') }}">
    </div>
@endsection

@push("scripts")
<script src="{{ Module::asset('core:contact_select.js') }}"></script>
<script src="{{ Module::asset('core:product_select.js') }}"></script>
<script type="text/javascript">
    var baseUrl = $('#app_base_url').val();

    function productTax(id, type) {
        let product_tax = 0;
        $.each($('.tax'), function (index, value) {
            let amount = $(this).attr('net-sub-total');
            product_tax += parseFloat(amount);
        });
        if (product_tax > 0){
            $('.product_tax').show();
        }else{
            $('.product_tax').hide();
        }
        $('.product_tax_input').val(product_tax);
    }

    function showDiv() {
        if ($('.payment_method').val().split('-')[0] == 'bank') {
            $('.amount_div').show();
            $('.bank_info_div').show();
            $('.amount_payment').removeAttr('disabled');
        } else if ($('.payment_method').val().split('-')[0] == 'cash') {
            $('.amount_div').show();
            $('.bank_info_div').hide();
            $('.amount_payment').removeAttr('disabled');
        } else {
            $('.amount_div').hide();
            $('.bank_info_div').hide();
            $('.amount_payment').attr('disabled', 'disabled');
        }
    }

    function calcutionTaxDiscount(tr){
        let taxParcentage = parseFloat(tr.find('.tax').val());
        let productPrice =  parseFloat(tr.find('.product_price ').val());
        let discount =  parseFloat(tr.find('.discount ').val());
        let qty =  parseInt(tr.find('.quantity').val());
        let netSubTotal = (( productPrice - discount) * qty) * taxParcentage /100;
        tr.find('.tax').attr('net-sub-total',netSubTotal)
    }

    function productDiscount() {
        let product_discounts = 0;
        $.each($('.discount'), function (index, value) {
            let amount = $(this).val();
            let tr = $(this).parent().parent();
            let qty =  parseInt(tr.find('.quantity').val());
            let totalDiscount = amount * qty;
            product_discounts += parseInt(totalDiscount);
            calcutionTaxDiscount(tr);
            productTax(1,2)

        });
        if (product_discounts > 0){
            $('.product_discounts').show();
        }else{
            $('.product_discounts').hide();
        }
        $('.product_discounts').val(product_discounts);
    }

function productSubTotal(id, type) {
    let price = $(".product_price_" + type + id).val();
    let quantity = $('.quantity_' + type + id).val();
    let sub_total = price * quantity;
    $('.product_subtotal_' + type + id).text(sub_total);
}

function totalQuantity() {
    let total_quantity = 0;
    $.each($('.quantity'), function (index, value) {
        let amount = $(this).val();
        total_quantity += parseInt(amount);
    });

    if (total_quantity > 0 || !isNaN(total_quantity)) {
        $('.total_quantity').text(total_quantity);
        $('.total_quantity').val(total_quantity);
    } else {
        total_quantity = $('.total_quantity').text();
        $('.total_quantity').text(total_quantity);
        $('.total_quantity').val(total_quantity);
    }
}

function SubTotal() {
    let total_amount = 0;

    $.each($('.product_subtotal'), function (index, value) {
        let amount = $(this).text();
        total_amount += parseFloat(amount);
    });
    $('.total_sub_total').val(total_amount);
}

function grandTotal() {
    let amount = parseFloat($('.total_sub_total').val());
    let discount = parseFloat($('.product_discounts').val());
    let tax = parseFloat($('.product_tax_input').val());
    if (isNaN(tax))
        tax = 0;
    let final_amount = parseFloat((amount + tax) - discount).toFixed(2);
    $('.total_price').val(final_amount);
}

function billingInfo() {
    let total_discount = parseFloat($('.total_discount').val());
    let total_tax = parseFloat($('.total_tax').val());
    let shipping_charge = parseFloat($('.shipping_charge').val());
    let other_charge = parseFloat($('.other_charge').val());
    let total_amount = parseFloat($('.total_price').val());
    let calculated_total_tax = 0;
    let calculated_total_discount = 0;
    if (total_tax > 0) {
        calculated_total_tax = ((total_amount) * total_tax) / 100;
    }
    let discount_type = $('.discount_type').val();

    if (total_discount > 0) {
        $('.total_discount_tr').show();
        if (discount_type == 2) {
            calculated_total_discount = (total_amount * total_discount) / 100;
        } else {
            calculated_total_discount = total_discount;
        }
    }
    else
        $('.total_discount_tr').hide();

    $('.total_discount_amount').val(calculated_total_discount);

    let final_amount = parseFloat(((total_amount + calculated_total_tax + shipping_charge + other_charge) - calculated_total_discount)).toFixed(2);
    $('.total_amount').val(final_amount);
    var already_paid_amount = parseFloat($('#already_paid_amount').val());
    $('.due_amount').val(parseFloat(final_amount - already_paid_amount));
}

    $(document).ready(function () {

        let delete_selector = $('.delete_product');

        if (delete_selector.length > 1) {
            delete_selector.show();
        } else {
            delete_selector.hide();
        }

        $(document).on('input', '.tax', function () {

            let taxParcentage = parseFloat($(this).val());
            let tr = $(this).parent().parent();
            let productPrice =  parseFloat(tr.find('.product_price ').val());
            let discount =  parseFloat(tr.find('.discount ').val());
            let qty =  parseInt(tr.find('.quantity').val());
            let netSubTotal = (( productPrice - discount) * qty) * taxParcentage /100;
            $(this).attr('net-sub-total',netSubTotal)
            productTax(1,2)
        });


        $(document).on('change','.discount_type',function(){
            billingInfo()
        });

        $(document).on('change', '.product_info', function () {
            let id = $(this).val();
            $.post('{{ route('purchase.product_modal_for_select') }}', {
                _token: '{{ csrf_token() }}',
                id: id
            }, function (data) {
                if (data.product_type == "Variable") {
                    $('.view_modal').html(data.html);
                    $('#Item_Details').modal('show');
                } else {
                    $('#product_details').append(data.product_id);
                    $('select').niceSelect();
                    totalQuantity();
                    productDiscount();
                    totalQuantity();
                    SubTotal();
                    grandTotal();
                    billingInfo();
                }
                $('.charging_infos').show();
            });
        });

        $(document).on('click', '.delete_product', function () {
            var whichtr = $(this).closest("tr");
            var id = $(this).data('id');

            whichtr.remove();
            totalQuantity();
            productDiscount();
            totalQuantity();
            SubTotal();
            billingInfo();
            $.ajax({
                url: "{{route('item.session.delete')}}",
                method: "POST",
                data: {
                    id: id,
                    _toke: "{{csrf_token()}}"
                },
                success: function (result) {
                    toastr.success(result.success);
                }
            })
        });

        $(document).on('click', '.new_delete_product', function () {
            var whichtr = $(this).closest("tr");
            var id = $(this).data('id');
            let quantity = $('.quantity' + id).val();
            whichtr.remove();
            totalQuantity();
            productTax();
            productDiscount();
            totalQuantity();
            SubTotal();
            billingInfo();
            $.ajax({
                method: "POST",
                url: "{{route('item.delete')}}",
                data: {
                    id: id,
                    quantity: quantity,
                    _token: "{{csrf_token()}}"
                },
                success: function (result) {
                    toastr.error(result)
                }
            })
        });

    });

    function addQuantity(id, type) {
        let price = $(".product_price_" + type + id).val();
        let quantity = $('.quantity_' + type + id).val();
        let sub_total = (price * quantity);
        $('.product_subtotal_' + type + id).text(sub_total);
        totalQuantity();
        productTax(id, type);
        productDiscount();
        totalQuantity();
        SubTotal();
        grandTotal();
        billingInfo();
    }

    function addDiscount(id, type) {
        let price = $('.product_price_' + type + id).val();
        let quantity = $('.quantity_' + type + id).val();
        let sub_total = price * quantity;
        $('.product_subtotal_' + type + id).text(sub_total);
        totalQuantity();
        productTax(id, type);
        productDiscount();
        totalQuantity();
        SubTotal();
        grandTotal();
        billingInfo();
    }

    function addTax(id, type) {
        let price = $('.product_price_' + type + id).val();
        let quantity = $('.quantity_' + type + id).val();

        let sub_total = price * quantity;
        $('.product_subtotal_' + type + id).text(sub_total);
        totalQuantity();
        productTax(id, type);
        productDiscount();
        totalQuantity();
        SubTotal();
        grandTotal();
        billingInfo();
    }

    function saleDetails() {
        let customer_id = $('.contact_type').val();
        $.ajax({
            method: 'POST',
            url: "{{route('customer.details')}}",
            data: {
                customer_id: customer_id,
                _token: "{{csrf_token()}}",
            },

            success: function (result) {
                $('.last_sale_price').text('$' + result.total_amount);
                $('.last_sale_invoice').text(result.invoice_no);
                if (result.invoice_no)
                    $('.sale_details').show()
                else
                    $('.sale_details').hide();
            }
        })
    }

    function priceCalc(id, type) {
        let price = $(".product_price_" + type + id).val();
        let quantity = $('.quantity_' + type + id).val();
        let sub_total = price * quantity;
        $('.product_subtotal_' + type + id).text(sub_total);
        totalQuantity();
        productTax(id, type);
        productDiscount();
        totalQuantity();
        SubTotal();
        grandTotal();
        billingInfo();

    }

    function paymentModal() {
        let method = $('.payment_method').val();
        let supplier = $('.contact_type option:selected').text();
        let total_amount = $('.total_amount').val();
        let total_quantity = $('.total_quantity').val();
        let paid_amount = $('.paid_amount').val();

        $.ajax({
            url: "{{route('purchase_multiple_payment')}}",
            method: "POST",
            data: {
                type: method,
                _token: "{{csrf_token()}}",
                supplier: supplier,
                total_amount: total_amount,
                total_qty: total_quantity,
                paid_amount: paid_amount,
            },
            success: function (result) {
                $('.payment_modal').html(result);
                $('#Pos_Payment_Multiple').modal('show');
                $('select').niceSelect();
            }
        })
    }

    $(document).on('click', '.reset_btn', function () {
        window.location.reload();
    })
</script>

@endpush
