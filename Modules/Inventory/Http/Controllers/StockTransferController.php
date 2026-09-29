<?php



namespace Modules\Inventory\Http\Controllers;



use App\User;

use App\Traits\Notification;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\DB;

use Brian2694\Toastr\Facades\Toastr;

use Modules\Inventory\Entities\ShowRoom;

use Modules\Inventory\Entities\WareHouse;

use Illuminate\Contracts\Support\Renderable;

use Modules\Product\Repositories\BrandRepository;

use Modules\Product\Repositories\ProductRepository;

use Modules\Inventory\Http\Requests\StockTransferRequest;

use Modules\Product\Repositories\ProductRepositoryInterface;

use Modules\Product\Repositories\VariantRepositoryInterface;

use Modules\Contact\Repositories\ContactRepositoriesInterface;

use Modules\Inventory\Repositories\ShowRoomRepositoryInterface;

use Modules\Inventory\Repositories\WareHouseRepositoryInterface;

use Modules\Inventory\Repositories\StockTransferRepositoryInterface;



class StockTransferController extends Controller

{

    use Notification;

    protected $productRepository, $wareHouseRepository, $stockTransferRepository, $showRoomRepository,$contactRepositories;

    /**

     * @var VariantRepositoryInterface

     */

    private $variationRepository;



    public function __construct(WareHouseRepositoryInterface $wareHouseRepository, ProductRepositoryInterface $productRepository, StockTransferRepositoryInterface $stockTransferRepository,

                                ShowRoomRepositoryInterface  $showRoomRepository, VariantRepositoryInterface $variationRepository, ContactRepositoriesInterface $contactRepositories)

    {

        $this->middleware(['auth', 'verified']);

        $this->productRepository = $productRepository;

        $this->wareHouseRepository = $wareHouseRepository;

        $this->stockTransferRepository = $stockTransferRepository;

        $this->showRoomRepository = $showRoomRepository;

        $this->variationRepository = $variationRepository;

        $this->contactRepositories = $contactRepositories;

    }



    public function index(Request $request)

    {

        try {

            $row_count = ($request->has('row')) ? $request->row : 10;

            $sort = ($request->has('sort')) ? $request->sort : 'asc';

            $column = ($request->has('col')) ? $request->col : null;

            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;

            $name = ($request->has('name')) ? $request->name : null;

            $data['items'] = $this->stockTransferRepository->withPaginate($row_count, $quick_search, $name, $sort, $column, 'sent', ['sendable:id,name', 'receivable:id,name', 'items:id,itemable_id,itemable_type,productable_id,productable_type,quantity,sub_total'], ['id', 'sendable_id', 'sendable_type', 'receivable_id', 'receivable_type', 'date', 'status', 'sent_at', 'received_at']);

            if ($request->ajax()) {

                return view('inventory::stock_transfer.paginate.list', $data);

            }

            if ($request->has("import_as")) {

                set_time_limit(-1);

                $data['items'] = $this->stockTransferRepository->withPaginate("all", $quick_search, $name, $sort, $column, 'sent', ['sendable:id,name', 'receivable:id,name', 'items:id,itemable_id,itemable_type,productable_id,productable_type,quantity,sub_total'], ['id', 'sendable_id', 'sendable_type', 'receivable_id', 'receivable_type', 'date', 'status', 'sent_at', 'received_at']);

                if ($request->import_as == "print") {

                    \LogActivity::successLog(trans('common.Print has been Done') . ' - Sent Stock Transfer List', route('stock-transfer.index'), "Print");

                    return view('inventory::stock_transfer.paginate.print', $data);

                }

                if ($request->import_as == "csv") {

                    $this->stockTransferRepository->csvDownload("sent", $data);

                    \LogActivity::successLog(trans('common.CSV Download has been done') . ' - Sent Stock Transfer List', route('stock-transfer.index'), "CSV Download");

                    $filePath = public_path("uploads/csv/stock-transfer-list.xlsx");

                    $headers = ['Content-Type: text/csv'];

                    $fileName = time() . '-stock-transfer-list.xlsx';



                    return response()->download($filePath, $fileName, $headers);

                }

            }

            return view('inventory::stock_transfer.index', $data);

        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }

    }



    public function stockList(Request $request)

    {

        try{

            $row_count = ($request->has('row')) ? $request->row : 10;

            $sort = ($request->has('sort')) ? $request->sort : 'asc';

            $column = ($request->has('col')) ? $request->col : null;

            $search_keyword = ($request->has('quick_search')) ? $request->quick_search : null;



            $productRepo = new ProductRepository;

            $brandRepo = new BrandRepository;
            
            

            if ($request->showroom || $request->supplier || $request->product_sku_id || $request->brand_id) {

                if ($request->showroom) {
                    $house = explode('-', $request->showroom);
                } else {
                    $house[0] = '';
                    $house[1] = '';
                }
                if ($request->product_sku_id) {
                    $product_sku_id = explode('-',$request->product_sku_id)[0];
                } else {
                    $product_sku_id = '';
                }
                if ($request->brand_id) {
                    $brand_id = $request->brand_id;
                } else {
                    $brand_id = '';
                }
                if ($request->supplier) {
                    $supplier = $request->supplier;
                } else {
                    $supplier = '';
                }
                



                $data = [

                    'stocks' => $this->stockTransferRepository->stockList(),

                    'items' => stockList($row_count, $sort, $column, $search_keyword, $house[0],$house[1],$supplier, $product_sku_id, $brand_id),

                    'showroom' => $request->showroom,

                    'brand' => $request->brand_id > 0 ? $brandRepo->find($request->brand_id) : null,

                    'supplier' => $request->supplier ? $this->contactRepositories->find($request->supplier) : null,

                    'product' => $product_sku_id > 0 ? $productRepo->findSku($product_sku_id) : null,

                ];

            }else {

                $house[0] = session()->get('showroom_id');

                $house[1] = "Modules\Inventory\Entities\ShowRoom";

                $data = [

                    'stocks' => $this->stockTransferRepository->stockList(),

                    'items' => stockList($row_count, $sort, $column, $search_keyword, $house[0],$house[1],null, null, null),

                ];

            }

            if ($request->has("import_as")) {

                set_time_limit(-1);

                if ($request->import_as == "csv") {

                    $this->stockTransferRepository->csvDownloadStockList($data);

                    $filePath = public_path("uploads/csv/stock_list.xlsx");

                    $headers = ['Content-Type: text/csv'];

                    $fileName = time() . '-stock_list.xlsx';



                    return response()->download($filePath, $fileName, $headers);

                }

               if ($request->import_as == "print") {

                    return view('inventory::stock_transfer.paginate.stock_print', $data);

                }

            }

            if ($request->ajax()) {

               return view('inventory::stock_transfer.paginate.stock_list',$data);

            }

            return view('inventory::stock_transfer.stock_list')->with($data);



        }catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }



    }



    // public function stockListReport(Request $request)

    // {

    //     try{

    //         $productRepo = new ProductRepository;

    //         $brandRepo = new BrandRepository;

    //         if ($request->showroom || $request->supplier || $request->product_sku_id || $request->brand_id) {

    //             $house = explode('-', $request->showroom);



    //             $data = [

    //                 'stocks' => $this->stockTransferRepository->stockList(),

    //                 'product_stocks' => stockList($house[0],$house[1],$request->supplier, $request->product_sku_id, $request->brand_id),

    //                 'showroom' => $request->showroom,

    //                 'product_sku_id' => $request->product_sku_id,

    //                 'brand_id' => $request->brand_id,

    //                 'req_supplier' => $request->supplier ? $this->contactRepositories->find($request->supplier) : null,

    //                 'products' => $productRepo->all(),

    //                 'brands' => $brandRepo->all(),

    //             ];

    //         }else {

    //             $house[0] = session()->get('showroom_id');

    //             $house[1] = "Modules\Inventory\Entities\ShowRoom";

    //             $data = [

    //                 'stocks' => $this->stockTransferRepository->stockList(),

    //                 'product_stocks' => stockList($house[0],$house[1],null, null, null),

    //                 'products' => $productRepo->all(),

    //                 'brands' => $brandRepo->all()

    //             ];

    //         }



    //         return view('inventory::stock_transfer.stock_report')->with($data);



    //     }catch (\Exception $e) {

    //         \LogActivity::errorLog($e->getMessage());

    //         Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

    //         return back();

    //     }



    // }



    public function create()

    {

        try {

            $data = [

                'warehouses' => $this->wareHouseRepository->all(),

                'showrooms' => $this->showRoomRepository->all(),

            ];

            return view('inventory::stock_transfer.create')->with($data);

        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }

    }



    public function store(StockTransferRequest $request)

    {

        try {

            if ($request->from == $request->to) {

                Toastr::error(__('inventory.Two branches matched! Please change any branch'), __('common.Error'));

                return back();

            }

            DB::beginTransaction();

            $transfer = $this->stockTransferRepository->create($request->except("_token"));

            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)

                        ->where('is_active','1')

                        ->get(['id','role_id']);

            $role_id = null;

            $subject = __('notification.Stock Transfer Reminder');

            $class = $transfer;

            $data = __('notification.Transfer Has been Made From') ."{$transfer->sendable->name} to {$transfer->receivable->name}";

            $url = route("stock-transfer.index");

            $this->sendNotification($class,null,$subject,null,null,$data,$users,$role_id,$url);

            DB::commit();

            \LogActivity::successLog('Product Added To Transfer List');

            Toastr::success(__('inventory.Product Added To Transfer List'), __('common.Success'));

            return redirect()->route('stock-transfer.index');

        } catch (\Exception $e) {

            DB::rollBack();

            \LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }

    }



    public function show($id)

    {

        try {

            $data = [

                'transfer' => $this->stockTransferRepository->find($id),

            ];



            return view('inventory::stock_transfer.show')->with($data);

        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }

    }



    public function print_view($id)

    {

        try {

            $data['data'] = $this->stockTransferRepository->find($id);

            return view('inventory::stock_transfer.stock_transfer_print', $data);

        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            Toastr::error(trans('common.Something Went Wrong'));

            return back();

        }

    }



    public function edit($id)

    {

        try {

            $transfer = $this->stockTransferRepository->find($id);



            $data = [

                'transfer' => $transfer,

                'warehouses' => $this->wareHouseRepository->all(),

                'showrooms' => $this->showRoomRepository->all(),

            ];

            return view('inventory::stock_transfer.edit')->with($data);

        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }

    }



    public function update(StockTransferRequest $request, $id)

    {

        DB::beginTransaction();

        try {

            $this->stockTransferRepository->update($request->except("_token"), $id);

            DB::commit();

            \LogActivity::successLog('Product Updated To Transfer List');

            Toastr::success(__('inventory.Product Updated To Transfer List Successfully'), __('common.Success'));

            return redirect()->route('stock-transfer.index');



        } catch (\Exception $e) {

            DB::rollBack();

            \LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }

    }



    public function destroy($id)

    {

        DB::beginTransaction();

        try {

            $this->stockTransferRepository->delete($id);

            DB::commit();

            \LogActivity::successLog('Product deleted from Transfer List');

            Toastr::success(__('inventory.Product deleted from Transfer List'), __('common.Success'));

            return back();

        } catch (\Exception $e) {

            DB::rollBack();

            \LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }

    }



    //Ajax Request for storing products in stock Transfer list

    public function storeProduct(Request $request)

    {

        try{

            $productSku = $this->productRepository->findSku($request->id);

            $variantName = $this->variationRepository->variantName($productSku);



            $output = '';

            $type = $productSku->id . ",'sku'";

            $output .= '<tr>

                        <td><input type="hidden" name="product_id[]" value="' . $productSku->id . '" class="primary_input_field">' . $productSku->product->product_name . '</br>' . $variantName . '</td>



                        <td class="product_sku' . $productSku->id . '">' . $productSku->sku . '</td>



                        <td><input name="product_price[]" min="' . $productSku->purchase_price . '" onkeyup="priceCalc(' . $type . ')" class="primary_input_field product_price product_price_sku' . $productSku->id . '" type="number"

                        value="' . $productSku->purchase_price . '"></td>



                        <td>

                            <input type="number" name="quantity[]" value="1" onfocusout="addQuantity(' . $type . ')" class="primary_input_field quantity quantity_sku' . $productSku->id . '">

                        </td>



                        <td style="text-align:right" class="product_subtotal product_subtotal_sku' . $productSku->id . '">' . $productSku->purchase_price . '</td>

                        <td style="text-align:right"><a data-id="' . $productSku->id . '" class="delete_product primary-btn primary-circle fix-gr-bg" href="javascript:void(0)"><i class="ti-trash"></i></a></td>

                        </tr>



                        ';



            return response()->json($output);



        }catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());



            return response()->json(['error'=> trans('common.Something Went Wrong')]);

        }

    }



    //Ajax Request for checking stock in pos,sale,

    public function checkQuantity(Request $request)

    {

        try{

            $data['msg'] = $this->productRepository->checkQuantity($request->all());

            $data['stock'] = $this->productRepository->checkNumberofQuantity($request->all());



            return response()->json($data);



        }catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            return response()->json(['error'=> trans('common.Something Went Wrong')]);

        }



    }



    public function statusChange($id)

    {

        try {

            $this->stockTransferRepository->statusChange($id);

            Toastr::success(__('inventory.Stock Transfer Approve Successfully'), __('common.Success'));

            return back();

        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }

    }



    public function sendToHouse($id)

    {

        DB::beginTransaction();

        try {

            $this->stockTransferRepository->sendToHouse($id);

            DB::commit();

            \LogActivity::successLog('Stock Transfer Send Successfully');

            Toastr::success(__('inventory.Stock Transfer Send Successfully'), __('common.Success'));

            return back();

        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            DB::rollBack();

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }

    }



    public function stockReceive($id)

    {

        DB::beginTransaction();

        try {

            $transfer = $this->stockTransferRepository->stockReceive($id);

            DB::commit();

            if (is_numeric($transfer))

            {

                Toastr::error('stock is limited', 'Error!');

                return back();

            }

            \LogActivity::successLog('Stock Received Successfully');

            Toastr::success(__('inventory.Stock Received Successfully'), __('common.Success'));

            return back();

        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            DB::rollBack();

            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

            return back();

        }

    }



    public function productExist(Request $request)

    {

        $type = explode('-',$request->val);



        $house = $type[0] == 'warehouse' ? WareHouse::class : ShowRoom::class;



        $products = $this->productRepository->productList($type[1],$house);



        $output = '<option value="1">'.__('sale.Select Product').'</option>';



        foreach ($products as $product)

        {

            $output .= '<option value="'.$product->product_id.'-'.$product->product_type.'">'.$product->product_name.'</option>';

        }



        return $output;

    }



    public function productInfo(Request $request)

    {

        $row_count = ($request->has('row')) ? $request->row : 10;

        $sort = ($request->has('sort')) ? $request->sort : 'asc';

        $column = ($request->has('col')) ? $request->col : null;

        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;

        $data['items'] = $this->stockTransferRepository->allStockProductwithPaginate($row_count, $quick_search, $sort, $column);

        if ($request->ajax()) {

            return view('inventory::stock_transfer.paginate.info_list', $data);

        }

        if ($request->has("import_as")) {

            set_time_limit(-1);

            $data['items'] = $this->stockTransferRepository->allStockProductwithPaginate("all", $quick_search, $sort, $column);

            if ($request->import_as == "print") {

                return view('inventory::stock_transfer.paginate.info_print', $data);

            }

            if ($request->import_as == "csv") {

                $this->stockTransferRepository->csvDownloadStockProduct($data);

                $filePath = public_path("uploads/csv/product-info-list.xlsx");

                $headers = ['Content-Type: text/csv'];

                $fileName = time() . '-product-info-list.xlsx';



                return response()->download($filePath, $fileName, $headers);

            }

        }



        return view('inventory::stock_transfer.product_info',$data);

    }



    //Ajax Product Modal Render for Products

    public function product_modal_for_select(Request $request)

    {

        try {

            $type = explode('-', $request->id);

            if ($type[1] == "Single") {



                $data['product_id'] = $type[0];

                $data['product_type'] = $type[1];



            } elseif ($type[1] == "Combo") {

                $data['product_id'] =$type[0];

                $data['product_type'] = $type[1];

            } else {

                $data['product_id'] = $type[0];

                $data['product_type'] = $type[1];



                $data['html'] = (string)view('sale::sale.product_details', [

                    "product" => $this->productRepository->find($type[0])

                ]);

            }



            return response()->json($data);

        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());

            return response()->json(['error' =>__('common.Something Went Wrong')]);

        }

    }



    public function getProducts (Request $request)

    {

         $house = explode('-',$request->id);

         if ($house[0] == 'warehouse') {

             $class = WareHouse::class;

         }

         else

            $class = ShowRoom::class;



        $output = '';

        $ProductList = $this->productRepository->stockProductList('transfer',$house[1],$class);



        $output .= '<option selected disabled>'.__('common.Select').'</option>';

        foreach ($ProductList as $product)

        {

            $output .= '<option value="'.$product->product_id.'-'.$product->product_type.'">'.$product->product_name.'</option>';

        }



        return $output;

    }

}

