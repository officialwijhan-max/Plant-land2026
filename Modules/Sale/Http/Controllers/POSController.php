<?php

namespace Modules\Sale\Http\Controllers;

use App\Traits\Notification;
use App\Traits\PosProductSelect;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Contact\Repositories\ContactRepositoriesInterface;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Sale\Http\Requests\PosRequest;
use Modules\Sale\Repositories\PosOrderRepositoryInterface;
use Modules\Inventory\Repositories\StockTransferRepository;
use Modules\Product\Entities\ComboProduct;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSku;
use Modules\Sale\Repositories\SaleRepositoryInterface;
use Modules\Product\Repositories\BrandRepositoryInterface;
use Modules\Product\Repositories\CategoryRepositoryInterface;
use Modules\Product\Repositories\ProductRepositoryInterface;
use Modules\Product\Repositories\VariantRepositoryInterface;
use Modules\Setup\Repositories\IntroPrefixRepositoryInterface;
use Modules\Setup\Repositories\TaxRepositoryInterface;
use Modules\Product\Repositories\ModelTypeRepositoryInterface;
use Modules\Product\Repositories\UnitTypeRepositoryInterface;
use Modules\Setting\Repositories\GeneralSettingRepository;
use Modules\Account\Repositories\LeadgerRepository;
use Modules\Product\Entities\Variant;
use Modules\Product\Entities\VariantValues;
use Modules\Sale\Entities\Sale;

class POSController extends Controller
{
    use Notification,PosProductSelect;
    protected $productRepository, $contactRepositories, $couponRepository, $posOrderRepository, $categoryRepository, $brandRepository,$introPrefixRepository,
        $variationRepository,$taxRepository,$modelRepository,$unitTypeRepository,$saleRepository;

    public function __construct(ProductRepositoryInterface $productRepository, ContactRepositoriesInterface $contactRepositories, PosOrderRepositoryInterface $posOrderRepository,
                                CategoryRepositoryInterface $categoryRepository, BrandRepositoryInterface $brandRepository,VariantRepositoryInterface $variationRepository,
                                TaxRepositoryInterface $taxRepository,IntroPrefixRepositoryInterface $introPrefixRepository,ModelTypeRepositoryInterface $modelRepository,UnitTypeRepositoryInterface $unitTypeRepository,SaleRepositoryInterface $saleRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->productRepository = $productRepository;
        $this->contactRepositories = $contactRepositories;
        $this->posOrderRepository = $posOrderRepository;
        $this->categoryRepository = $categoryRepository;
        $this->brandRepository = $brandRepository;
        $this->variationRepository = $variationRepository;
        $this->taxRepository = $taxRepository;
        $this->introPrefixRepository = $introPrefixRepository;
        $this->modelRepository = $modelRepository;
        $this->unitTypeRepository = $unitTypeRepository;
        $this->saleRepository = $saleRepository;
    }

    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            $data['items'] = $this->posOrderRepository->withPaginate($row_count,$quick_search,$name,$sort,$column,'no_draft','all',['user:id,name', 'customer:id,name', 'agentuser:id,name', 'items:id,return_quantity,itemable_id,itemable_type','payments:id,payable_type,payable_id,amount'],['id','date','customer_id','agent_user_id','user_id','invoice_no','total_quantity','total_tax','return_status','status','is_approved','payable_amount']);
            if ($request->ajax()) {
                return view('sale::pos_order.paginate.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->posOrderRepository->withPaginate("all",$quick_search,$name,$sort,$column,'no_draft','all',['user:id,name', 'customer:id,name', 'agentuser:id,name', 'items:id,return_quantity,itemable_id,itemable_type','payments:id,payable_type,payable_id,amount'],['id','date','customer_id','agent_user_id','user_id','invoice_no','total_quantity','total_tax','return_status','status','is_approved','payable_amount']);
                if ($request->import_as == "print") {
                    \LogActivity::successLog(trans('common.Print has been Done').' - POS Sale List', route('pos-order.index'), "Print");
                    return view('sale::pos_order.paginate.print', $data);
                }
                if ($request->import_as == "csv") {
                    \LogActivity::successLog(trans('common.CSV Download has been done').' - POS Sale List', route('pos-order.index'), "CSV Download");
                    $this->posOrderRepository->csvDownload('2','no_draft','all', $data);
                    $filePath = public_path("uploads/csv/pos_sales_list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-pos_sales_list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('sale::pos_order.index', $data);
        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans("common.Something Went Wrong"));
            return back();
        }
    }

    public function draftList()
    {
        try{
            $orders = $this->posOrderRepository->all()->where('is_draft',1);
            return view('sale::pos_order.draft_list', compact('orders'));
        }catch (\Exception $e) {
            return 0;
        }
    }

    public function create()
    {
        try{
            $data = [
                'products' => $this->productRepository->allStockProduct(),
                'customers' => $this->contactRepositories->posCustomer(),
            ];
            return view('sale::pos_order.create')->with($data);
        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans("common.Something Went Wrong"));
            return back();
        }
    }

    public function store(PosRequest $request, SaleRepositoryInterface $saleRepository)
    {
        if ($request->customer_id == "customer-1" && $request->final_over_sure_amount < 0) {
            Toastr::error(trans("sale::sale.partial_type_payment_is_not_allowed_for_walk_in_customer"));
            return back();
        }
        DB::beginTransaction();
        try {
            if ($request->has('product_id')) {
                foreach ($request->product_id as $key => $product_sku_id) {
                    $stockTransferRepo = new StockTransferRepository();
                    $checkStock = $stockTransferRepo->checkQty($product_sku_id, session()->get('showroom_id'));
                    if ($checkStock != "pass") {
                        if ($request->quantity[$key] > $checkStock->stock ) {
                            Toastr::error(trans('sale.Your stock is out'), __('common.Error'));
                            return back();
                        }
                    }
                }
            }
            if ($request->has('combo_product_id')) {
                foreach ($request->combo_product_id as $key => $combo_id) {
                    $stockTransferRepo = new StockTransferRepository();
                    $checkStockCombo = $stockTransferRepo->checkQtyForCombo($combo_id, session()->get('showroom_id'));
                    if ($checkStockCombo != "pass") {
                        if($checkStockCombo == 0)
                        {
                            Toastr::error(trans('sale.Your stock is out'), __('common.Error'));
                            return back();
                        }
                    }
                }
            }

            if ($request->pos_id) {
                $saleRepository->delete($request->pos_id);
            }
            $sale = $this->posOrderRepository->create($request->except('_token'));
            if (is_numeric($sale)){
                DB::rollBack();
                Toastr::error(trans('sale.Your stock is out'), __('common.Error'));
                return back();
            }
            if ($request->draft){
                DB::commit();
                session()->forget('carts');
                \LogActivity::successLog('POS saved as Draft'.$sale->invoice_no, route('sale.show', $sale->id), $sale->invoice_no);
                Toastr::success(trans('sale::sale.successfully_saved'));
                return back();
            }
            if ($request->due != 1){
                $this->update($request, $sale->id);
            }
            else {
                $request->merge(['no_amounts' => '0.00']);
                $this->update($request, $sale->id);
            }
            if (file_exists(base_path().'/Modules/Installment/')) {
                if (app('installment_config')['enable'] == 1 && $request->installment_status == "on") {
                    $this->makeStoreInstallment($request, $sale->id);
                }
            }
            if (app('business_settings')->where('type', 'sale_approval')->first()->status == 1) {
                $this->statusChange($sale->id);
            }

            \LogActivity::successLog('POS has been done: '.$sale->invoice_no, route('sale.show', $sale->id), $sale->invoice_no);
            DB::commit();
            $paid_amount = $sale->payments()->where('payment_type','pay')->sum('amount') - $sale->payments()->where('payment_type','return')->sum('amount');
            $sale->update([
                'due_amount' => $sale->payable_amount - $paid_amount
            ]);
            session()->forget('carts');
            session()->put('sale', $sale);
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans("common.Something Went Wrong"));
            return back();
        }
    }

    public function show($id)
    {
        try{
            $sale = $this->saleRepository->find($id);

            if ($sale->customer_id) {
                $due = $sale->customer->accounts['due'];
            }else {
                $due = $sale->agentuser->accounts['due'];
            }
            $data = [
                'sale' => $sale,
                'due' => $due
            ];
            return view('sale::sale.show')->with($data);
        }catch (\Exception $e) {
            Toastr::error(trans("common.Something Went Wrong"));
            return back();
        }
    }

    public function update(Request $request, $id)
    {
        if ($request->quick_amounts == null && count((array) $request->payment_method) < 1) {
            Toastr::error(trans('sale.Please Select a Payment Method'), '!Error');
            return back();
        }
        try {
            $quick_payments = $bank_payments = $cash_payments = [];
            if ($request->quick_amounts) {
                $quick_payments['quick_cash'] = [
                    'payment_method' => 'quick cash',
                    'amount' => array_sum(explode(',', $request->quick_amounts)),
                ];
            }
            if ($request->payment_method) {
                for ($i = 0; $i < count($request->payment_method); $i++) {
                    $req_payment_method = explode('-', $request->payment_method[$i]);
                    if ($req_payment_method[0] == 'bank') {
                        $bank_methods[$i] = [
                            'payment_method' => $req_payment_method[0],
                            'account_id' => $req_payment_method[1],
                            'amount' => $request->amount[$i],
                            'discount_percent' => ($request->discount) ? $request->discount : 0,
                            'discount_amount' => ($request->discount_amount) ? $request->discount_amount : 0,
                        ];
                        $banks[] = $i;
                    } else {
                        $cash_payments[$i] = [
                            'payment_method' => $req_payment_method[0],
                            'amount' => $request->amount[$i],
                            'discount_percent' => ($request->discount) ? $request->discount : 0,
                            'discount_amount' => ($request->discount_amount) ? $request->discount_amount : 0,
                        ];
                    }
                }
            }
            if (isset($banks))
                foreach ($banks as $key => $bank) {
                    $bank_infos[$key] = [
                        'bank_name' => array_key_exists($key, $request->bank_name) ? $request->bank_name[$key] : '',
                        'branch' => array_key_exists($key, $request->branch) ? $request->branch[$key] : '',
                        'account_no' => array_key_exists($key, $request->account_no) ? $request->account_no[$key] : '',
                        'account_owner' => array_key_exists($key, $request->account_owner) ? $request->account_owner[$key] : '',
                    ];
                    $bank_payments[] = array_merge($bank_methods[$bank], $bank_infos[$key]);
                }

            $payments = array_merge($cash_payments, $bank_payments, $quick_payments);

            $this->saleRepository->payments($payments, $id, 1);
            DB::commit();
            return 1;
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return 0;
        }
    }

    public function products(Request $request)
    {
        try{
            session()->forget('sku');
            if (session()->get('showroom_id') != null) {
                $productList = $this->productRepository->lodeMoreProductList(session()->get('showroom_id'),ShowRoom::class,0,0,null,null,null,null);
            }else {
                Toastr::error(trans("sale::sale.your_session_has_been_expired_login_again"));
                return back();
            }

            $showrooms =ShowRoom::active()->latest()->get();
            $data = [
                'allProducts'       => count($productList),
                'single_skip'       => count($productList['single_skip']),
                'combo_skip'        => count($productList['combo_skip']),
                'products'          => $productList,
                'categories'        => $this->categoryRepository->all(),
                'brands'            => $this->brandRepository->forSelectIdName(),
                'customers'         => $this->contactRepositories->posCustomer(),
                'retailers'         => [],
                'taxes'             => $this->taxRepository->activeTax(),
                'models'            => $this->modelRepository->all(),
                'showrooms'         => $showrooms,
            ];

            return view('sale::pos_order.pos')->with($data);

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans("common.Something Went Wrong"));
            return back();
        }
    }

    public function getProductListForAddProductModal()
    {
        $data['productSkus']       = $this->productRepository->allProduct();
        return (string) view('sale::pos_order.components.select_product_list_for_add_product', $data);
    }

    //Ajax Request for Load Products in POS
    public function storeProduct(Request $request)
    {
        try{
            $last_price = '';
            $lpv  = 0;
            $productSku = $this->productRepository->findSku($request->id);
            $last_price .= '<td class="last_price_td">';
            $request_cutomer = explode('-', $request->customer);

            if ($request_cutomer[0] == 'customer' && $request_cutomer[1] != 1)
            {
                $customer = $this->contactRepositories->find($request_cutomer[1]);
                $id = $request->id;
                $sale = Sale::where('customer_id', $customer->id)->whereHas('items', function($q) use($id){
                    $q->where('productable_id',$id)->where('productable_type','Modules\Product\Entities\ProductSku');
                })->latest()->first();
                if ($sale) {

                    $product_item = $sale->items->where('productable_id',$request->id)->where('productable_type',ProductSku::class)->first();
                    if ($product_item) {
                        $last_price .= '<a href="javascript:void(0)" data-toggle="modal" onclick="specificInvoiceDetail(' . $sale->id . ')"
                            class="invoice_link">' . $product_item->price . '</a>';
                    }

                    $lpv = number_format($product_item->price ,2);
                }
            }
            $lp = 0;
            $last_price .= '</td>';
            $skus = session()->get('sku');
            $carts = session()->get('carts');

            $sku[$productSku->sku] = $productSku->sku;

            if (!empty($skus) || !empty($carts)) {
                if ((is_array($skus) && array_key_exists($productSku->sku, $skus)) || (is_array($carts) && array_key_exists('sku-'.$productSku->id, $carts))) {
                    return 1;
                }
                if (is_array($skus))
                    session()->put('sku', $sku + $skus);
            } else
                session()->put('sku', $sku);

            $variantName = $this->variationRepository->variantName($productSku);
            $option = '';
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
                $igstProduct = ((float)$productSku->selling_price * (float)$productSku->gst_tax_group->igst) / 100;
                $cgstProduct = ((float)$productSku->selling_price * (float)$productSku->gst_tax_group->cgst) / 100;
                $sgstProduct = ((float)$productSku->selling_price * (float)$productSku->gst_tax_group->sgst) / 100;
                $cessProduct = ((float)$productSku->selling_price * (float)$productSku->gst_tax_group->cess) / 100;
                $taxrow = '<input type="hidden" name="product_igst[]" net-sub-total="'.$igstProduct.'" value="' . $productSku->gst_tax_group->igst . '"class="primary_input_field igst igst_sku' . $productSku->id . '">
                            <input type="hidden" name="product_cgst[]" net-sub-total="'.$cgstProduct.'" value="' . $productSku->gst_tax_group->cgst . '" class="primary_input_field cgst cgst_sku' . $productSku->id . '">
                            <input type="hidden" name="product_sgst[]" net-sub-total="'.$sgstProduct.'" value="' . $productSku->gst_tax_group->sgst . '" class="primary_input_field sgst sgst_sku' . $productSku->id . '">
                            <input type="hidden" name="product_cess[]" net-sub-total="'.$cessProduct.'" value="' . $productSku->gst_tax_group->cess . '" class="primary_input_field cess cess_sku' . $productSku->id . '">';
            }else{
                $price = $productSku->selling_price;
                $taxProduct = ((float)$productSku->selling_price * (float)$productSku->tax) / 100;
                $taxrow = '<input type="hidden" name="product_tax[]" net-sub-total="'.$taxProduct.'" value="' . $productSku->tax . '" onkeyup="addTax(' . $type . ')" class="primary_input_field2 tax tax_sku' . $productSku->id . '">';
            }

            $name =  substr($productSku->product->product_name, 0, 40);
            $output = '';
            $output .= '<tr>
                            <input class="product_min_price_sku'.$productSku->id.'" type="hidden" value="' . $productSku->min_selling_price . '">
                            <th class="nowrap" data-toggle="tooltip" data-placement="top" title="'.$last_price.'"><input type="hidden" name="product_id[]" value="' . $productSku->id . '" class="primary_input_field sku_id' . $productSku->id . '">' . $name . $origin . '</br>' . $variantName . '</th>
                            <td>'.$productSku->sku.'</td>
                            '.$last_price.'

                            <td class="">
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
            return response()->json($output);

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation failed');
            return response()->json(['status' => trans("common.Something Went Wrong")]);
        }

    }

    //Ajax Request for load more products in POS
    public function loadProduct(Request $request)
    {
        $loadBtn = 1;
        $skip = $request->skip ?? 0;
        $combo_skip = $request->comboSkip ?? 0;

        $totalProduct = Product::count();

        $productList = $this->productRepository->lodeMoreProductList(session()->get('showroom_id'),'Modules\Inventory\Entities\ShowRoom',$skip,$combo_skip,$request->search_keyword,$request->category_id,$request->brand_id,$request->model_id,$totalProduct);

        $skip = $skip + count($productList['single_skip']);
        $combo_skip = $combo_skip + count($productList['combo_skip']);

        $totalPost = $totalProduct;

        if (count($productList['single_skip']) == 0 && count($productList['combo_skip'])) {
            $loadBtn = 0;
        }

        $output = '';

        if (app('general_setting')->pos_view == 1) {
            if (count($productList['ProductList']) > 0) {
                foreach ($productList['ProductList'] as $product) {

                    $output .= '<div class="grid_single_product product_info pointer" data-value="' .$product->product_id. '" data-id="' .$product->product_id. '-' . $product->product_type . '">
                                    <div class="product_thumb">
                                        <img src="' .asset($product->image_source). '" alt="' .$product->product_name. '">
                                    </div>
                                    <div class="product_content text-center">
                                       <h4 class="f_s_14 f_w_500 theme_text mb-1" >' .substr($product->product_name, 0, 40). '</h4>
                                    </div>
                                </div>';
                }
            } else
                $output .= '';
        }else {
            if (count($productList['ProductList']) > 0) {

                foreach ($productList['ProductList'] as $product) {

                    $stock_quantity =  $product->stock;

                    if (app('general_setting')->origin == 1) {
                        $output .= '<tr class="product_info" data-value="' .$product->product_id. '" data-id="' .$product->product_id. '-' . $product->product_type . '">
                                        <th>
                                            <div class="product_image">
                                                <div class="thumb">
                                                    <img src="' .asset($product->image_source). '" alt="">
                                                </div>
                                                <a href="javascript:void(0)">
                                                    <h4>' .substr($product->product_name, 0, 40). ' ('.$stock_quantity.')</h4>
                                                </a>
                                            </div>
                                        </th>
                                        <td>'.@$product->product_sku .'</td>
                                        <td>'.@$product->origin .'</td>
                                        <td>'.@$product->brand_name .'</td>
                                        <td>'.@$product->model_name .'</td>
                                    </tr>';
                    }else {
                        $output .= '<tr class="product_info" data-value="' .$product->product_id. '" data-id="' .$product->product_id. '-' . $product->product_type . '">
                                        <th>
                                            <div class="product_image">
                                                <div class="thumb">
                                                    <img src="' .asset($product->image_source). '" alt="">
                                                </div>
                                                <a href="javascript:void(0)">
                                                    <h4>' .substr($product->product_name, 0, 40).' ('.$stock_quantity.')</h4>
                                                </a>
                                            </div>
                                        </th>
                                        <td>'.@$product->product_sku .'</td>
                                        <td>'.@$product->brand_name .'</td>
                                        <td>'.@$product->model_name .'</td>
                                    </tr>';
                    }

                }
            } else
                $output .= '';
        }

        return \response()->json([
            'loadBtn' => $loadBtn,
            'single_skip' => $skip,
            'combo_skip' => $combo_skip,
            'products' => $output,
            'totalPost' => $totalPost,
        ]);

    }

    public function loadProductGrid(Request $request)
    {
        $loadBtn = 1;
        $skip = $request->skip ?? 0;

        $productList = $this->productRepository->productList(session()->get('showroom_id'),'Modules\Inventory\Entities\ShowRoom');

        $products = array_slice($productList, $skip,3);

        $skip = $skip + count($products);

        $totalProduct = Product::count();
        $totalCombo = ComboProduct::count();
        $totalPost = $totalProduct + $totalCombo;
        if (count($products) == 0) {
            $loadBtn = 0;
        }


        $output = '';

        if (count($products) > 0) {
            foreach ($products as $product) {

                if ($product->image_source)
                    $image =$product->image_source;
                else
                    $image = 'public/backEnd/img/no_image.png';

                $output .= '<div class="grid_single_product product_info pointer" data-value="' .$product->product_id. '" data-id="' .$product->product_id. '-' . $product->product_type . '">
                                <div class="product_thumb">
                                    <img src="' .asset($image). '" alt="' .$product->product_name. '">
                                </div>
                                <div class="product_content text-center">
                                   <h4 class="f_s_14 f_w_500 theme_text mb-1" >' .substr($product->product_name, 0, 40). '</h4>
                                </div>
                            </div>';
            }
        } else
            $output .= '';

        return \response()->json([
            'loadBtn' => $loadBtn,
            'skip' => $skip,
            'products' => $output,
            'totalPost' => $totalPost,
        ]);

    }

    public function multiple_payment_modal(Request $request)
    {
        $user_type = explode('-', $request->customer_id);
        $user = $user_type[0];
        $user_id = $user_type[1];
        $total_amount = $request->total_amount;
        $paying_amount = $request->paying_amount;
        $total_qty = $request->total_qty;
        $paid_amount = $request->paid_amount ?? 0;
        $bank_accounts = \Modules\Account\Entities\ChartAccount::where('configuration_group_id', 2)->get();
        $branch_acc = [];
        return view('sale::pos_order.payment_modal', compact('total_amount', 'paying_amount', 'bank_accounts', 'total_qty','paid_amount', 'user', 'user_id', 'branch_acc'));
    }

    public function cash_payment_modal(Request $request)
    {
        $customer = $this->contactRepositories->find($request->customer_id);
        $total_amount = $request->total_amount;
        $total_qty = $request->total_qty;
        $bank_accounts = null;
        return view('sale::pos_order.cash_payment_modal', compact('customer', 'total_amount', 'bank_accounts', 'total_qty'));
    }

    public function draftListProductInfo(Request $request)
    {
        $pos = $this->posOrderRepository->find($request->order_id);
        $output = '<tr class="d-none">
                    <td><input type="hidden" name="pos_id" value="'.$pos->id.'"></td>
                    </tr>';
        foreach ($pos->items as $item) {
            $v_name = [];
            $v_value = [];
            $p_name = [];
            $p_qty = [];
            $variantName = null;
            if ($item->productable->product && $item->productable->product_variation) {
                foreach (json_decode($item->productable->product_variation->variant_id) as $key => $value) {
                    array_push($v_name , Variant::find($value)->name);
                }
                foreach (json_decode($item->productable->product_variation->variant_value_id) as $key => $value) {
                    array_push($v_value , VariantValues::find($value)->value);
                }

                for ($i=0; $i < count($v_name); $i++) {
                    $variantName .= $v_name[$i] . ' : ' . $v_value[$i];
                }
            }else {
                if (is_array($item->productable->combo_products) || is_object($item->productable->combo_products)) {
                    foreach ($item->productable->combo_products as $c_product_detail) {
                        array_push($p_name , $c_product_detail->productSku->product->product_name);
                        array_push($p_qty , $c_product_detail->product_qty);
                        if ($c_product_detail->productSku->product_variation) {
                            foreach (json_decode($c_product_detail->productSku->product_variation->variant_id) as $key => $value) {
                                array_push($v_name , Variant::find($value)->name);
                            }

                            foreach (json_decode($c_product_detail->productSku->product_variation->variant_value_id) as $key => $value) {
                                array_push($v_value , VariantValues::find($value)->value);
                            }
                        }
                    }

                    for ($i=0; $i < count($p_name); $i++) {
                        if (!empty($v_name[$i])) {
                            $variantName .= $p_name[$i] . ' -> qty : ('. $p_qty[$i] . ') Specification::' . $v_name[$i] . ' : ' . $v_value[$i] . '; </br>';
                        }else {
                            $variantName .= $p_name[$i] . ' -> qty : ('. $p_qty[$i] . ') ; </br>';
                        }
                    }
                }
            }
            if ($variantName) {
                $variantNameFormatted = ' <br> ('.   $variantName .')';
            }else {
                $variantNameFormatted = '';
            }


            if ($item->productable->product){
                $productSku = $item->productSku;
                $type = $productSku->id . ",'sku'";
                $option = '';
                foreach ($productSku->part_numbers->where('is_sold', 0) as $key => $part_number) {
                    $option .= '<option value="'.$part_number->id.'">'.$part_number->seiral_no.'</option>';
                }
                $name = substr($productSku->product->product_name, 0, 40);
                if (app('general_setting')->enable_gst == 1) {
                    $price = $productSku->selling_price;
                    $igstProduct = ((float)$productSku->selling_price * (float)$productSku->gst_tax_group->igst) / 100;
                    $cgstProduct = ((float)$productSku->selling_price * (float)$productSku->gst_tax_group->cgst) / 100;
                    $sgstProduct = ((float)$productSku->selling_price * (float)$productSku->gst_tax_group->sgst) / 100;
                    $cessProduct = ((float)$productSku->selling_price * (float)$productSku->gst_tax_group->cess) / 100;
                    $taxrow = '<input type="hidden" name="product_igst[]" net-sub-total="'.$igstProduct.'" value="' . $productSku->gst_tax_group->igst . '"class="primary_input_field igst igst_sku' . $productSku->id . '">
                                <input type="hidden" name="product_cgst[]" net-sub-total="'.$cgstProduct.'" value="' . $productSku->gst_tax_group->cgst . '" class="primary_input_field cgst cgst_sku' . $productSku->id . '">
                                <input type="hidden" name="product_sgst[]" net-sub-total="'.$sgstProduct.'" value="' . $productSku->gst_tax_group->sgst . '" class="primary_input_field sgst sgst_sku' . $productSku->id . '">
                                <input type="hidden" name="product_cess[]" net-sub-total="'.$cessProduct.'" value="' . $productSku->gst_tax_group->cess . '" class="primary_input_field cess cess_sku' . $productSku->id . '">';
                }else{
                    $price = $productSku->selling_price;
                    $taxProduct = ((float)$productSku->selling_price * (float)$productSku->tax) / 100;
                    $taxrow = '<input type="hidden" name="product_tax[]" net-sub-total="'.$taxProduct.'" value="' . $productSku->tax . '" onkeyup="addTax(' . $type . ')" class="primary_input_field2 tax tax_sku' . $productSku->id . '">';
                }

                $type = $item->product_sku_id.",'sku'" ;
                if (app('general_setting')->origin == 1) {
                    $origin =  '</br><small>' . $item->productSku->product->origin . '</small>';
                }else {
                    $origin =  '';
                }
                $output .= '<tr>
                                <input class="product_min_price_sku'.$productSku->id.'" type="hidden" value="' . $productSku->min_selling_price . '">
                                <th class="nowrap" data-toggle="tooltip" data-placement="top"><input type="hidden" name="product_id[]" value="' . $productSku->id . '" class="primary_input_field sku_id' . $productSku->id . '">' . $name . $origin . '</br>' . $variantName . '</th>
                                <td>'.$productSku->sku.'</td>
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
                                <td class="d-none></td>
                                <td><input class="min_sell_qty_sku'.$productSku->id.'" type="hidden" value="0" name="min_sell_qty[]">
                                    <input type="number" name="quantity[]" value="' .$item->quantity. '" placeholder="-" onkeyup="addQuantity(' . $type . ')" class="primary_input_field2 quantity quantity_sku' . $productSku->id . '">
                                </td>
                                <td><input name="product_price[]" data-type="sku" data-sku-id="'.$productSku->id.'" step="0.01" data-product-price="' . $productSku->selling_price . '" min="' . $productSku->min_selling_price . '" onkeyup="priceCalc(' . $type . ')" class="primary_input_field2 product_price product_price_sku' . $productSku->id . '" type="number"
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
            }else {
                $type = $item->product_sku_id.",'combo'" ;
                $productCombo = $item->productable;
                $option = '';
                foreach ($productCombo->combo_products as $key => $combo_product_option) {
                    foreach ($combo_product_option->productSku->part_numbers->where('is_sold', 0) as $key => $part_number) {
                        $option .= '<option value="'.$productCombo->id.'-'.$part_number->id.'-'.$combo_product_option->product_sku_id.'">'.$part_number->seiral_no.'</option>';
                    }
                }
                if (app('general_setting')->enable_gst == 1) {
                    $price = $productCombo->selling_price;
                    $igstVal = $productCombo->gst_tax_group->igst;
                    $cgstVal = $productCombo->gst_tax_group->cgst;
                    $sgstVal = $productCombo->gst_tax_group->sgst;
                    $cessVal = $productCombo->gst_tax_group->cess;
                    $igstProduct = ((float)$productCombo->selling_price * (float)$productCombo->gst_tax_group->igst) / 100;
                    $cgstProduct = ((float)$productCombo->selling_price * (float)$productCombo->gst_tax_group->cgst) / 100;
                    $sgstProduct = ((float)$productCombo->selling_price * (float)$productCombo->gst_tax_group->sgst) / 100;
                    $cessProduct = ((float)$productCombo->selling_price * (float)$productCombo->gst_tax_group->cess) / 100;
                    $taxrow = '<input type="hidden" name="combo_product_igst[]" net-sub-total="'.$igstProduct.'" value="' . $productCombo->gst_tax_group->igst . '"class="primary_input_field igst igst_combo' . $productCombo->id . '">
                                <input type="hidden" name="combo_product_cgst[]" net-sub-total="'.$cgstProduct.'" value="' . $productCombo->gst_tax_group->cgst . '" class="primary_input_field cgst cgst_combo' . $productCombo->id . '">
                                <input type="hidden" name="combo_product_sgst[]" net-sub-total="'.$sgstProduct.'" value="' . $productCombo->gst_tax_group->sgst . '" class="primary_input_field sgst sgst_combo' . $productCombo->id . '">
                                <input type="hidden" name="combo_product_cess[]" net-sub-total="'.$cessProduct.'" value="' . $productCombo->gst_tax_group->cess . '" class="primary_input_field cess cess_combo' . $productCombo->id . '">';
                }else{
                    $price = $productCombo->selling_price;
                    $taxProduct = ((float)$productCombo->selling_price * (float)$productCombo->tax) / 100;
                    $taxrow = '<input type="hidden" name="product_tax[]" net-sub-total="'.$taxProduct.'" value="' . $productCombo->tax . '" onkeyup="addTax(' . $type . ')" class="primary_input_field2 tax tax_combo' . $productCombo->id . '">';
                }
                $output .= '<tr>
                                <td><input type="hidden" name="combo_product_id[]"
                                           value="' .$item->productable_id. '"
                                           class="primary_input_field2 sku_id' .$item->product_sku_id. '">' .$item->productable->name. ' </br>'. $variantNameFormatted.'
                                </td>
                                <td></td>
                                <td class="d-none>
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
                                <td>
                                    <input type="number" data-type="combo" name="combo_product_quantity[]"
                                           value="' .$item->quantity. '"
                                           onkeyup="addQuantity(' .$type. ')"
                                           class="primary_input_field2 quantity quantity_combo' .$item->product_sku_id. '">
                                </td>
                                <td><input min="' .$item->productSku->purchase_price. '" step="0.01"
                                           onkeyup="priceCalc(' .$type. ')"
                                           data-type="combo" data-sku-id="'.$productCombo->id.'" data-product-price="' . $productCombo->price . '"
                                           class="primary_input_field2 product_price product_price_combo' .$item->product_sku_id. '"
                                           type="number"
                                           value="' .$item->price. '" name="combo_product_price[]"></td>
                                           <td><input name="product_tax_amount[]" step="0.001" data-type="combo" data-sku-id="'.$item->product_sku_id.'" class="primary_input_field2 product_tax_amount product_tax_amount_combo' . $item->product_sku_id . '" type="number" readonly value="0"></td>
                               <td>
                                   <input type="number" name="combo_product_discount[]" value="0" onkeyup="addDiscount(' . $type . ')" class="primary_input_field2 discount discount_combo' . $item->product_sku_id . '">
                               </td>
                                <td class="product_subtotal product_subtotal_combo' .$item->product_sku_id. '"> ' . str_replace(',','',number_format($item->sub_total ,2)) . ' </td>
                                <td><a data-id="' .$item->id. '" data-product="' .$item->id. '-combo"
                                       class="delete_product primary-btn" href="javascript:void(0)"><i class="fas fa-trash-alt required_mark2 f_s_13"></i></a></td>
                            </tr>';
            }
        }
        return response()->json($output);
    }

    public function getPdf($id)
    {
        try{

            $data = [
                'sale' => $this->posOrderRepository->find($id),
            ];
            return view('sale::pos_order.pdf')->with($data);
        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation failed');
            return back();
        }
    }

    //Ajax Product Modal Render for Products
    public function product_modal_for_select(Request $request)
    {
        try {
            $type = explode('-', $request->id);
            $xustomer_info = explode('-', $request->customer);
            if ($type[1] == "Combo") {
                $data['product_id'] =$this->storeCombo($type[0],$xustomer_info[1]);
                $data['product_type'] = $type[1];
            } else {
                $data['product_id'] =$this->storeSkuProduct($type[0],$xustomer_info[1],$xustomer_info[0],);
                $data['product_type'] = $type[1];
            }
            return $data;
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return false;
        }
    }

    public function clearProducts()
    {
        session()->forget('sku');
        session()->forget('carts');
        return redirect()->route('pos-order.products');
    }

    public function get_change_pos_view(Request $request)
    {
        try {
            $generalRepo = new GeneralSettingRepository;
            $generalRepo->update($request->except("_token"));
            return 1;
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return 0;
        }
    }

    public function statusChange($id)
    {
        DB::beginTransaction();
        try {
            $sale = $this->posOrderRepository->approval($id);
            if (!is_object($sale)) {
                Toastr::error(trans("sale::sale.Stock is Less than your input qty !!! Please Delete this order !!!"));
                return back();
            }
            DB::commit();
            $created_by = auth()->user()->name;
            $content = 'A POS has been created by ' . $created_by . ' which Invoice No. is <a href="' . route("sale.show", $sale->id) . '">' . $sale->invoice_no . '</a> for this you have to pay total of ' . $sale->payable_amount . '';
            $message = 'A POS has been created by ' . $created_by . ', Invoice No: ' . $sale->invoice_no . ', Amount: ' . single_price($sale->payable_amount) . '';
            if ($sale->customer_id != null) {
                $number = $sale->customer->mobile;
                $this->sendNotification($sale, $sale->customer->email, 'POS Reminder', $content, $number, $message,null,null,route('sale.show',$sale->id));
            } else {
                $number = $sale->agentuser->phone;
                $this->sendNotification($sale, $sale->agentuser->email, 'POS Reminder', $content, $number, $message,null,null,route('sale.show',$sale->id));
            }
            $paid_amount = $sale->payments()->where('payment_type','pay')->sum('amount') - $sale->payments()->where('payment_type','return')->sum('amount');
            $sale->update([
                'due_amount' => $sale->payable_amount - $paid_amount
            ]);
            \LogActivity::successLog('Sale has been approved : '.$sale->invoice_no, route('sale.show', $sale->id), $sale->invoice_no);
            Toastr::success(__('sale::sale.status_has_been_changed_successfully'));
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans("common.Something Went Wrong"));
            return back();
        }
    }

    public function approve_all_sales(Request $request)
    {
        try {
            if (!empty($request->sale_ids)) {
                foreach ($request->sale_ids as $key => $sale_id) {
                    $pos = $this->saleRepository->find($sale_id);
                    if ($pos->is_approved == 0) {
                        DB::beginTransaction();
                        $this->posOrderRepository->approval($sale_id);
                        \LogActivity::successLog('Sale has been approved : '.$pos->invoice_no, route('sale.show', $pos->id), $pos->invoice_no);
                        DB::commit();
                        $created_by = auth()->user()->name;
                        $content = 'A POS has been created by ' . $created_by . ' which Invoice No. is <a href="' . route("sale.show", $pos->id) . '">' . $pos->invoice_no . '</a> for this you have to pay total of ' . single_price($pos->payable_amount) . '';
                        $message = 'A POS has been created by ' . $created_by . ', Invoice No: ' . $pos->invoice_no . ', Amount: ' . single_price($pos->payable_amount) . '';
                        if ($pos->customer_id != null) {
                            $number = $pos->customer->mobile;
                            $this->sendNotification($pos, $pos->customer->email, 'Sale Create Reminder', $content, $number, $message,null,null,route('sale.show',$pos->id));
                        } else {
                            $number = $pos->agentuser->phone;
                            $this->sendNotification($pos, $pos->agentuser->email, 'Sale Create Reminder', $content, $number, $message,null,null,route('sale.show',$pos->id));
                        }
                    }
                }
                Toastr::success(trans('sale::sale.successfully_approved_all_pending_sales'));
                return back();
            }
            else {
                Toastr::warning(trans('sale::sale.select_your_order_to_approve'));
                return back();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans("common.Something Went Wrong"));
            return back();
        }
    }

}
