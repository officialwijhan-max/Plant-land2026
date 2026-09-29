<form action="{{route("pos-order.store")}}" method="POST" enctype="multipart/form-data" class="pos_form w-100 pos_page__order" id="post_form_submit">
    @csrf
    <div class="pos_white_box w-100">
        <div class="input-group pos_primary_select dark_select">
            <select class="primary_select mb-30 customer" name="customer_id" onchange="saleDetails()" required>
                @foreach($customers as $customer)
                    <option value="customer-{{$customer->id}}" {{isset($pos) && $pos->customer_id == $customer->id ? 'selected' : ''}}>
                        @if ($customer->id == 1)
                            {{$customer->name}} (Default)
                        @else {{$customer->name}}
                            @if ($customer->state_id != null || $customer->state_id != 0)
                                , {{ $customer->state->name }} ,
                            @endif
                            @if ($customer->mobile != null)
                                ({{ $customer->mobile }})
                            @endif
                        @endif
                    </option>
                @endforeach
                @foreach($retailers as $retailer)
                    <option value="retailer-{{$retailer->id}}" {{isset($pos) && $pos->customer_id == $customer->id ? 'selected' : ''}}>
                        {{@$retailer->user->name}} (Retailer)
                        @if ($retailer->state_id != null || $retailer->state_id != 0)
                            , {{ $retailer->state->name }} ,
                        @endif
                        @if ($retailer->phone != null)
                            ({{ $retailer->phone }})
                        @endif
                    </option>
                @endforeach
            </select>
            <div class="input-group-append plus_button">
                <button class="primary-btn primary-circle fix-gr-bg"  data-toggle="modal" data-target="#add_customer"   type="button"> <i class="fas fa-plus"></i></button>
            </div>
        </div>


        <div class="QA_section3 posfull_table QA_section_heading_custom th_padding_l0">
            <div class="d-flex justify-content-between">
                <h6 class="customer_due" style="display: none">
                    {{__('sale.Total Due')}} : <a class="balance_due" target="_blank" href="{{route('due.invoice.list')}}"></a>
                </h6>
                <h6 class="text-danger customer_invoice pb-3" style="display: none">
                    {{__('sale.Last Invoice')}} :<a href="javascript:void(0)" data-toggle="modal" onclick="invoiceDetail()" class="invoice_link"></a>
                </h6>
            </div>
            <div class="QA_table">
                <!-- table-responsive -->
                <div class="table-responsive">
                    <table class="table shadow_none pb-0">
                        <thead>
                            <tr>
                                <th scope="col">{{ trans("product.Product") }}</th>
                                <th scope="col">{{ trans("product.SKU") }}</th>
                                <th scope="col" class="@if (app('general_setting')->display_last_price_in_sales == 0) d-none @endif">{{ trans("sale.last_price") }}</th>
                                <th scope="col" class="d-none">{{ trans("core::core.serial_key") }}</th>
                                <th scope="col">{{ trans("product.QTY") }}</th>
                                <th scope="col">{{ trans("product.Price") }}</th>
                                <th scope="col">{{ (app('general_setting')->enable_gst == 1) ? trans("core::core.gst") : trans('setup.Tax') }}</th>
                                <th scope="col">{{ trans("sale.Discount") }} (%)</th>
                                <th scope="col">{{ trans("sale.SubTotal") }}</th>
                                <th scope="col" class="text-center"><i class="fas fa-cog required_mark2 mr-1 f_s_13"></i></th>
                            </tr>
                        </thead>
                        <tbody id="product_details">
                            <input type="hidden" name="due" id="due" value="0">
                            @if(session()->has('carts') && count(session()->get('carts')) > 0)
                                @php
                                    $price = array_sum(array_column(session()->get('carts'), 'sub_total'));
                                @endphp
                                @foreach (session()->get('carts') as $key => $item)
                                    @php
                                        $quantity++;
                                        if (isset($item['taxProduct'])) {
                                            $productTaxTotal += (double)$item['taxProduct'];
                                        }
                                    @endphp
                                    @if ($item['type'] == 'sku')
                                        @php
                                            $type = $item['product_sku_id'].",'sku'" ;
                                            // $part_numbers = Modules\Product\Entities\ProductSku::find($item['product_sku_id'])->part_numbers->where('is_sold', 0);
                                        @endphp

                                        <tr>
                                            <td><input type="hidden" name="product_id[]" value="{{$item['product_sku_id']}}">
                                                {{$item['product']}}
                                                <br><small>{{$item['product_origin']}}</small>
                                                <br>
                                            </td>
                                            <td>{{$item['sku']}}</td>
                                            <td class="@if (app('general_setting')->display_last_price_in_sales == 0) d-none @endif"></td>
                                            <td class="td_max_width d-none">
                                                <a href="javascript:void(0)" title="Serial Key Add" class="serial_key_add_btn" data-id="{{ $item['product_sku_id'] }}-sku"  data-target="#{{ $item['product_sku_id'] }}-sku">
                                                    <i class="ti-clipboard mr-2"></i>{{ trans('common.Serial Key') }}
                                                </a>
                                                <div class="modal fade admin-query" id="{{ $item['product_sku_id'] }}-sku">
                                                    <div class="modal-dialog modal_1000px modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title"></h4>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <i class="ti-close "></i>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body product_detail_modal">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <select multiple="multiple" class="multypol_check_select active position-relative" id="serial_no" name="serial_no[]" multiple>
                                                                            {{-- @if ($part_numbers->count() > 0)
                                                                                @foreach ($part_numbers as $key => $part_number)
                                                                                    <option value="{{ $part_number->id }}">{{ $part_number->seiral_no }}</option>
                                                                                @endforeach
                                                                            @endif --}}
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <div class="d-flex justify-content-center pt_20">
                                                                            <button type="button" class="primary-btn radius_30px fix-gr-bg" data-dismiss="modal"><i class="ti-check"></i>{{ __("base::base.save") }}</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <input class="product_min_price_sku{{$item['product_sku_id']}}" type="hidden" @if (isset($item['min_selling_price'])) value="{{ $item['min_selling_price'] }}" @else value="0" @endif>
                                            <input class="min_sell_qty_sku{{$item['product_sku_id']}}" type="hidden" value="0" name="min_sell_qty[]">
                                            <td>
                                                <input type="number"
                                                       onkeyup="addQuantity({{$type}})"
                                                       name="quantity[]"
                                                       value="{{$item['quantity']}}"
                                                       class="primary_input_field2 quantity quantity_sku{{$item['product_sku_id']}}">
                                            </td>
                                            <td><input
                                                    min="{{$item['price']}}" data-product-price="{{$item['only_price']}}"
                                                    onkeyup="priceCalc({{$type}})" step="0.01"
                                                    class="primary_input_field2 product_price product_price_sku{{$item['product_sku_id']}}"
                                                    type="number"
                                                    data-type="sku"
                                                    data-sku-id="{{ $item['product_sku_id'] }}"
                                                    value="{{$item['price']}}"
                                                    name="product_price[]">
                                            </td>
                                            <td>
                                                <input name="product_tax_amount[]" step="0.001" data-type="sku" data-sku-id="{{ $item['product_sku_id'] }}" class="primary_input_field2 product_tax_amount product_tax_amount_sku{{$item['product_sku_id']}}" type="number" readonly value="0">
                                            </td>
                                            <td>
                                                <input type="number" name="product_discount[]" value="0" onkeyup="addDiscount({{$type}})" class="primary_input_field2 discount discount_sku{{$item['product_sku_id']}}">
                                            </td>
                                            @if (app('general_setting')->enable_gst == 0)
                                                <input type="hidden" name="product_tax[]" net-sub-total="{{ $item['taxProduct'] }}" value="{{ $item['taxSku'] }}" onkeyup="addTax({{$type}})" class="primary_input_field tax tax_sku{{$item['product_sku_id']}}">
                                            @else
                                            <input type="hidden" name="product_igst[]" net-sub-total="{{ $item['igstProduct'] }}" value="{{ $item['igstVal'] }}" onkeyup="addTax({{$type}})" class="primary_input_field igst igst_sku{{$item['product_sku_id']}}">
                                            <input type="hidden" name="product_cgst[]" net-sub-total="{{ $item['cgstProduct'] }}" value="{{ $item['cgstVal'] }}" onkeyup="addTax({{$type}})" class="primary_input_field cgst cgst_sku{{$item['product_sku_id']}}">
                                            <input type="hidden" name="product_sgst[]" net-sub-total="{{ $item['sgstProduct'] }}" value="{{ $item['sgstVal'] }}" onkeyup="addTax({{$type}})" class="primary_input_field sgst sgst_sku{{$item['product_sku_id']}}">
                                            <input type="hidden" name="product_cess[]" net-sub-total="{{ $item['cessProduct'] }}" value="{{ $item['cessVal'] }}" onkeyup="addTax({{$type}})" class="primary_input_field cess cess_sku{{$item['product_sku_id']}}">
                                            @endif

                                            <td class="product_subtotal product_subtotal_sku{{$item['product_sku_id']}}">{{ str_replace(',','',number_format($item['sub_total'],2))}}</td>
                                            <td class="text-center"><a
                                                    data-id="{{$item['product_sku_id']}}"
                                                    data-product="{{$item['product_sku_id']}}"
                                                    class="delete_product primary-btn"
                                                    href="javascript:void(0)">
                                                    <i class="fas fa-trash-alt required_mark2 f_s_13"></i></a></td>
                                        </tr>
                                    @else
                                        @php
                                            $type =$item['product_sku_id'].",'combo'" ;
                                            $comboProducts = Modules\Core\Entities\Product\ComboProduct::find($item['product_sku_id'])->combo_products;
                                            // foreach ($comboProducts as $key => $comboProduct) {
                                            //     $part_numbers = Modules\Product\Entities\ProductSku::find($comboProduct->product_sku_id)->part_numbers->where('is_sold', 0);
                                            //     if ($key == 0) {
                                            //         $part_numbers_all = $part_numbers;
                                            //     }
                                            //     $part_numbers_all_list = $part_numbers_all->merge($part_numbers);
                                            // }
                                        @endphp
                                        <tr>
                                            <td><input type="hidden" name="combo_product_id[]"
                                                       value="{{$item['productable_id']}}"
                                                       class="primary_input_field2 sku_id{{$item['product_sku_id']}}">{{$item['product']}}
                                            </td>
                                            <td></td>
                                            <td class="@if (app('general_setting')->display_last_price_in_sales == 0) d-none @endif"></td>

                                            <td class="td_max_width d-none">
                                                <a href="javascript:void(0)" title="Serial Key Add" class="serial_key_add_btn" data-id="{{ $item['product_sku_id'] }}-combo"  data-target="#{{ $item['product_sku_id'] }}-combo">
                                                    <i class="ti-clipboard mr-2"></i>{{ trans('common.Serial Key') }}
                                                </a>
                                                <div class="modal fade admin-query" id="{{ $item['product_sku_id'] }}-combo">
                                                    <div class="modal-dialog modal_1000px modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title"></h4>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <i class="ti-close "></i>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body product_detail_modal">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <select multiple="multiple" class="multypol_check_select active position-relative sale_type" id="combo_serial_no" name="combo_serial_no[]" multiple>
                                                                            {{-- @if ($part_numbers_all_list->count() > 0)
                                                                                @foreach ($part_numbers_all_list as $key => $part_number)
                                                                                    <option value="{{ $part_number->id }}">{{ $part_number->seiral_no }}</option>
                                                                                @endforeach
                                                                            @endif --}}
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <div class="d-flex justify-content-center pt_20">
                                                                            <button type="button" class="primary-btn radius_30px fix-gr-bg" data-dismiss="modal"><i class="ti-check"></i>{{ __("base::base.save") }}</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <input class="min_sell_qty_combo{{$item['product_sku_id']}}" type="hidden" value="0" name="min_sell_qty[]">
                                            <td>
                                                <input type="number" data-type="combo"
                                                       name="combo_product_quantity[]"
                                                       value="{{$item['quantity']}}"
                                                       onkeyup="addQuantity({{$type}})"
                                                       class="primary_input_field2 quantity quantity_combo{{$item['product_sku_id']}}">
                                            </td>

                                            <td><input
                                                    min="{{$item['price']}}"
                                                    step="0.01"
                                                    onkeyup="priceCalc({{$type}})"
                                                    class="primary_input_field2 product_price product_price_combo{{$item['product_sku_id']}}"
                                                    type="number"
                                                    value="{{$item['price']}}"
                                                    name="combo_product_price[]"></td>
                                            <td>
                                                <input type="number" name="product_discount[]" value="0" onkeyup="addDiscount({{$type}})" class="primary_input_field2 discount discount_sku{{$item['product_sku_id']}}">
                                            </td>
                                            <td></td>
                                            <td class="product_subtotal product_subtotal_combo{{$item['product_sku_id']}}"> {{ str_replace(',','',number_format($item['sub_total'],2))}} </td>
                                            <td class="text-center"><a
                                                    data-id=" {{$item['product_sku_id']}} "
                                                    data-product="{{$item['product_sku_id']}}-combo"
                                                    class="delete_product primary-btn"
                                                    href="javascript:void(0)"><i class="fas fa-trash-alt required_mark2 f_s_13"></i></a></td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endisset
                        </tbody>
                        <tfoot style="background:#ECEEF4" >
                            <tr>
                                <th>{{ trans("common.Total Amount") }}</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="@if (app('general_setting')->display_last_price_in_sales == 0) d-none @endif"></th>
                                <th class="d-none"></th>
                                <th></th>
                                <th></th>
                                <th class="total_amount_tr p-0">{{single_price(0)}}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" name="app_base_url" id="app_base_url" value="{{ URL::to('/') }}">
        <div class="pos_order_bottom">
            <div class="pos_order_left">
                @if (file_exists(base_path().'/Modules/Installment/'))
                    @if (app('installment_config')['enable'] == 1)
                        <div class="pos_order_left_upper d-flex justify-content-between align-items-end installment_mod_div hide_element">
                            <div class="installment_div">
                                <ul class="permission_list m-0">
                                    <li class="m-0 pb_15">
                                        <label data-id="Installments" class="primary_checkbox d-flex mr-12 ">
                                            <input name="installment_status" id="installment_status" type="checkbox">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{ __('installment.INSTALLMENT') }}</p>
                                    </li>
                                </ul>
                            </div>
                            <div class="primary_input down_payment_div d-none">
                                <label class="primary_input_label" for="">{{ __('installment.Down Payment') }} (%)</label>
                                <input type="number" name="down_payment" min="{{ app('installment_config')['down_payment'] }}" step="0.01" max="100" class="primary_input_field down_payment" value="{{ app('installment_config')['down_payment'] }}">
                            </div>
                        </div>
                        <!-- pos_order_left_upper::start -->
                        <div class="pos_order_left_upper mt-2 installment_rld_div d-none">
                            @foreach (app('installment_config')['total_emi'] as $key => $status)
                                @if ($status == "1")
                                    <ul class="permission_list m-0">
                                        <li>
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="emi" type="radio" id="{{ $key }}" value="{{ $key }}" checked>
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ strtoupper(str_replace("_"," ",$key)) }}</p>
                                        </li>
                                    </ul>
                                @endif
                            @endforeach
                        </div>
                        <hr class="mb-3">
                    @endif
                @endif
                <div class="pos_order_left_upper  justify-content-between align-items-end">
                    <div class="primary_input flex-fill">
                        <label class="primary_input_label" for="">{{__('purchase.Order Tax')}}</label>
                        <select onchange="addTotalVat()" class="primary_select total_vat wide" name="vat">
                            <option value="0-0">{{__('sale.No Tax')}}</option>
                            @foreach($taxes as $tax)
                                <option value="{{$tax->rate}}-{{ $tax->id }}" {{isset($pos) && $pos->vat == $tax->rate ? 'selected' : '' }}>{{$tax->rate}} % {{$tax->name}} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="primary_input">
                        <label class="primary_input_label" for="">{{__('purchase.Order Tax')}} <small>({{ app('general_setting')->currency_symbol}})</small></label>
                        <input type="text" class="primary_input_field other_product_tax other_product_tax_input" net-sub-total="0"
                           value="{{ isset($otherTaxTotal) ? $otherTaxTotal : 0 }}" name="other_item_amount" readonly="readonly">
                    </div>
                    @if (app('general_setting')->enable_gst == 1)
                        <div class="primary_input flex-fill">
                            <label class="primary_input_label" for="">{{__('core::core.gst')}}</label>
                            <select class="primary_select gst_group wide" id="gst_group" name="gst_group">
                                <option value="igst">{{ __('core::core.igst') }}</option>
                                <option value="cgst">{{ __('core::core.cgst') }}</option>
                                <option value="sgst">{{ __('core::core.sgst') }}</option>
                                <option value="cess">{{ __('core::core.cess') }}</option>
                            </select>
                        </div>
                    @endif

                    <div class="primary_input">
                        <label class="primary_input_label" for=""> @if (app('general_setting')->enable_gst == 1) {{__('core::core.gst')}} @endif {{__('setup.Tax')}} <small>({{ app('general_setting')->currency_symbol}})</small></label>
                        <input type="text" class="primary_input_field product_tax product_tax_input" net-sub-total="0"
                        value="{{ isset($productTaxTotal) ? $productTaxTotal : 0 }}" name="item_amount" readonly="readonly">
                    </div>
                </div>
            <!-- pos_order_left_upper::start -->
                <div class="pos_order_left_upper mt-2">
                    <div class="primary_input">
                        <label class="primary_input_label" for="">{{__('purchase.Shipping Charge')}}</label>
                        <input type="number" name="shipping_charge" placeholder="00" class="primary_input_field shipping_charge" onkeyup="addShippingCharge()" value="0" step="0.01">
                    </div>
                    <div class="primary_input">
                        <label class="primary_input_label" for="">{{__('sale.Total Quantity')}}</label>
                        <input type="text" name="total_quantity"placeholder="00" class="primary_input_field total_quantity pos_billing" value="{{ $quantity}}" readonly>
                    </div>
                    <div class="primary_input">
                        <label class="primary_input_label" for="">{{__('sale.Per Product Dis.')}}</label>
                        <input type="text" name="total_discount_product" class="primary_input_field product_discount" value="" readonly>
                    </div>
                    <div class="primary_input">
                        <label class="primary_input_label" for="">{{__('sale.Discount')}} <small>({{app('general_setting')->currency_symbol}})</small></label>
                        <input type="number" onkeyup="addTotalDiscount()" name="total_discount" class="primary_input_field total_discount pos_billing" value="0">
                    </div>
                </div>
            <!-- pos_order_left_upper::end -->


            <!-- pos_order_left_bottom::start  -->
                <div class="pos_order_left_bottom">
                    <ul>
                        <li><a class="primary_color_btn2 radish_btn" onclick="approve_modal('{{route('home')}}')">{{__('sale.Cancel')}}</a></li>
                        <li><a class="primary_color_btn2 Maroon_btn draft_btn" onclick="makeitDraft()">{{ __('sale.Draft') }}</a></li>
                        <li><a class="primary_color_btn2 yellow_btn draft_list_btn" onclick="draftListModal()">{{ __('sale.Draft List') }}</a></li>
                        <li><a class="primary_color_btn2 radish_btn" data-toggle="modal" data-target="#noteModal">{{ __('sale.Note') }}</a></li>
                        <li><a class="primary_color_btn2 Maroon_btn due_btn" type="button">{{ __('sale.Due') }}</a></li>
                        @if(session()->has('carts') && count(session()->get('carts')) > 0)
                            <li><a href="{{route('clear.products')}}" class="primary_color_btn2 radish_btn">{{__('sale.Clear')}}</a></li>
                        @endif
                    </ul>
                </div>
                <!-- pos_order_left_bottom::end  -->
            </div>
        </div>
    </div>

    <input type="hidden" value="{{urlShortener()}}" data-id="{{isset($pos) ? $pos->id : ''}}" class="url">
    <input type="hidden" value="{{$price}}" class="total_amount" name="total_amount">
    <input type="hidden" value="" class="down_payment_money" name="down_payment_money">
    <input type="hidden" name="amounts" class="amount" value="">
    <input type="hidden" name="draft" id="draft" value="">
    <div id="multiple_payment_form"></div>

    <div class="modal fade admin-query" id="noteModal">
        <div class="modal-dialog modal_800px modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('sale.Note') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="ti-close "></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form method="POST" id="brandForm">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ __('sale.Note') }}</label>
                                    <input name="note" class="primary_input_field" type="text">
                                    <span class="text-danger" id="note"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 text-center">
                                <div class="d-flex justify-content-center pt_20">
                                    <button type="button" class="primary-btn semi_large2 fix-gr-bg" id="save_button_note"><i class="ti-check"></i>{{ __('common.Save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</form>
