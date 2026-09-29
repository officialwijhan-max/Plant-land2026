<?php

namespace Modules\Product\Http\Controllers;

use PDF;
use App\User;
use App\Traits\PdfGenerate;
use App\Traits\Notification;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Product\Entities\PartNumber;
use Illuminate\Contracts\Support\Renderable;
use Modules\Product\Entities\ProductSku;
use Modules\Product\Http\Requests\ProductFormRequest;
use Modules\Product\Entities\ProductSellingPriceHistory;
use Modules\Product\Repositories\BrandRepositoryInterface;
use Modules\Product\Http\Requests\ProductUpdateFormRequest;
use Modules\Product\Repositories\ProductRepositoryInterface;
use Modules\Product\Repositories\VariantRepositoryInterface;
use Modules\Product\Repositories\CategoryRepositoryInterface;
use Modules\Product\Repositories\UnitTypeRepositoryInterface;
use Modules\Product\Repositories\ModelTypeRepositoryInterface;
use Modules\Inventory\Repositories\ShowRoomRepositoryInterface;
use Modules\Inventory\Repositories\WareHouseRepositoryInterface;
use Modules\Inventory\Repositories\StockTransferRepositoryInterface;

class ProductController extends Controller
{
    use PdfGenerate,Notification;

    protected $modelRepository, $unitTypeRepository, $brandRepository, $categoryRepository, $variationRepository, $productRepository,$wareHouseRepository,
        $showRoomRepository,$stockTransferRepository;

    public function __construct(ModelTypeRepositoryInterface $modelRepository, UnitTypeRepositoryInterface $unitTypeRepository, BrandRepositoryInterface $brandRepository,
                                CategoryRepositoryInterface $categoryRepository, VariantRepositoryInterface $variationRepository, ProductRepositoryInterface $productRepository,
                                WareHouseRepositoryInterface $wareHouseRepository, ShowRoomRepositoryInterface $showRoomRepository,StockTransferRepositoryInterface $stockTransferRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->modelRepository = $modelRepository;
        $this->unitTypeRepository = $unitTypeRepository;
        $this->brandRepository = $brandRepository;
        $this->categoryRepository = $categoryRepository;
        $this->variationRepository = $variationRepository;
        $this->productRepository = $productRepository;
        $this->wareHouseRepository = $wareHouseRepository;
        $this->showRoomRepository = $showRoomRepository;
        $this->stockTransferRepository = $stockTransferRepository;
    }

    public function index()
    {
        try{;
            $variants = $this->variationRepository->all()->where('status', 1);

            return view('product::product.add_product', [
                "variants" => $variants,
                "barcodes" => $this->productRepository->allBarcode(),
            ]);
        }catch(\Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return redirect()->back();
        }
    }

    public function category_wise_subcategory($category)
    {
        return $this->categoryRepository->subcategory($category);
    }

    public function variation_list($variant)
    {
        return $this->variationRepository->variantValues($variant);
    }

    public function variant_with_values($variant)
    {
        return $this->variationRepository->variantWithValues($variant);
    }

    public function create(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;

            if ($request->has('type') && $request->type == "product" && $request->ajax()) {
                $data['items'] = $this->productRepository->withPaginate($row_count, $quick_search, $name, $sort, $column, ["product:id,product_name,origin,image_source,category_id,product_type,brand_id,model_id,unit_type_id", "product.category:id,name", "product.unit_type:id,name", "product.brand:id,name", "product.model:id,name", 'stock:id,product_sku_id,houseable_id,houseable_type,stock', 'item', 'item.itemable'], ['id', 'product_id', 'sku', 'purchase_price', 'selling_price', 'min_selling_price', 'alert_quantity']);
                return view('product::product.paginate_component.paginate_product_list', $data);
            }
            if ($request->has('type') && $request->type == "combo_product" && $request->ajax()) {
                $data['combo_items'] = $this->productRepository->withPaginateCombo($row_count, $quick_search, $name, $sort, $column, ["combo_products", "combo_products.productSku:id,product_id,sku", "combo_products.productSku.product:id,product_name,image_source,category_id,product_type,brand_id,model_id", "combo_products.productSku.product.category:id,name", "combo_products.productSku.product.brand:id,name", "combo_products.productSku.product.model:id,name"], ['id', 'name', 'price', 'image_source', 'total_purchase_price', 'total_regular_price', 'min_selling_price', 'description', 'status']);
                return view('product::product.paginate_component.paginate_combo_product_list', $data);
            }

            if ($request->has("import_as") && $request->has('type') && $request->type == "product") {
                set_time_limit(-1);
                $data['items'] = $this->productRepository->withPaginate("all", $quick_search, $name, $sort, $column, ["product:id,product_name,origin,image_source,category_id,product_type,brand_id,model_id,unit_type_id", "product.category:id,name", "product.unit_type:id,name", "product.brand:id,name", "product.model:id,name", 'stock:id,product_sku_id,houseable_id,houseable_type,stock', 'item', 'item.itemable'], ['id', 'product_id', 'sku', 'purchase_price', 'selling_price', 'min_selling_price', 'alert_quantity']);
                if ($request->import_as == "print") {
                    return view('product::product.paginate_component.product_print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->productRepository->csvDownloadProduct($data);
                    $filePath = public_path("uploads/csv/product-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-product-list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            if ($request->has("import_as") && $request->has('type') && $request->type == "combo_product") {
                set_time_limit(-1);
                $data['combo_items'] = $this->productRepository->withPaginateCombo("all", $quick_search, $name, $sort, $column, ["combo_products", "combo_products.productSku:id,product_id,sku", "combo_products.productSku.product:id,product_name,image_source,category_id,product_type,brand_id,model_id", "combo_products.productSku.product.category:id,name", "combo_products.productSku.product.brand:id,name", "combo_products.productSku.product.model:id,name"], ['id', 'name', 'price', 'image_source', 'total_purchase_price', 'total_regular_price', 'min_selling_price', 'description', 'status']);
                if ($request->import_as == "print") {
                    return view('product::product.paginate_component.combo_print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->productRepository->csvDownloadCombo($data);
                    $filePath = public_path("uploads/csv/combo-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-combo-list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }

            $data['items'] = $this->productRepository->withPaginate($row_count, $quick_search, $name, $sort, $column, ["product:id,product_name,origin,image_source,category_id,product_type,brand_id,model_id,unit_type_id", "product.category:id,name", "product.unit_type:id,name", "product.brand:id,name", "product.model:id,name", 'stock:id,product_sku_id,houseable_id,houseable_type,stock', 'item', 'item.itemable'], ['id', 'product_id', 'sku', 'purchase_price', 'selling_price', 'min_selling_price', 'alert_quantity']);
            $data['combo_items'] = $this->productRepository->withPaginateCombo($row_count, $quick_search, $name, $sort, $column, ["combo_products", "combo_products.productSku:id,product_id,sku", "combo_products.productSku.product:id,product_name,image_source,category_id,product_type,brand_id,model_id", "combo_products.productSku.product.category:id,name", "combo_products.productSku.product.brand:id,name", "combo_products.productSku.product.model:id,name"], ['id', 'name', 'price', 'image_source', 'total_purchase_price', 'total_regular_price', 'min_selling_price', 'description', 'status']);

            return view('product::product.list_products', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return redirect()->back();
        }
    }

    public function service(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $data['items'] = $this->productRepository->withPaginateService($row_count, $quick_search, $sort, $column,["product:id,image_source,product_name,origin"]);
            if ($request->ajax()) {
                return view('product::product.paginate_component.service_list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->productRepository->withPaginateService($row_count, $quick_search, $sort, $column,["product:id,image_source,product_name,origin"]);
                if ($request->import_as == "print") {
                    return view('product::product.paginate_component.service_print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->productRepository->csvDownloadService($data);
                    $filePath = public_path("uploads/csv/service-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-service-list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            };
            return view('product::product.list_service', $data);
        }catch(\Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return redirect()->back();
        }
    }

    public function serial_key_index($id)
    {
        try{
            $items = PartNumber::where('product_sku_id', $id)->get();
            $product = $this->productRepository->findSku($id);
            return view('product::product.serial_key', [
                "items" => $items,
                "product" => $product
            ]);
        }catch(\Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return redirect()->back();
        }
    }

    public function print_label_index(Request $request)
    {
        try {
            $data['showrooms'] = $this->showRoomRepository->all();
            $data['wareHouses'] = $this->wareHouseRepository->all();
            $data['brandList'] = $this->brandRepository->forSelectIdName();
            $data['modelList'] = $this->modelRepository->forSelectIdName();
            return view("product::print_labels.labels", $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return $e->getMessage();
        }
    }

    public function product_detail_json(Request $request)
    {
        try {
            $sku = $this->productRepository->findSku(explode('-',$request->id)[0]);
            $output = '';
            $output .= '<tr id="parent_tr">
                            <td class="product_name">' . $sku->product->product_name . '<input type="hidden" name="sku_ids[]" value="' . $sku->id . '" ></td>
                            <td class="product_sku">' . $sku->sku . '</td>
                            <td>
                                <input class="primary_input_field no_of_label" name="label[]" type="number" min="0" value="0">
                            </td>
                            <td><a class="primary-btn primary-circle fix-gr-bg delete_product" href="javascript:void(0)"><i class="ti-trash"></i></a></td>
                        </tr>';
            return response()->json($output);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["error" => $e->getMessage()], 503);
        }
    }

    public function printLabels(Request $request)
    {

        if ($request->sku_ids == null) {
            Toastr::error(__('common.Select At least One Product'));
            return back();
        }

        $price = 0;
        $sku_id = explode('-', $request->product_sku_id)[0];



        $skus = ProductSku::whereIn('id', $request->sku_ids)->with(['product'])->get();
        $data['barcode_label_product_name'] = ($request->name == "on") ? 1 : 0;
        $data['barcode_label_product_name_font_size'] = ($request->name == "on") ? $request->product_name_font_size : 10;
        $data['barcode_label_variation'] = ($request->variation == "on") ? 1 : 0;
        $data['barcode_label_variation_font_size'] = ($request->variation == "on") ? $request->variant_font_size : 10;
        $data['barcode_label_product_price'] = ($request->product_price == "on") ? 1 : 0;
        $data['barcode_label_product_price_font_size'] = ($request->product_price == "on") ? $request->price_font_size : 10;
        $data['barcode_label_business_name'] = ($request->business_name == "on") ? 1 : 0;
        $data['barcode_label_business_name_font_size'] = ($request->business_name == "on") ? $request->business_name_font_size : 10;

        $data['product_name'] = $skus[0]->product->product_name;
        $data['skus'] = $skus;
        $data['tax_option'] = $request->tax_option;
        $data['name'] = $request->name;
        $data['variation'] = $request->variation;
        $data['business'] = $request->business_name;
        $data['labels'] = $request->label;
        $data['page'] = $request->page;
        $data['max_width'] = $request->max_width;
        $data['height'] = $request->height;

        \LogActivity::successLog('Label Printed for - ' . $skus->pluck('sku'), route('print_label_generate'), 'Print Label');
        return view('product::print_labels.labels_print', $data);
    }

    public function store(ProductFormRequest $request)
    {

        DB::beginTransaction();
        try {
            $product = $this->productRepository->create($request->except("_token"));

            $users=User::whereIn('role_id',[1,2])
                    ->where('id','!=',auth()->user()->id)->where('is_active','1')
                    ->get(['id','role_id']);

            $subject = $request->product_name;
            $class = $product;
            $data = __('notification.A Product Has been Created');
            $url = route('add_product.create');

            $this->sendNotification($class,null,$subject,null,null,$data,$users,$role_id=null,$url);



            DB::commit();
            \LogActivity::successLog('New Product - ('.$request->product_name.') has been created.');
            if ($request->ajax()) {
                return response()->json(['message' => __('product.Product has been added Successfully'), 'goto' => route('add_product.index')]);
            }
            else{
                Toastr::success(__('product.Product has been added Successfully'));
                return back();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Product creation');
            if ($request->ajax()) {
                return $e;
            }
            else{
                Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
                return back();
            }

        }
    }

    public function show($id)
    {
        try {
            $productCombo = $this->productRepository->findCombo($id);
            return view('product::product.edit_combo_product', [
                "productCombo" => $productCombo,
                "barcodes" => $this->productRepository->allBarcode(),
            ]);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["error" => $e->getMessage()], 503);
        }
    }

    public function edit($id)
    {
        try{
            $product = $this->productRepository->find($id);
            $product_variant_type = json_decode(collect($product->variations)->pluck("variant_id")->first());
            $variants = $this->variationRepository->all();
            $variant_values = $variants->pluck("values")->flatten()->toArray();
            return view('product::product.edit_product', [
                "product" => $product,
                "variants" => $variants,
                "variant_values" => $variant_values,
                "product_variant_type" => $product_variant_type,
                "barcodes" => $this->productRepository->allBarcode(),
            ]);
        }catch(\Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return redirect()->back();
        }
    }

    public function update(ProductUpdateFormRequest $request, $id)
    {


        DB::beginTransaction();

        try {
            $this->productRepository->update($request->except("_token"), $id);
            DB::commit();

            Toastr::success(__('product.Product has been updated Successfully'));
            if ($request->product_type == 'Service') {
                return redirect()->route("add_product.service");
            }

            return redirect()->route("add_product.create");

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Product creation');
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $this->productRepository->delete($id);
            Toastr::success(__('product.Product has been deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Product creation');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function destroyCombo($id)
    {
        try {
            $this->productRepository->deleteCombo($id);
            Toastr::success(__('product.Product has been deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Product creation');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function product_Detail(Request $request)
    {
        try {
            if ($request->type != "combo") {
                $product = $this->productRepository->find($request->id);
                $product_variant_type = json_decode(collect($product->variations)->pluck("variant_id")->first());
                $variants = $this->variationRepository->all();
                $variant_values = $variants->pluck("values")->flatten()->toArray();
                return view('product::product.product_details', [
                    "product" => $product,
                    "variants" => $variants,
                    "variant_values" => $variant_values,
                    "product_variant_type" => $product_variant_type,
                    "range" => $request->range,
                ]);
            }else {
                $product = $this->productRepository->findCombo($request->id);
                return view('product::product.combo_product_details', [
                    "product" => $product
                ]);
            }

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["error" => $e->getMessage()], 503);
        }
    }

    public function product_sku_get_price(Request $request)
    {
        try {
            return $this->productRepository->getPrice($request->sku_id, $request->purchase_price, $request->selling_price);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["error" => $e->getMessage()], 503);
        }
    }

    public function comboStatus(Request $request)
    {
        try{
            $language = $this->productRepository->findCombo($request->id);
            $language->status = $request->status;
            if($language->save()){
                return 1;
            }
            return 0;

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return __('common.Operation failed');
        }

    }

    public function list_select_option_opening_stock(Request $request)
    {
        $data = $this->productRepository->listForSelectProductOpeningStock($request->search, $request->brand_id, $request->model_id, $request->category_id);
        return response()->json($data);
    }

    public function pdfLabels()
    {
        $price = 0;
        $sku = $this->productRepository->findSku($request->id);
        if ($request->product_price)
        {
            if ($request->tax == 1)
                $price = $sku->selling_price + (($sku->price *20)/100);
            else
                $price = $sku->selling_price;
        }
        $data = [
            'sku'       => $sku,
            'name'      => $request->name,
            'variation' => $request->variation,
            'business'  => $request->business_name,
            'price'     => $price,
            'label'     => $request->label,
            'page'      => $request->page,
        ];
        $pdf = PDF::loadView('product::product.labels',compact('data'));
        $pdf->setPaper('a4')->setOrientation('landscape')->setOption('margin-bottom', 0);
        return $pdf->download('invoice.pdf');
    }

    public function sku_product_select_list_option(Request $request)
    {
        $data = $this->productRepository->listForSelectProductSKU($request->search);
        return response()->json($data);
    }

    public function service_select_list_option(Request $request)
    {
        $data = $this->productRepository->listForSelectService($request->search);
        return response()->json($data);
    }

    public function stock_select_list_option(Request $request)
    {
        $data = $this->productRepository->listForSelectStockProduct($request->search, $request->house, $request->purpose_filter);
        return response()->json($data);
    }

    public function add_opening_stock_create(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $column = ($request->has('col')) ? $request->col : null;
            $data['items'] = $this->stockTransferRepository->allProductListByShowroomListQuery($quick_search, $row_count, $column, ['itemable:id,name','productSku:id,product_id,purchase_price,selling_price', 'productSku.product:id,product_name,brand_id,model_id,unit_type_id', 'productSku.product.brand:id,name','productSku.product.model:id,name','productSku.product.unit_type:id,name'],['id','date','in_out','created_by','product_sku_id','itemable_type','itemable_id']);
            if ($request->ajax()) {
                return view('product::product.paginate_component.opening_list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->stockTransferRepository->allProductListByShowroomListQuery($quick_search, "all", $column,['itemable:id,name','productSku:id,product_id,purchase_price,selling_price', 'productSku.product:id,product_name,brand_id,model_id,unit_type_id', 'productSku.product.brand:id,name','productSku.product.model:id,name','productSku.product.unit_type:id,name'],['id','date','in_out','created_by','product_sku_id','itemable_type','itemable_id']);
                if ($request->import_as == "print") {
                    return view('product::product.paginate_component.opening_print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->stockTransferRepository->csvDownloadAddOpeningStock($data);
                    $filePath = public_path("uploads/csv/opening-stock-add.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-opening-stock-add.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }

            $data['showrooms'] = $this->showRoomRepository->all();
            $data['wareHouses'] = $this->wareHouseRepository->all();

            return view('product::product.add_opening_stock_create', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return redirect()->back();
        }
    }


    public function productDetailForStock(Request $request)
    {
        try{
            $product = $this->productRepository->findSku($request->id);
            return view('product::product.stock_add_product_details', [
                "product" => $product
            ]);
        }catch(\Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return redirect()->back();
        }
    }

    public function productDetailForPacking(Request $request)
    {
        try{
            $product = $this->productRepository->findSku($request->id);
            return $product;
        }catch(\Exception $e){
            return 0;
        }
    }

    public function selling_price_history($id)
    {
        $data['sell_histories'] = ProductSellingPriceHistory::with('purchase_order', 'productSku', 'user')->where('product_sku_id', $id)->latest()->get();
        return view('product::product.selling_price_history', $data);
    }

    public function csv_upload()
    {
        return view('product::product.upload_via_csv.create');
    }

    public function csv_upload_store(Request $request)
    {
        $validate_rules = [
            'file' => 'required|mimes:csv,xls,xlsx|max:2048'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        ini_set('max_execution_time', 0);
        DB::beginTransaction();
        try {
            $this->productRepository->csv_upload_single_product($request->except("_token"));
            DB::commit();
            Toastr::success(__('common.Successfully Uploaded !!!'));
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getCode() == 23000) {
                Toastr::error(__('common.Duplicate entry is exist in your file !!!'), __('common.Error'));
            }
            else {
                Toastr::error(__('common.Something went wrong. Upload again !!!'), __('common.Error'));
            }
            return back();
        }

    }
}
