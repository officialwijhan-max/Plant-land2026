<?php

namespace Modules\Purchase\Http\Controllers;

use PDF;
use Mail;
use App\User;
use stdClass;
use App\Traits\PdfGenerate;
use App\Traits\Notification;
use Illuminate\Http\Request;
use App\Traits\ProductSelect;
use App\Traits\ChecksAccountBalance;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Product\Entities\ProductSku;
use Modules\Setting\Model\EmailTemplate;
use Modules\Account\Entities\ChartAccount;
use Modules\Inventory\Entities\StockReport;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\ProductItemDetail;
use Modules\Purchase\Repositories\CNFRepository;
use Modules\Purchase\Http\Requests\PurchaseOrder;
use Modules\Sale\Repositories\SaleRepositoryInterface;
use Modules\Setup\Repositories\TaxRepositoryInterface;
use Modules\Product\Repositories\ProductRepositoryInterface;
use Modules\Product\Repositories\VariantRepositoryInterface;
use Modules\Contact\Repositories\ContactRepositoriesInterface;
use Modules\Inventory\Repositories\ShowRoomRepositoryInterface;
use Modules\Inventory\Repositories\WareHouseRepositoryInterface;
use Modules\Purchase\Repositories\PurchaseOrderRepositoryInterface;
use Modules\Setting\Repositories\GeneralSettingRepositoryInterface;
use Modules\Inventory\Repositories\StockTransferRepositoryInterface;
use Modules\ProAccount\Repositories\LeadgerRepository;
use Modules\Setting\Entities\PdfFont;


class PurchaseOrderController extends Controller
{
    use PdfGenerate, Notification,ProductSelect, ChecksAccountBalance;

    protected $contactRepository, $productRepository, $orderRepository, $showRoomRepository, $warehouseRepository, $settingRepository,
        $variationRepository, $taxRepository, $stockTransferRepository;

    public function __construct(ContactRepositoriesInterface $contactRepository, WareHouseRepositoryInterface $warehouseRepository, VariantRepositoryInterface $variationRepository,
                                ProductRepositoryInterface     $productRepository, PurchaseOrderRepositoryInterface $orderRepository, GeneralSettingRepositoryInterface $settingRepository,
                                ShowRoomRepositoryInterface    $showRoomRepository, TaxRepositoryInterface $taxRepository, StockTransferRepositoryInterface $stockTransferRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->contactRepository = $contactRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->variationRepository = $variationRepository;
        $this->warehouseRepository = $warehouseRepository;
        $this->showRoomRepository = $showRoomRepository;
        $this->settingRepository = $settingRepository;
        $this->taxRepository = $taxRepository;
        $this->stockTransferRepository = $stockTransferRepository;
    }

    public function get_due_invoice_list(Request $request)
    {
        $data = $this->orderRepository->getInvoiceList($request->leadger_id, $request->search);
        return response()->json($data);
    }

    public function index(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;

            $data['items'] = $this->orderRepository->withPaginate($row_count, $quick_search, $name, $sort, $column, null, ['supplier:id,name', 'payments:id,payable_id,payable_type,amount', 'items:id,itemable_id,itemable_type,quantity,price,sub_total'], ['id', 'supplier_id', 'invoice_no', 'date', 'payable_amount', 'status', 'is_paid', 'added_to_stock','return_status']);
            if ($request->ajax()) {
                return view('purchase::purchase_order.paginate.list', $data);
            }
            if ($request->has("import_as")) {
                $data['items'] = $this->orderRepository->withPaginate('all', $quick_search, $name, $sort, $column, null, ['supplier:id,name', 'payments:id,payable_id,payable_type,amount', 'items:id,itemable_id,itemable_type,quantity,price,sub_total'], ['id', 'supplier_id', 'invoice_no', 'date', 'payable_amount', 'status', 'is_paid', 'added_to_stock','return_status']);
                set_time_limit(-1);
                if ($request->import_as == "csv") {
                    $this->orderRepository->csvDownload(null, $data);
                    $filePath = public_path("uploads/csv/purchase-order-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-purchase-order-list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
                if ($request->import_as == "print") {
                    return view('purchase::purchase_order.paginate.print', $data);
                }
            }
            return view('purchase::purchase_order.purchases', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function purchase_return_list(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;
            $data['items'] = $this->orderRepository->withPaginate($row_count, $quick_search, $name, $sort, $column, "return_list", ['supplier:id,name', 'payments:id,payable_id,payable_type,amount'], ['id', 'supplier_id', 'invoice_no', 'date', 'payable_amount', 'status', 'added_to_stock','return_status']);
            if ($request->ajax()) {
                return view('purchase::purchase_order.paginate.rtn_create_list', $data);
            }
            if ($request->has("import_as")) {
                $data['items'] = $this->orderRepository->withPaginate($row_count, $quick_search, $name, $sort, $column, "return_list", ['supplier:id,name', 'payments:id,payable_id,payable_type,amount'], ['id', 'supplier_id', 'invoice_no', 'date', 'payable_amount', 'status', 'added_to_stock','return_status']);
                set_time_limit(-1);
                if ($request->import_as == "csv") {
                    $this->orderRepository->csvDownload('create', $data);
                    $filePath = public_path("uploads/csv/purchase-returns.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-purchase-returns.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
                if ($request->import_as == "print") {
                    return view('purchase::purchase_order.paginate.rtn_create_print', $data);
                }
            }
            return view('purchase::purchase_order.purchase_return_list', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function recieve_index(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;
            $data['items'] = $this->orderRepository->withPaginate($row_count, $quick_search, $name, $sort, $column, null, ['supplier:id,name'], ['id', 'supplier_id', 'invoice_no', 'date', 'status', 'added_to_stock']);
            if ($request->ajax()) {
                return view('purchase::purchase_order.paginate.rcv_list', $data);
            }
            if ($request->has("import_as")) {
                $data['items'] = $this->orderRepository->withPaginate('all', $quick_search, $name, $sort, $column, null, ['supplier:id,name'], ['id', 'supplier_id', 'invoice_no', 'date', 'status', 'added_to_stock']);
                set_time_limit(-1);
                if ($request->import_as == "csv") {
                    $this->orderRepository->csvDownloadRcv($data);
                    $filePath = public_path("uploads/csv/purchase-order-recieve-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-purchase-order-recieve-list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
                if ($request->import_as == "print") {
                    return view('purchase::purchase_order.paginate.rcv_print', $data);
                }
            }
            return view('purchase::purchase_order.purchases_recieve', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function suggestList(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 50;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;
            $data['suppliers'] = $this->contactRepository->aciveSupplier();
            $data['items'] = $this->orderRepository->withPaginateSupplierProducts($row_count, $quick_search, $name, $sort, $column, $request->supplier);
            if ($request->has('supplier')) {
                $data['supplier_id_selected'] = $request->supplier;
                $data['items'] = $this->orderRepository->withPaginateSupplierProducts($row_count, $quick_search, $name, $sort, $column, $request->supplier);
            }
            if ($request->ajax()) {
                return view('purchase::purchase_order.paginate.stock_alert_list', $data);
            }
            return view('purchase::purchase_order.suggest_list', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
        try {
            $suppliers = $this->contactRepository->aciveSupplier();

            $stocks = $this->stockTransferRepository->suggestList();

            return view('purchase::purchase_order.suggest_list', compact('stocks','suppliers'));
        } catch (\Exception $e) {
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function convertSuggest(Request $request)
    {
        $validate_rules = [
            'all_product' => 'required_without:stocks',
            'stocks' => 'required_without:all_product',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        try {
            $items = [];
            if ($request->all_product) {
                $items = $this->orderRepository->supplierProducts($request->supplier);
            } else {
                foreach ($request->stocks as $key => $stock) {
                    $items[] = $this->stockTransferRepository->stock($stock);
                }
            }
            $ProductList = $this->productRepository->stockProductList('purchase', null, null);
            $cnfRepo = new CNFRepository;
            $data = [
                'warehouses' => $this->warehouseRepository->all(),
                'showrooms' => $this->showRoomRepository->all(),
                'cnfs' => $cnfRepo->all(),
                'items' => $items,
                'supplier_id' => $request->supplier,
                'supplier' => ($request->supplier) ? $this->contactRepository->find($request->supplier) : null,
                'taxes' => $this->taxRepository->activeTax(),
            ];
            return view('purchase::purchase_order.add_purchase')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('purchase::purchase.something_went_wrong'));
            return back();
        }
    }

    public function create()
    {
        session()->forget('sku');

        try {
            $cnfRepo = new CNFRepository;
            $data = [
                'warehouses' => $this->warehouseRepository->all(),
                'showrooms' => $this->showRoomRepository->all(),
                'taxes' => $this->taxRepository->activeTax(),
                'cnfs' => $cnfRepo->all()
            ];
            return view('purchase::purchase_order.add_purchase')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function store(PurchaseOrder $request)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->create($request->except("_token"));
            if ($request->payment_method[0] != "due-00" && !empty($request->amount[0]) ) {
               $this->savePaymentFirst($request, $order->id);
            }
            if (app('business_settings')->where('type', 'purchase_approval')->first()->status == 1) {
                $this->orderRepository->approve($order->id);
            }
            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
                        ->where('is_active','1')
                        ->get(['id','role_id']);
            $created_by = Auth::user()->name;
            $content = __('notification.A purchase has been made by') . $created_by . ' which Invoice No. is <a href="' . route("purchase_order.show", $order->id) . '">' . $order->invoice_no . '</a> for this you have to pay total of ' . $order->payable_amount . '';
            $number = $order->supplier->mobile;
            $subject= __('notification.Purchase Create Reminder');
            $message = __('notification.A Purchase has been created by') . $created_by . ','.  __('notification.Invoice No:') . $order->invoice_no . ','. __('notification.Amount: ') . single_price($order->payable_amount) . '';
            $this->sendNotification($order, $order->supplier->email, $subject, $content, $number, $message,$users,null,route('purchase_order.show',$order->id));

            DB::commit();

            \LogActivity::successLog('Purchase Added Successfully with payment');
            Toastr::success(trans('purchase.Purchase Added Successfully'), __('common.Success'));
            return redirect()->route('purchase_order.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function payments($id)
    {
        try {
            $order = $this->orderRepository->find($id);
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $leadger_repo = new LeadgerRepository();
                $bank_accounts = $leadger_repo->bankAccounts();
            } else {
                $bank_accounts = ChartAccount::where('configuration_group_id', 2)->get();
            }
            $data = [
                'order' => $order,
                'bank_accounts' => $bank_accounts,
            ];

            return view('purchase::purchase_order.payment')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function returnPayments($id)
    {
        try {
            $order = $this->orderRepository->find($id);
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $leadger_repo = new LeadgerRepository();
                $bank_accounts = $leadger_repo->bankAccounts();
            } else {
                $bank_accounts = ChartAccount::where('configuration_group_id', 2)->get();
            }
            $data = [
                'order' => $order,
                'bank_accounts' => $bank_accounts,
            ];

            return view('purchase::purchase_order.return_payment')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function savePaymentFirst(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required'
        ], [
                'payment_method.required' => __('purchase.Please Select method')
            ]
        );
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
                    ];
                    $banks[] = $i;
                } else {
                    $cash_payments[$i] = [
                        'payment_method' => $req_payment_method[0],
                        'amount' => $request->amount[$i],
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

        // Called internally from store()/update() right after the order
        // itself is created - blocking the whole order here would also
        // discard the order, which is wrong (an unpaid/on-credit order is
        // a normal, valid state). Instead skip attaching the payment and
        // flag it, leaving the order to save as unpaid so the user can
        // pay it later via the normal payment screen once funds exist.
        $insufficientFundsMessage = $this->insufficientFundsMessage($payments);
        if ($insufficientFundsMessage) {
            Toastr::error($insufficientFundsMessage . ' The order was saved without this payment - add it once funds are available.', __('common.Error'));
            DB::commit();
            return 0;
        }

        $this->orderRepository->payments($payments, $id);
        DB::commit();
        return 1;
    }

    public function savePayment(Request $request, $id)
    {
        $validate_rules = [
            "amount" => "required_without_all:payment_method,quick_amounts",
            "payment_method" => "required_without_all:amount,quick_amounts",
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        try {
            $bank_payments = $cash_payments = [];

            if ($request->payment_method) {
                for ($i = 0; $i < count($request->payment_method); $i++) {
                    $req_payment_method = explode('-', $request->payment_method[$i]);
                    if ($req_payment_method[0] == 'bank') {
                        $bank_methods[$i] = [
                            'payment_method' => $req_payment_method[0],
                            'account_id' => $req_payment_method[1],
                            'amount' => $request->amount[$i],
                        ];
                        $banks[] = $i;
                    } else {
                        $cash_payments[$i] = [
                            'payment_method' => $req_payment_method[0],
                            'amount' => $request->amount[$i],
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

            $payments = array_merge($cash_payments, $bank_payments);

            $insufficientFundsMessage = $this->insufficientFundsMessage($payments);
            if ($insufficientFundsMessage) {
                Toastr::error($insufficientFundsMessage, __('common.Error'));
                return back();
            }

            $this->orderRepository->payments($payments, $id);
            DB::commit();
            \LogActivity::successLog('Purchase Added Successfully without payment');
            Toastr::success(trans('purchase.Payment Successful'), __('common.Success'));
            return redirect()->route('purchase_order.index');
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

        }
    }

    public function saveReturnPayment(Request $request, $id)
    {
        $validate_rules = [
            "amount" => "required_without_all:payment_method,quick_amounts",
            "payment_method" => "required_without_all:amount,quick_amounts",
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        try {
            $bank_payments = $cash_payments = [];

            if ($request->payment_method) {
                for ($i = 0; $i < count($request->payment_method); $i++) {
                    $req_payment_method = explode('-', $request->payment_method[$i]);
                    if ($req_payment_method[0] == 'bank') {
                        $bank_methods[$i] = [
                            'payment_method' => $req_payment_method[0],
                            'account_id' => $req_payment_method[1],
                            'amount' => $request->amount[$i],
                        ];
                        $banks[] = $i;
                    } else {
                        $cash_payments[$i] = [
                            'payment_method' => $req_payment_method[0],
                            'amount' => $request->amount[$i],
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

            $payments = array_merge($cash_payments, $bank_payments);

            $this->orderRepository->returnPayments($payments, $id);
            DB::commit();
            \LogActivity::successLog('Purchase Return Payment Successfull');
            Toastr::success(trans('purchase.Payment Successful'), __('common.Success'));
            return redirect()->route('purchase.return.index');
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));

        }
    }

    public function show($id)
    {
        try {
            $data = [
                'order' => $this->orderRepository->find($id),
                'setting' => $this->settingRepository->all(),
            ];
            return view('purchase::purchase_order.show')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function edit($id)
    {
        session()->forget('sku');
        try {
            $cnfRepo = new CNFRepository;

            $data = [
                'warehouses' => $this->warehouseRepository->all(),
                'showrooms' => $this->showRoomRepository->all(),
                'order' => $this->orderRepository->find($id),
                'taxes' => $this->taxRepository->activeTax(),
                'cnfs' => $cnfRepo->all()
            ];
            return view('purchase::purchase_order.edit_purchase')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function update(PurchaseOrder $request, $id)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->update($request->except("_token"), $id);
            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
                        ->where('is_active','1')
                        ->get(['id','role_id']);
            $updated_by = Auth::user()->name;
            $content = __('notification.A purchase has been made by') . $updated_by . ' which Invoice No. is <a href="' . route("purchase_order.show", $order->id) . '">' . $order->invoice_no . '</a> for this you have to pay total of ' . $order->payable_amount . '';
            $number = $order->supplier->mobile;
            $subject =__('notification.Purchase Update Reminder');
            $message = __('notification.A Purchase has been created by') . $updated_by . ','.  __('notification.Invoice No: ') . $order->invoice_no . ',' . __('notification.Amount: ') . single_price($order->payable_amount) . '';
            $this->sendNotification($order, $order->supplier->email,$subject, $content, $number, $message,$users,null,route('purchase_order.show',$order->id));
            if ($request->payment_method[0] != "due-00" && !empty($request->amount[0]) ) {
                $this->savePaymentFirst($request, $order->id);
            }
            DB::commit();


            \LogActivity::successLog('Purchase Updated Successfully without payment');
            Toastr::success(trans('purchase.Purchase Updated Successfully'), __('common.Success'));
            return redirect()->route('purchase_order.index');

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->orderRepository->delete($id);

            DB::commit();
            \LogActivity::successLog('Purchase Deleted Successfully');
            Toastr::success(trans('purchase.Purchase Deleted Successfully'), 'Success!');
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    //Ajax Request for Storing Products in Sale,Purchase,Pos
    public function storeProduct(Request $request)
    {
        try {
            $tax = 0;
            $productSku = $this->productRepository->findSku($request->id);
            $skus = session()->get('sku');

            $sku[$productSku->sku] = $productSku->sku;

            if (!empty($skus)) {
                if (array_key_exists($productSku->sku, $skus)) {
                    return 1;
                }
                session()->put('sku', $sku + $skus);
            } else
                session()->put('sku', $sku);

            if ($request->type == 'quotation')
                $price = $productSku->selling_price;
            else
                $price = $productSku->purchase_price;

            $sub_total = $price ;

            $variantName = $this->variationRepository->variantName($productSku);

            $type = $productSku->id . ",'sku'";
            if (app('general_setting')->origin == 1) {
                $row = '<td class="product_sku' . $productSku->id . '">' . @$productSku->product->origin . '</td>';
            }else {
                $row = '<td class="product_sku' . $productSku->id . '">' . $productSku->sku . '</td>';
            }
            $tax_options = '<input type="number" name="product_tax[]"  value="0" onkeyup="addTax(' . $type . ')" class="primary_input_field tax tax_sku' . $productSku->id . '">';

            $name = substr($productSku->product->product_name, 0, 40);

            $output = '';
            $output .= '<tr>
                        <td><input type="hidden" name="product_id[]" value="' . $productSku->id . '" class="primary_input_field sku_id' . $productSku->id . '">' . $name . '</br>' . $variantName . '</td>

                        '.$row.'
                        <td>'.@$productSku->product->model->name.'</td>
                        <td>'.@$productSku->product->brand->name.'</td>
                        <td><input name="product_price[]" onkeyup="priceCalc(' . $type . ')" class="primary_input_field product_price product_price_sku' . $productSku->id . '" type="number"
                        value="' . $price . '"></td>
                        <td><input name="product_selling_price[]" class="primary_input_field product_selling_price product_selling_price_sku' . $productSku->id . '" type="number"
                        value="' . $productSku->selling_price . '"></td>
                        <td>
                            <input type="number" name="product_quantity[]" value="1" onkeyup="addQuantity(' . $type . ')" class="primary_input_field quantity quantity_sku' . $productSku->id . '">
                        </td>

                        <td>
                            ' . $tax_options . '
                        </td>

                        <td>
                            <input type="number" name="product_discount[]" value="0" onkeyup="addDiscount(' . $type . ')" class="primary_input_field discount discount_sku' . $productSku->id . '">
                        </td>
                        <td style="text-align:center" class="product_subtotal product_subtotal_sku' . $productSku->id . '">' . $sub_total . '</td>
                        <td><a data-id="' . $productSku->id . '" data-product="' . $productSku->id . '-Normal" class="primary-btn primary-circle fix-gr-bg delete_product" href="javascript:void(0)"><i class="ti-trash"></i></a></td>
                        </tr>

                        ';

            return response()->json($output);

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(['error' => trans('common.Something Went Wrong')]);
        }


    }

    //Ajax Request for Deleting Items in Sale,Purchase,Pos

    public function itemDestroy(Request $request, SaleRepositoryInterface $saleRepository)
    {
        try {
            $saleRepository->itemDelete($request->id);

            return response()->json(trans('common.Deleted'));

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return trans('common.Something Went Wrong');
        }

    }

    public function returnList(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;
            $data['items'] = $this->orderRepository->withPaginateReturn($row_count, $quick_search, $name, $sort, $column, ['supplier:id,name']);
            if ($request->ajax()) {
                return view('purchase::purchase_order.paginate.rtn_list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->orderRepository->withPaginateReturn("all", $quick_search, $name, $sort, $column, ['supplier:id,name']);
                if ($request->import_as == "print") {
                    return view('purchase::purchase_order.paginate.rtn_print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->orderRepository->csvDownloadReturn($data);
                    $filePath = public_path("uploads/csv/purchase-returned-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-purchase-returned-list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('purchase::purchase_order.return_item_list', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function return_details($id)
    {
        try {
            $order = $this->orderRepository->find($id);
            return view('purchase::purchase_order.return_purchase_details', compact('order'));
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function purchaseReturn($id)
    {

        try {
            $order = $this->orderRepository->find($id);
            return view('purchase::purchase_order.return_purchase', compact('order'));
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function purchaseReturnPdf($id)
    {
        try {
            $data = $this->orderRepository->find($id);
            return $this->getInvoice('purchase::purchase_order.return_purchase_pdf', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function purchaseReturnPrint($id)
    {
        try {
            $data['data'] = $this->orderRepository->find($id);
            return view('purchase::purchase_order.return_purchase_print_view')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function returnItem(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $item = [];
            if ($request->items) {
                for ($i = 0; $i < count($request->items); $i++) {
                    $item[$i] = [
                        'item_id' => $request->items[$i],
                        'quantity' => $request->quantity[$i],
                        'return_subtotal' => $request->product_subtotal[$i],
                    ];
                }
            }
            $this->orderRepository->itemUpdate($request->except("_token"), $item, $request->id);
            DB::commit();
            Toastr::success(trans('purchase.Item Updated Successfully'), __('common.Success'));
            return redirect()->route('purchase.return.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->approve($id);
            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
                        ->where('is_active','1')
                        ->get(['id','role_id']);
            $created_by = Auth::user()->name;
            $content = 'A purchase has been approved which was created by ' . $created_by . ' which Invoice No. is <a href="' . route("purchase_order.show", $order->id) . '">' . $order->invoice_no . '</a> for this you have to pay total of ' . $order->payable_amount . '';
            $number = $order->supplier->mobile;
            $subject =__('notification.Purchase Approved Reminder');
            $message = 'A Purchase has been approved by ' . $created_by . ','. __('notification.Invoice No: ') . $order->invoice_no . ',' . __('notification.Amount: ') . single_price($order->payable_amount) . '';
            $this->sendNotification($order, $order->supplier->email,$subject, $content, $number, $message,$users,null,route('purchase_order.show',$order->id));

            // Commit Transaction
            DB::commit();
            Toastr::success(trans('purchase.Purchase has been Approved'), __('common.Success'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollback();
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    //Ajax Request for Storing Combo Products in Sale,Purchase,Pos

    public function storeComboProduct(Request $request)
    {
        try {
            $tax = 0;
            $productCombo = $this->productRepository->findCombo($request->id);
            $type = $productCombo->id . ",'combo'";
            $skus = session()->get('sku');

            $sku[$type] = $type;

            if (!empty($skus)) {
                if (array_key_exists($type, $skus)) {
                    return 1;
                }
                session()->put('sku', $sku + $skus);
            } else
                session()->put('sku', $sku);

            $variantName = $this->variationRepository->comboVariant($productCombo);

            $type = $productCombo->id . ",'combo'";
            $name = substr($productCombo->name, 0, 40);

            $output = '';
            $output .= '<tr>
                        <td><input type="hidden" name="combo_product_id[]" value="' . $productCombo->id . '" class="primary_input_field sku_id' . $productCombo->id . '">' . $name . '</br>' . $variantName . '</td>

                        <td class="product_sku"></td>

                        <td><input type="text" name="combo_product_price[]" class="primary_input_field product_price product_price_combo' . $productCombo->id . '" value="' . $productCombo->price . '"></td>

                        <td>
                            <input type="number" data-type="combo" name="combo_product_quantity[]" value="1" onfocusout="addQuantity(' . $type . ')"
                            class="primary_input_field quantity quantity_combo' . $productCombo->id . '">
                        </td>

                        <td>
                            <input type="number" name="combo_product_tax[]"  value="' . $productCombo->tax . '" onkeyup="addTax(' . $type . ')" class="primary_input_field tax tax_combo' . $productCombo->id . '">
                        </td>

                        <td>
                            <input type="number" data-type="combo" name="combo_product_discount[]" value="0" onkeyup="addDiscount(' . $type . ')"
                            class="primary_input_field discount discount_combo' . $productCombo->id . '">
                        </td>
                        <td style="text-align:center" class="product_subtotal product_subtotal_combo' . $productCombo->id . '">' . $productCombo->price . '</td>
                        <td><a data-id="' . $productCombo->id . '" data-product="' . $productCombo->id . '-Combo" class="primary-btn primary-circle fix-gr-bg delete_product new_delete_product" href="javascript:void(0)"><i class="ti-trash"></i></a></td>
                        </tr>

                        ';

            return response()->json($output);

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(['error' => trans('common.Something Went Wrong')]);

        }


    }

    public function returnApprove($id)
    {
        try {
            $this->orderRepository->returnApprove($id);
            Toastr::success(trans('purchase.Purchase Return has been Approved'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function addToStock($id)
    {
        try {
            $order = $this->orderRepository->find($id);

            return view('purchase::purchase_order.recieve_purchase_form',compact('order'));
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function receiveProducts(Request $request,$id)
    {
        try {
            DB::beginTransaction();
            $order = $this->orderRepository->addToStock($id,$request->all());
            if ($order->added_to_stock == 1)
            {
                $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
                        ->where('is_active','1')
                        ->get(['id','role_id']);

                $role_id = null;
                $subject = __('notification.Product Receive Reminder');
                $class = $order;
                $data =  __('notification.Products Has been Received by') . '{'.$order->supplier->name.'}';
                $url = route('purchase.receive.products', $order->id);
                $this->sendNotification($class,null,$subject,null,null,$data,$users,$role_id,$url);
            }
            DB::commit();
            Toastr::success(trans('purchase.Product Has Been Received Successfully'));
            return redirect()->route('purchase_order.recieve.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function receiveProductList($id)
    {
        try {
            $order = $this->orderRepository->find($id);
            return view('purchase::purchase_order.receive_item_list', compact('order'));
        } catch (\Exception $e) {
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function purchaseDetails(Request $request)
    {
        $due = $url = $invoice = '';
        try {

            $supplier = $this->contactRepository->find($request->supplier_id);

            $due = $supplier->accounts ? single_price($supplier->accounts['due']) : '';

            $purchase = $supplier->lastSupply;

            if ($purchase) {
                $url = route('purchase_order.show', $purchase->id);
                $invoice = $purchase->invoice_no;
            }

            return response()->json([
                'due' => $due,
                'url' => $url,
                'invoice' => $invoice,
            ]);
        } catch (\Exception $e) {
            return response(trans('common.Something Went Wrong'));
        }
    }

    public function orderDetails(Request $request)
    {
        try {
            if (!$request->supplier_id)
                $order = $this->orderRepository->find($request->id);
            else {
                $supplier = $this->contactRepository->find($request->supplier_id);
                $order = $supplier->lastSupply;
            }
            $data = [
                'order' => $order,
                'setting' => $this->settingRepository->all(),
            ];
            return view('purchase::purchase_order.get_details')->with($data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return trans('common.Something Went Wrong');
        }
    }

    public function getPdf($id)
    {
        try {
            $data = $this->orderRepository->find($id);
            return $this->getInvoice('purchase::purchase_order.pdf', $data);
        } catch (\Exception $e) {

            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function print_view($id)
    {
        try {
            $data['data'] = $this->orderRepository->find($id);
            return view('purchase::purchase_order.print_view',$data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function add_to_stock_store(Request $request)
    {
        if ($request->product_sku_id == "Select Product") {
            Toastr::error(trans('purchase.Please select product first.'), __('common.Error'));
            return back();
        }
        DB::beginTransaction();
        try {
            $sku = $this->orderRepository->adToStockOpening($request->except("_token"));
            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
                        ->where('is_active','1')
                        ->get(['id','role_id']);
            $role_id = $request->for_whom;
            $subject = $sku->product->product_name;
            $class = $sku;
            $data = __('notification.Product Has been Added as Opening Stock');
            $url = route('stock.report');
            $this->sendNotification($class,null,$subject,null,null,$data,$users,$role_id,$url);
            DB::commit();
            Toastr::success(trans('purchase.Product has been added to stock successfully'), __('common.Success'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            DB::rollBack();
            Toastr::error($e->getMessage());
            return back();
        }
    }

    public function purchasePayment(Request $request)
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $leadger_repo = new LeadgerRepository();
            $bank_accounts = $leadger_repo->bankAccounts();
        } else {
            $bank_accounts = ChartAccount::where('configuration_group_id', 2)->get();
        }
        $data = [
            'method' => $request->type,
            'supplier' => $request->supplier,
            'total_amount' => $request->amount,
            'total_qty' => $request->quantity,
            'bank_accounts' => $bank_accounts,
        ];
        if ($request->type == 'cash')
            return view('purchase::purchase_order.cash_payment_modal')->with($data);
        else
            return view('purchase::purchase_order.payment_modal')->with($data);

    }

    public function send_mail($id)
    {
        try {

            $data = $this->orderRepository->find($id);
            if ($data->supplier->email != null) {
                $datas["email"] = app('general_setting')->email;
                $datas["title"] = EmailTemplate::where('type', 'purchase_template')->first()->subject;
                $datas["body"] = EmailTemplate::where('type', 'purchase_template')->first()->value;
                $datas["body"] = str_replace("{USER_FIRST_NAME}",$data->supplier->name,$datas["body"]);
                $datas["body"] = str_replace("{USER_LOGIN_EMAIL}",$data->supplier->email,$datas["body"]);
                $datas["body"] = str_replace("{EMAIL_SIGNATURE}",app('general_setting')->mail_signature,$datas["body"]);
                $datas["body"] = str_replace("{EMAIL_FOOTER}",app('general_setting')->mail_footer,$datas["body"]);
                $pdf_font= PdfFont::where('is_active',1)->first();
                $pdf = PDF::loadView('purchase::purchase_order.pdf', compact('data', 'pdf_font'))->setPaper('a4', 'portrait');
                Mail::send('sale::emails.quote_mail', $datas, function ($message) use ($data, $datas, $pdf) {
                    $message->to($data->supplier->email, $data->supplier->email)
                        ->subject($datas["title"])
                        ->attachData($pdf->output(), $data->invoice_no . '.pdf');
                });
                Toastr::success('Mail Send To ' . $data->supplier->email . ' Successfully!');
            } else {
                Toastr::error(__('common.Supplier email Does not exist !'), __('common.Error'));
            }
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    //Ajax Product Modal Render for Products
    public function product_modal_for_select(Request $request)
    {
        try {
            $type = explode('-', $request->id);
            if ($type[1] == "Single") {
                $data['product_id'] =$this->storeSkuProduct($type[0]);
                $data['product_type'] = $type[1];
            } elseif ($type[1] == "Combo") {
                $data['product_id'] =$this->storeCombo($type[0]) ;
                $data['product_type'] = $type[1];
            }
            else {
                $data['product_id'] = $type[0];
                $data['product_type'] = $type[1];
                $data['html'] = (string)view('sale::sale.product_details', [
                    "product" => $this->productRepository->find($type[0]),
                    'from' => 'purchase'
                ]);
            }

            return mb_convert_encoding($data, "UTF-8", "auto");
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(['error' => __('common.Something Went Wrong')]);
        }
    }

    public function filterProduct(Request $request)
    {
        $supplier = $this->contactRepository->find($request->supplier)->name;
        $row_count = ($request->has('row')) ? $request->row : 50;
        $sort = ($request->has('sort')) ? $request->sort : 'asc';
        $column = ($request->has('col')) ? $request->col : null;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
        $name = ($request->has('name')) ? $request->name : null;
        $data['suppliers'] = $this->contactRepository->aciveSupplier();
        if ($request->has('supplier')) {
            $data['supplier_id_selected'] = $request->supplier;
            $data['items'] = $this->orderRepository->withPaginateSupplierProducts($row_count, $quick_search, $name, $sort, $column, $request->supplier);
        }
        return view('purchase::purchase_order.paginate.stock_alert_list',$data);
    }

    public function multiple_payment_modal(Request $request)
    {
        $total_amount = $request->total_amount;
        $total_qty = $request->total_qty;
        $paid_amount = $request->paid_amount ?? 0;
        $bank_accounts = ChartAccount::where('configuration_group_id', 2)->get();
        return view('purchase::purchase_order.payment_modal', compact('total_amount', 'bank_accounts', 'total_qty','paid_amount'));
    }

    public function purchase_product_select_list_option(Request $request)
    {
        $data = $this->productRepository->listForSelectPurchaseProduct($request->search);
        return response()->json($data);
    }
}
