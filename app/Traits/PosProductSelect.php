<?php

namespace App\Traits;

use Modules\Product\Entities\ComboProduct;
use Modules\Product\Entities\ProductSku;
use Modules\Sale\Entities\Sale;

trait PosProductSelect
{
    public function storeSkuProduct($id, $customer,$customer_type = null)
    {
        $last_price = '';
        $lp = 0;

        $productSku = $this->productRepository->findSku($id);

        if (app('general_setting')->display_last_price_in_sales == 0) {
            $last_price .= '<td style="text-align: right" class="last_price_td d-none"></td>';
        }
        else {
            $last_price .= '<td class="last_price_td">';

            if ($customer && $customer != 1) {

                if($customer_type == 'retailer'){
                    $sale = Sale::where('agent_user_id',$customer)->whereHas('items', function($q) use($id){
                        $q->where('productable_id',$id)->where('productable_type','Modules\Core\Entities\Product\ProductSku');
                    })->latest()->first();
                }else{
                    $sale = Sale::where('customer_id', $customer)->whereHas('items', function($q) use($id){
                        $q->where('productable_id',$id)->where('productable_type','Modules\Core\Entities\Product\ProductSku');
                    })->latest()->first();
                }

                if ($sale) {
                    $product_item = $sale->items->where('productable_id', $id)->where('productable_type', ProductSku::class)->first();
                    if ($product_item) {
                        $last_price .= '<a href="javascript:void(0)" data-toggle="modal" onclick="specificInvoiceDetail(' . $sale->id . ')"
                            class="invoice_link">' . $product_item->price . '</a>';
                    }
                }
            }
            $last_price .= '</td>';
        }
        $skus = session()->get('sku');
        $carts = session()->get('carts');
        $sku[$productSku->sku] = $productSku->sku;

        if (!empty($skus) || !empty($carts)) {
            if ((is_array($skus) && array_key_exists($productSku->sku, $skus)) || (is_array($carts) && array_key_exists('sku-' . $productSku->id, $carts))) {
                return 1;
            }
            if (is_array($skus))
                session()->put('sku', $sku + $skus);
        } else
            session()->put('sku', $sku);

        $variantName = $this->variationRepository->variantName($productSku);
        $option = '';
        $option .= '<option value="0" selected>Choose One</option>';
        foreach ($productSku->part_numbers->where('is_sold', 0) as $key => $part_number) {
            $option .= '<option value="'.$part_number->id.'">'.$part_number->seiral_no.'</option>';
        }
        if (app('general_setting')->origin == 1) {
            $origin =  '</br><small>' . $productSku->product->origin . '</small>';
        }else {
            $origin =  '';
        }
        $type = $productSku->id . ",'sku'";


        if (app('general_setting')->enable_gst == 1) {
            $price = $productSku->selling_price;
            $igstVal = $productSku->gst_tax_group->igst;
            $cgstVal = $productSku->gst_tax_group->cgst;
            $sgstVal = $productSku->gst_tax_group->sgst;
            $cessVal = $productSku->gst_tax_group->cess;
            $igstProduct = ($productSku->selling_price * $productSku->gst_tax_group->igst) / 100;
            $cgstProduct = ($productSku->selling_price * $productSku->gst_tax_group->cgst) / 100;
            $sgstProduct = ($productSku->selling_price * $productSku->gst_tax_group->sgst) / 100;
            $cessProduct = ($productSku->selling_price * $productSku->gst_tax_group->cess) / 100;
            $taxrow = '<input type="hidden" name="product_igst[]" net-sub-total="'.$igstProduct.'" value="' . $productSku->gst_tax_group->igst . '"class="primary_input_field igst igst_sku' . $productSku->id . '">
                        <input type="hidden" name="product_cgst[]" net-sub-total="'.$cgstProduct.'" value="' . $productSku->gst_tax_group->cgst . '" class="primary_input_field cgst cgst_sku' . $productSku->id . '">
                        <input type="hidden" name="product_sgst[]" net-sub-total="'.$sgstProduct.'" value="' . $productSku->gst_tax_group->sgst . '" class="primary_input_field sgst sgst_sku' . $productSku->id . '">
                        <input type="hidden" name="product_cess[]" net-sub-total="'.$cessProduct.'" value="' . $productSku->gst_tax_group->cess . '" class="primary_input_field cess cess_sku' . $productSku->id . '">';
        }else{
            // $price = $productSku->selling_price + ($productSku->selling_price * $productSku->tax) / 100;
            $price = $productSku->selling_price;
            $taxProduct = ($productSku->selling_price * $productSku->tax) / 100;
            $taxrow = '<input type="hidden" name="product_tax[]" net-sub-total="'.$taxProduct.'" value="' . $productSku->tax . '" onkeyup="addTax(' . $type . ')" class="primary_input_field2 tax tax_sku' . $productSku->id . '">';
        }

        $name = substr($productSku->product->product_name, 0, 40);
        $output = '';
        $output .= '<tr>

                        <input class="product_min_price product_min_price_sku'.$productSku->id.'" type="hidden" value="' . $productSku->min_selling_price . '">
                        <th class="nowrap" data-toggle="tooltip" data-placement="top" title="'.$lp.'"><input type="hidden" name="product_id[]" value="' . $productSku->id . '" class="primary_input_field sku_id' . $productSku->id . '">' . $name . $origin . '</br>' . $variantName . '</th>
                        <td>'.$productSku->sku.'</td>
                        '.$last_price.'

                        <td class="td_max_width d-none">
                        <a href="javascript:void(0)" title="Serial Key Add" class="serial_key_add_btn" data-id="' . $productSku->id . '-sku"  data-target="#' . $productSku->id . '-sku">
                            <i class="ti-clipboard mr-2"></i>'.trans('common.Serial Key').'
                        </a>
                        <div class="modal fade admin-query" id="' . $productSku->id . '-sku">
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
                                                <select multiple="multiple" class="multypol_check_select active position-relative sale_type" id="serial_no" name="serial_no[]" multiple>
                                                    '.$option.'
                                                </select>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="d-flex justify-content-center pt_20">
                                                    <button type="button" class="primary-btn radius_30px fix-gr-bg" data-dismiss="modal"><i class="ti-check"></i>'.__("base::base.save").'</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </td>
                        <td><input class="min_sell_qty_sku'.$productSku->id.'" type="hidden" value="0" name="min_sell_qty[]">
                            <input type="number" name="quantity[]" value="1" placeholder="-" onkeyup="addQuantity(' . $type . ')" class="primary_input_field2 quantity quantity_sku' . $productSku->id . '">
                        </td>
                        <td><input name="product_price[]" step="0.01" data-type="sku" data-sku-id="'.$productSku->id.'" data-product-price="' . $productSku->selling_price . '" min="' . $productSku->min_selling_price . '" onkeyup="priceCalc(' . $type . ')" class="primary_input_field2 product_price product_price_sku' . $productSku->id . '" type="number"
                        value="' . $price . '"></td>
                        <td><input name="product_tax_amount[]" step="0.001" data-type="sku" data-sku-id="'.$productSku->id.'" class="primary_input_field2 product_tax_amount product_tax_amount_sku' . $productSku->id . '" type="number" readonly value="0"></td>
                        <td>
                            <input type="number" name="product_discount[]" value="0" onkeyup="addDiscount(' . $type . ')" class="primary_input_field2 discount discount_sku' . $productSku->id . '">
                        </td>
                        '.$taxrow.'
                        <td class="product_subtotal product_subtotal_sku' . $productSku->id . '">' . str_replace(',','',number_format($price ,2)) . '</td>
                        <td>
                            <a data-id="' . $productSku->id . '" class="delete_product primary-btn" href="javascript:void(0)">
                                <i class="fas fa-trash-alt required_mark2 f_s_13"></i>
                            </a>
                        </td>
                    </tr>';

        if (app('general_setting')->origin == 1) {
            $product_origin =  $productSku->product->origin;
        }else {
            $product_origin =  '';
        }
        if (app('general_setting')->enable_gst == 0) {
            $cart['sku-' . $productSku->id] = [
                'product' => $name,
                'sku' => $productSku->sku,
                'product_origin' => $product_origin,
                'sub_total' => str_replace(',','',number_format($price,2)),
                'price' => str_replace(',','',number_format($price,2)),
                'only_price' => str_replace(',','',number_format($productSku->selling_price,2)),
                'selling_price' => str_replace(',','',number_format($price,2)),
                'min_selling_price' => $productSku->min_selling_price,
                'type' => 'sku',
                'product_sku_id' => $productSku->id,
                'quantity' => 1,
                'taxProduct' => str_replace(',','',number_format($taxProduct,2)),
                'taxSku' => $productSku->tax,
            ];
        }else{
            $cart['sku-' . $productSku->id] = [
                'product' => $name,
                'sku' => $productSku->sku,
                'product_origin' => $product_origin,
                'sub_total' => str_replace(',','',number_format($price,2)),
                'price' => str_replace(',','',number_format($price,2)),
                'only_price' => str_replace(',','',number_format($productSku->selling_price,2)),
                'selling_price' => str_replace(',','',number_format($price,2)),
                'min_selling_price' => $productSku->min_selling_price,
                'type' => 'sku',
                'product_sku_id' => $productSku->id,
                'quantity' => 1,
                'taxProduct' => str_replace(',','',number_format(0,2)),
                'igstProduct' => str_replace(',','',number_format($igstProduct,2)),
                'sgstProduct' => str_replace(',','',number_format($sgstProduct,2)),
                'cgstProduct' => str_replace(',','',number_format($cgstProduct,2)),
                'cessProduct' => str_replace(',','',number_format($cessProduct,2)),
                'taxSku' => $productSku->tax,
                'igstVal' => $igstVal,
                'cgstVal' => $cgstVal,
                'sgstVal' => $sgstVal,
                'cessVal' => $cessVal,
            ];
        }

        if (!empty($carts)) {

            session()->put('carts', $carts + $cart);
        } else
            session()->put('carts', $cart);


        return $output;
    }

    public function storeCombo($id, $customer)
    {
        $productCombo = $this->productRepository->findCombo($id);

        $last_price = '';

        if (app('general_setting')->display_last_price_in_sales == 0) {
            $last_price .= '<td style="text-align: right" class="last_price_td d-none"></td>';
        }
        else {
            $last_price .= '<td class="last_price_td">';

            if ($customer && $customer != 1) {
                $customer = $this->contactRepositories->find($customer);
                $sale = $customer->lastPosInvoice;
                if ($sale) {
                    $product_item = $sale->items->where('productable_id', $id)->where('productable_type', ComboProduct::class)->first();
                    if ($product_item) {
                        $last_price .= '<input name="last_price_td" class="primary_input_field product_price product_price_sku' . $productCombo->id . '" type="number"
                            value="' . $product_item->price . '" readonly>';
                    }
                }
            }
            $last_price .= '</td>';
        }
        $type = $productCombo->id . ",'combo'";
        $skus = session()->get('sku');
        $carts = session()->get('carts');

        $sku[$type] = $type;

        if (!empty($skus) || !empty($carts)) {
            if ((is_array($skus) && array_key_exists($type, $skus)) || (is_array($carts) && array_key_exists('combo-' . $productCombo->id, $carts))) {
                return 1;
            }
            if (is_array($skus))
                session()->put('sku', $sku + $skus);
        } else
            session()->put('sku', $sku);

        $variantName = $this->variationRepository->comboVariant($productCombo);
        if (app('general_setting')->enable_gst == 1) {
            $price = $productCombo->selling_price;
            $igstVal = $productCombo->gst_tax_group->igst;
            $cgstVal = $productCombo->gst_tax_group->cgst;
            $sgstVal = $productCombo->gst_tax_group->sgst;
            $cessVal = $productCombo->gst_tax_group->cess;
            $igstProduct = ($productCombo->selling_price * $productCombo->gst_tax_group->igst) / 100;
            $cgstProduct = ($productCombo->selling_price * $productCombo->gst_tax_group->cgst) / 100;
            $sgstProduct = ($productCombo->selling_price * $productCombo->gst_tax_group->sgst) / 100;
            $cessProduct = ($productCombo->selling_price * $productCombo->gst_tax_group->cess) / 100;
            $taxrow = '<input type="hidden" name="combo_product_igst[]" net-sub-total="'.$igstProduct.'" value="' . $productCombo->gst_tax_group->igst . '"class="primary_input_field igst igst_combo' . $productCombo->id . '">
                        <input type="hidden" name="combo_product_cgst[]" net-sub-total="'.$cgstProduct.'" value="' . $productCombo->gst_tax_group->cgst . '" class="primary_input_field cgst cgst_combo' . $productCombo->id . '">
                        <input type="hidden" name="combo_product_sgst[]" net-sub-total="'.$sgstProduct.'" value="' . $productCombo->gst_tax_group->sgst . '" class="primary_input_field sgst sgst_combo' . $productCombo->id . '">
                        <input type="hidden" name="combo_product_cess[]" net-sub-total="'.$cessProduct.'" value="' . $productCombo->gst_tax_group->cess . '" class="primary_input_field cess cess_combo' . $productCombo->id . '">';
        }else{
            $price = $productCombo->selling_price + ($productCombo->selling_price * $productCombo->tax) / 100;
            $taxProduct = ($productCombo->selling_price * $productCombo->tax) / 100;
            $taxrow = '<input type="hidden" name="combo_product_tax[]" value="0" onkeyup="addTax(' . $type . ')" class="primary_input_field2 tax tax_combo' . $productCombo->id . '">';
        }
        $name = substr($productCombo->name, 0, 40);
        $option = '';
        foreach ($productCombo->combo_products as $key => $combo_product_option) {
            foreach ($combo_product_option->productSku->part_numbers->where('is_sold', 0) as $key => $part_number) {
                $option .= '<option value="'.$productCombo->id.'-'.$part_number->id.'-'.$combo_product_option->product_sku_id.'">'.$part_number->seiral_no.'</option>';
            }
        }
        $output = '';

        $output .= '<tr>
                        <input class="product_min_price product_min_price_combo'.$productCombo->id.'" type="hidden" value="' . $productCombo->min_selling_price . '">
                        <th class="nowrap"><input type="hidden" name="combo_product_id[]" value="' . $productCombo->id . '" class="primary_input_field sku_id' . $productCombo->id . '">' . $name . '</br>' . $variantName . '</th>
                        <td></td>
                        <td class="td_max_width d-none">
                        <a href="javascript:void(0)" title="Serial Key Add" class="serial_key_add_btn" data-id="' . $productCombo->id . '-combo">
                            <i class="ti-clipboard mr-2"></i>'.trans('common.Serial Key').'
                        </a>
                        <div class="modal fade admin-query" id="' . $productCombo->id . '-combo">
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
                                                    '.$option.'
                                                </select>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="d-flex justify-content-center pt_20">
                                                    <button type="button" class="primary-btn radius_30px fix-gr-bg" data-dismiss="modal"><i class="ti-check"></i>'.__("base::base.save").'</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </td>
                        '.$taxrow.'
                        <td><input class="min_sell_qty_combo'.$productCombo->id.'" type="hidden" value="0" name="min_sell_qty[]">
                        <input type="number" data-type="combo" name="combo_product_quantity[]" value="1" onkeyup="addQuantity(' . $type . ')" class="primary_input_field2 quantity quantity_combo' . $productCombo->id . '">
                        </td>
                        <td><input type="text" min="' . $productCombo->min_selling_price . '" name="combo_product_price[]" data-type="combo" data-sku-id="'.$productCombo->id.'" data-product-price="' . $productCombo->price . '" class="primary_input_field2 product_price product_price_combo' . $productCombo->id . '" value="' . $productCombo->price . '"></td>
                        <input type="hidden" name="product_tax[]"  value="0" onkeyup="addTax(' . $type . ')" class="primary_input_field tax tax_combo' . $productCombo->id . '">

                        <td><input name="product_tax_amount[]" step="0.001" data-type="combo" data-sku-id="'.$productCombo->id.'" class="primary_input_field2 product_tax_amount product_tax_amount_combo' . $productCombo->id . '" type="number" readonly value="0"></td>
                        <td>
                            <input type="number" name="combo_product_discount[]" value="0" onkeyup="addDiscount(' . $type . ')" class="primary_input_field2 discount discount_combo' . $productCombo->id . '">
                        </td>
                        <td class="product_subtotal product_subtotal_combo' . $productCombo->id . '">' . str_replace(',','',number_format($productCombo->price ,2)) . '</td>
                        <td>
                            <a data-id="' . $productCombo->id . '" data-product="' . $productCombo->id . '-Combo" class="primary-btn delete_product new_delete_product" href="javascript:void(0)">
                                <i class="fas fa-trash-alt required_mark2 f_s_13"></i>
                            </a>
                        </td>
                    </tr>';

        return $output;
    }
}
