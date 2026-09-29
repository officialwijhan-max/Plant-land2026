<?php

namespace Modules\Purchase\Repositories;

use Carbon\Carbon;

use App\Traits\Accounts;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Purchase\Exports\PurchaseExport;
use Modules\Purchase\Exports\PurchaseReturnExport;
use Modules\Purchase\Exports\PurchaseReceivedExport;
use Modules\Purchase\Exports\InvoiceListFromSupplierExport;
use update\Modules\Purchase\Exports\ReturnInvoiceFromSupplierExport;
use Modules\Account\Repositories\JournalRepository;
use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Inventory\Entities\WareHouse;
use Modules\Sale\Entities\Payment;
use Modules\Product\Entities\ProductHistory;
use Modules\Product\Entities\ProductSku;
use Modules\Product\Repositories\ProductRepository;
use Modules\Account\Repositories\VoucherRepository;
use Modules\ProAccount\Repositories\VoucherRepository as ProVoucherRepository;
use Modules\Purchase\Entities\ProductItemDetail;
use Modules\Purchase\Entities\CostOfGoodHistory;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Inventory\Entities\StockReport;
use Modules\Account\Entities\ChartAccount;
use Modules\ProAccount\Entities\Leadger;
use Modules\Contact\Entities\ContactModel;
use Modules\Product\Entities\PartNumber;
use Modules\Product\Entities\ProductSellingPriceHistory;
use Importer;
use Modules\Purchase\Entities\ReceiveProduct;
use Modules\Setup\Entities\Tax;

class PurchaseOrderRepository implements PurchaseOrderRepositoryInterface
{
    use Accounts;

    public function all()
    {
        if (auth()->user()->role->type == "system_user") {
            return PurchaseOrder::with('supplier', 'payments')->latest()->get();
        } else {
            return PurchaseOrder::with('supplier', 'payments')->whereHasMorph('purchasable', ShowRoom::class)
                ->where('purchasable_id', session()->get('showroom_id'))->latest()->get();
        }
    }

    public function approvePurchase()
    {
        if (session()->get('showroom_id') == 1)
            return PurchaseOrder::with('supplier:id,name','purchasable:id,name')->where('status', 1)->latest()->get(['id','invoice_no','date','purchasable_id','purchasable_type','payable_amount','supplier_id','is_paid']);
        else
            return PurchaseOrder::with('supplier:id,name','purchasable:id,name')->whereHasMorph('purchasable', ShowRoom::class)->where('status', 1)
                ->where('purchasable_id', session()->get('showroom_id'))->latest()->get(['id','invoice_no','date','purchasable_id','purchasable_type','payable_amount','supplier_id','is_paid']);
    }

    public function csvDownload($purpose, $data)
    {
        if ($purpose == "create") {
            if (file_exists(public_path("uploads/csv/purchase-returns.xlsx"))) {
                unlink(public_path("uploads/csv/purchase-returns.xlsx"));
            }
            return Excel::store(new PurchaseExport($purpose, $data), 'uploads/csv/purchase-returns.xlsx', 'public_folder');
        } else {
            if (file_exists(public_path("uploads/csv/purchase-order-list.xlsx"))) {
                unlink(public_path("uploads/csv/purchase-order-list.xlsx"));
            }
            return Excel::store(new PurchaseExport($purpose, $data), 'uploads/csv/purchase-order-list.xlsx', 'public_folder');
        }
    }

    public function csvDownloadRcv($data)
    {
        if (file_exists(public_path("uploads/csv/purchase-order-recieve-list.xlsx"))) {
            unlink(public_path("uploads/csv/purchase-order-recieve-list.xlsx"));
        }
        return Excel::store(new PurchaseReceivedExport($data), 'uploads/csv/purchase-order-recieve-list.xlsx', 'public_folder');
    }

    public function csvDownloadReturn($data)
    {
        if (file_exists(public_path("uploads/csv/purchase-returned-list.xlsx"))) {
            unlink(public_path("uploads/csv/purchase-returned-list.xlsx"));
        }
        return Excel::store(new PurchaseReturnExport($data), 'uploads/csv/purchase-returned-list.xlsx', 'public_folder');
    }

    public function csvDownloadSupplierInvoice($data, $user_type)
    {
        if (file_exists(public_path("uploads/csv/supplier-invoice-list.xlsx"))) {
            unlink(public_path("uploads/csv/supplier-invoice-list.xlsx"));
        }
        return Excel::store(new InvoiceListFromSupplierExport($data, $user_type), 'uploads/csv/supplier-invoice-list.xlsx', 'public_folder');
    }

    public function csvDownloadSupplierReturnInvoice($data, $user_type)
    {
        if (file_exists(public_path("uploads/csv/supplier-return-invoice-list.xlsx"))) {
            unlink(public_path("uploads/csv/supplier-return-invoice-list.xlsx"));
        }
        return Excel::store(new ReturnInvoiceFromSupplierExport($data, $user_type), 'uploads/csv/supplier-return-invoice-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count, $quick_search, $name, $sort, $column, $list_type, $relational_data = [], $selected_data = ['*'])
    {
        $items = PurchaseOrder::query();
        $items = $items->with($relational_data);
        if (auth()->user()->role->type != "system_user") {
            $items = $items->whereHasMorph('purchasable', ShowRoom::class)->where('purchasable_id', session()->get('showroom_id'));
        }
        if ($quick_search != null) {
            $items = $items->whereLike(['invoice_no', 'date', 'payable_amount', 'total_quantity'], $quick_search)
                ->orWhereHas('supplier', function ($q) use ($quick_search) {
                    return $q->whereLike(['name'], $quick_search);
                });
        }
        if ($name != null) {
            $items = $items->whereLike(['invoice_no'], $name);
        }
        if ($list_type == "return_list") {
            $items = $items->where('status', 1);
        }
        if ($row_count == "all") {
            $total_number = PurchaseOrder::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function withPaginateSupplierProducts($row_count, $quick_search, $name, $sort, $column, $supplier, $relational_data = [], $selected_data = ['*'])
    {
        $items = StockReport::query();
        $items = $items->with($relational_data)
                        ->where('houseable_type', ShowRoom::class)
                        ->where('houseable_id', session()->get('showroom_id'))
                        ->whereHas('suggestProducts', function ($query) use ($supplier) {
                            $query->whereColumn('alert_quantity', '>=', 'stock_reports.stock');
                        })->whereHas('items', function ($query) use ($supplier) {
                            $query->whereHasMorph('itemable', [PurchaseOrder::class], function ($query) use ($supplier) {
                                $query->where('purchasable_id', session()->get('showroom_id'))->where('purchasable_type', ShowRoom::class)
                                    ->where('supplier_id', $supplier);
                            });
                        });


        if ($row_count == "all") {
            $total_number = StockReport::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number,$selected_data);
            } else {
                return $items->latest()->paginate($total_number,$selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count,$selected_data);
            } else {
                return $items->latest()->paginate($row_count,$selected_data);
            }
        }
    }

    public function withPaginateReturn($row_count, $quick_search, $name, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = PurchaseOrder::query();
        $items = $items->with($relational_data)->where('return_status', '!=', 2);
        if (auth()->user()->role->type != "system_user") {
            $items = $items->whereHasMorph('purchasable', ShowRoom::class)->where('purchasable_id', session()->get('showroom_id'));
        }
        if ($quick_search != null) {
            $items = $items->whereLike(['invoice_no', 'date', 'payable_amount', 'total_quantity'], $quick_search)
                ->orWhereHas('supplier', function ($q) use ($quick_search) {
                    return $q->whereLike(['name'], $quick_search);
                });
        }
        if ($name != null) {
            $items = $items->whereLike(['invoice_no'], $name);
        }
        if ($row_count == "all") {
            $total_number = PurchaseOrder::count();
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function withPaginateSupplierInvoice($row_count, $quick_search, $sort, $column, $id, $relational_data = [], $selected_data = ['*'])
    {
        $items = PurchaseOrder::query();
        $items = $items->with($relational_data)->where('status', 1)->where('supplier_id', $id);

        if ($quick_search != null) {
            $items = $items->whereLike(['invoice_no', 'date'], $quick_search)->where('supplier_id', $id);
        }

        if ($row_count == "all") {
            $total_number = PurchaseOrder::where('supplier_id', $id)->count();

            if ($column != null && $column != "undefined") {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null && $column != "undefined") {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function withPaginateSupplierReturnInvoice($row_count, $quick_search, $sort, $column, $id, $relational_data = [], $selected_data = ['*'])
    {
        $items = PurchaseOrder::query();
        $items = $items->with($relational_data)->where('return_status', 1)->where('supplier_id', $id);

        if ($quick_search != null) {
            $items = $items->whereLike(['invoice_no', 'date'], $quick_search)->where('supplier_id', $id);
        }

        if ($row_count == "all") {
            $total_number = PurchaseOrder::where('supplier_id', $id)->where('return_status', 1)->count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function getInvoiceList($partner_id, $search)
    {
        $repo = new SubLeadgerRepository();
        $partner_account = $repo->find($partner_id);
        if ($partner_account->morphable->getTable() == "contacts" && $partner_account->morphable->contact_type == "Supplier") {
            $sales = PurchaseOrder::query();
            $sales->where('supplier_id', $partner_account->morphable->id);
        }
        if ($search != '') {
            $sales->whereLike(['invoice_no'], $search);
        }
        $items = $sales->where('is_paid', '!=', 2)->paginate(10);
        $response = [];
        foreach ($items as $item) {
            $response[]  = [
                'id'    => $item->id,
                'text'  => $item->payments()->exists() ? single_price((float)$item->payable_amount - (float)$item->payments()->where('payment_type','pay')->sum('amount')) . ' - ' . $item->invoice_no : single_price($item->payable_amount) . ' - ' . $item->invoice_no
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function create(array $data)
    {
        $error = '';
        $sender = explode('-', $data['showroom']);
        $otherTaxDetails = explode('-', $data['total_tax']);

        if ($otherTaxDetails[1]  == 0) {
            $otherTaxDetails[1] = null;
        }

        if ($sender[0] == 'warehouse')
            $showroom = WareHouse::find($sender[1]);
        else
            $showroom = ShowRoom::find($sender[1]);

        $documents = array();
        if (!empty($data['documents'])) {
            foreach ($data['documents'] as $file) {
                $name = uniqid() . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/purchase_order/', $name);
                $documents[] = '/uploads/purchase_order/' . $name;
            }
        };

//        Saving Purchase
        $order = PurchaseOrder::create([
            'supplier_id' => $data['supplier_id'],
            'payment_method' => $data['payment_method'],
            'shipping_address' => $data['shipping_address'],
            'notes' => $data['notes'],
            'documents' => $documents,
            'amount' => $data['item_amount'],
            'date' => date('Y-m-d', strtotime($data['date'])),
            'total_quantity' => $data['total_quantity'],
            'total_discount' =>$data['total_discount_amount'],
            'discount_amount' =>  $data['total_discount'],
            'discount_type' => $data['discount_type'],
            'payable_amount' => $data['total_amount'],
            'total_vat' => $data['total_tax'],
            'tax_id' =>  $otherTaxDetails[1],
            'shipping_charge' => $data['shipping_charge'],
            'other_charge' => $data['other_charge'],
            'ref_no' => $data['ref_no'],
            'lc_no' => $data['lc_no'],
            'cnf_id' => $data['cnf_agent'],
            'purchasable_type' => get_class($showroom),
            'purchasable_id' => $showroom->id,
        ]);

//        Saving Items
        if (!empty($data['product_id'])) {
            foreach ($data['product_id'] as $key => $id) {

                $sub_total = $data['product_price'][$key] * $data['product_quantity'][$key];

                $product = new ProductItemDetail([
                    'product_sku_id' => $data['product_id'][$key],
                    'selling_price' => $data['product_selling_price'][$key],
                    'price' => $data['product_price'][$key],
                    'quantity' => $data['product_quantity'][$key],
                    'tax' => $data['product_tax'][$key],
                    'discount' => $data['product_discount'][$key],
                    'sub_total' => $sub_total,
                    'productable_id' => $data['product_id'][$key],
                    'productable_type' => get_class(new ProductSku),
                ]);
                $order->items()->save($product);

                $productHistory = new ProductHistory([
                    'type' => 'purchase',
                    'date' => Carbon::now()->toDateString(),
                    'in_out' => $data['product_quantity'][$key],
                    'product_sku_id' => $data['product_id'][$key],
                    'itemable_id' => $showroom->id,
                    'itemable_type' => get_class($showroom),
                ]);

                $order->houses()->save($productHistory);
            }
        }

        return $order;

    }

    public function find($id)
    {
        return PurchaseOrder::with('items', 'items.product')->findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $error = 1;
        $sender = explode('-', $data['showroom']);
        $otherTaxDetails = explode('-', $data['total_tax']);

         if ($otherTaxDetails[1]  == 0) {
            $otherTaxDetails[1] = null;
        }


        if ($sender[0] == 'warehouse')
            $showroom = WareHouse::find($sender[1]);
        else
            $showroom = ShowRoom::find($sender[1]);

        //Update Purchase
        $order = PurchaseOrder::find($id);
        $order->supplier_id = $data['supplier_id'];
        $order->shipping_address = $data['shipping_address'];
        $order->notes = $data['notes'];
        $order->date = date('Y-m-d', strtotime($data['date']));
        if (!empty($data['documents'])) {
            $documents = array();
            foreach ($data['documents'] as $file) {
                $name = uniqid() . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/purchase_order/', $name);
                $documents[] = '/uploads/purchase_order/' . $name;
            }
            $order->documents = $documents;
        }
        $order->purchasable_id = $showroom->id;
        $order->purchasable_type = get_class($showroom);
        $order->amount = $data['item_amount'];
        $order->total_quantity = $data['total_quantity'];

        $order->total_discount = $data['total_discount_amount'];
        $order->discount_amount = $data['total_discount'];

        $order->discount_type = $data['discount_type'];
        $order->total_vat = $data['total_tax'];
        $order->tax_id = $otherTaxDetails[1];
        $order->shipping_charge = $data['shipping_charge'];
        $order->other_charge = $data['other_charge'];
        $order->payable_amount = $data['total_amount'];
        $order->payable_amount = $data['total_amount'];
        $order->ref_no = $data['ref_no'];
        $order->lc_no = $data['lc_no'];
        $order->cnf_id = $data['cnf_agent'];
        $order->save();

//        Update Items
        if (!empty($data['items'])) {
            foreach ($data['items'] as $key => $cart) {
                $productsku = ProductItemDetail::where('itemable_id', $order->id)->where('itemable_type', get_class(new PurchaseOrder()))
                    ->where('productable_type', get_class(new ProductSku))->where('product_sku_id', $data['items'][$key])->first();
                $decreaseQuantity = $data['item_quantity'][$key] - $productsku->quantity; //req 55 - 50 = 5


                $sub_total = $data['item_price'][$key] * $data['item_quantity'][$key];

                $productsku->update([
                    'price' => $data['item_price'][$key],
                    'selling_price' => $data['item_selling_price'][$key],
                    'quantity' => $data['item_quantity'][$key],
                    'tax' => $data['item_tax'][$key],
                    'discount' => $data['item_discount'][$key],
                    'sub_total' => $sub_total,
                    'productable_id' => $data['items'][$key],
                    'productable_type' => get_class(new ProductSku),
                ]);
                $increaseQuantity = $data['item_quantity'][$key] - $productsku->quantity;


                $history = $order->houses()->where('product_sku_id', $data['items'][$key])->first();
                if ($history) {
                    $history->update(['in_out' => $data['item_quantity'][$key]]);
                }
            }

        }

//        Storing New Items
        if (!empty($data['product_id'])) {
            foreach ($data['product_id'] as $key => $id) {

                $sub_total = $data['item_price'][$key] * $data['item_quantity'][$key];

                $product = new ProductItemDetail([
                    'product_sku_id' => $data['product_id'][$key],
                    'selling_price' => $data['product_selling_price'][$key],
                    'price' => $data['product_price'][$key],
                    'quantity' => $data['product_quantity'][$key],
                    'tax' => $data['product_tax'][$key],
                    'discount' => $data['product_discount'][$key],
                    'sub_total' => $sub_total,
                    'productable_id' => $data['items'][$key],
                    'productable_type' => get_class(new ProductSku),
                ]);
                $order->items()->save($product);

                $productHistory = new ProductHistory([
                    'type' => 'purchase',
                    'date' => Carbon::now()->toDateString(),
                    'in_out' => $data['product_quantity'][$key],
                    'product_sku_id' => $data['product_id'][$key],
                    'itemable_id' => $showroom->id,
                    'itemable_type' => get_class($showroom),
                ]);

                $order->houses()->save($productHistory);
            }
        }
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            foreach ($order->proRefers->whereIn('type', ["pay_cash","pay_bank", "misc"]) as $key => $voucher) {
                $voucherRepo = new ProVoucherRepository();
                $voucherRepo->deleteApproved($voucher->id);
            }
            if (!empty($data['delete_previous_amount'])) {
                $order->payments()->delete();
            }
        } else {
            foreach ($order->refers->whereIn('payment_type',["voucher_payment", "journal_voucher"]) as $key => $voucher) {
                $voucher->transactions()->delete();
                $voucher->delete();
            }
            if (!empty($data['delete_previous_amount'])) {
                $order->payments()->delete();
            }
        }

        $this->approve($order->id);
        return $order;
    }

    public function approve($id)
    {
        $purchase = PurchaseOrder::find($id);

        //Chart account find
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $chart_account = $purchase->supplier->morph;
        } else {
            $chart_account = $this->AccountFind($purchase->supplier_id, get_class(new ContactModel));
        }
        $house = $purchase->purchasable;
        $acoountBalance = $purchase->supplier->accounts['due'];
        $main_amount = $purchase->payable_amount;
        $tax_account_amount = 0;
        $single_item_tax = 0;

        //Deleting histories
        foreach ($purchase->items as $product) {
            $history = ProductHistory::where('type', 'purchase')->where('houseable_id', $product->itemable_id)->where('houseable_type', $product->itemable_type)
                ->where('product_sku_id', $product->product_sku_id)->first();

            if ($history) {
                $history->status = 1;
                $history->save();
            }

            $tax_account_amount += (($product->price - $product->discount) * $product->quantity ) * $product->tax / 100;

            $product_sku_tbl =  ProductSku::find($product->product_sku_id);

            $selling_history = ProductSellingPriceHistory::create([
                'product_sku_id' => $product_sku_tbl->id,
                'purchase_order_id' => $purchase->id,
                'old_price' => $product_sku_tbl->selling_price,
                'new_selling_price' => $product->selling_price,
                'updated_by' => auth()->user()->id,
            ]);
            $product_sku_tbl->update([
                'selling_price' => $product->selling_price,
            ]);
        }

        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $this->proJournalEntryPurchase($purchase, $tax_account_amount, $main_amount);
        } else {
            $sub_account_id[] = ChartAccount::where('code', '01-07')->first()->id;
            $sub_amount[] = $purchase->amount - ($purchase->total_discount + $tax_account_amount);
            $sub_narration[] = 'Product Purchase';

            if ($tax_account_amount > 0) {
                $sub_account_id[] = $this->defaultProductTaxAccount(); //Product Tax
                $sub_amount[] = $tax_account_amount;
                $sub_narration[] = 'Purchase Tax';
            }



            if ($purchase->total_vat > 0) {
                $taxDetails = Tax::findOrFail($purchase->tax_id);
                $sub_account_id[] = $this->othersTaxAccountByTaxId($purchase->tax_id);
                $sub_amount[] = $purchase->amount * $purchase->total_vat / 100;
                $sub_narration[] =  $taxDetails->name.' '.  $taxDetails->rate . 'Tax on Purchase';
            }

            if ($purchase->shipping_charge > 0 || $purchase->other_charge > 0) {
                $sub_account_id[] = $this->shippingOrOthersChargeExpense();
                $sub_amount[] = $purchase->shipping_charge + $purchase->other_charge;
                $sub_narration[] = 'Purchase Expense (Shipping and others charge)';
            }

            $repo = new JournalRepository();
            //      creating journal
            $repo->create([
                'voucher_type' => 'JV',
                'amount' => $main_amount,
                'date' => Carbon::now()->format('Y-m-d'),
                'account_type' => 'credit',
                'payment_type' => 'journal_voucher',
                'account_id' => $chart_account ? $chart_account->id : '',
                'main_amount' => $main_amount,
                'narration' => 'Product Purchase',

                'sub_account_id' => array_reverse($sub_account_id),
                'sub_amount' => array_reverse($sub_amount),
                'sub_narration' => array_reverse($sub_narration),

                'sale_id' => $purchase->id,
                'sale_class' => get_class(new PurchaseOrder),
                'is_approve' => (app('business_settings')->where('type', 'purchase_voucher_approval')->first()->status == 1) ? 1 : 0,
                'is_purchase' => 1,
            ]);

            $voucher = new VoucherRepository();

            foreach ($purchase->payments as $key => $payment) {
                //Transaction Moneydd
                $debit_account_id[] = $this->GetAccountId($purchase->supplier_id, ContactModel::class);

                if ($payment->payment_method == "cash" || $payment->payment_method == "quick cash") {
                    $credit_account_id = $this->GetAccountId($purchase->purchasable_id, $purchase->purchasable_type); //Cash Account
                } else {
                    $credit_account_id = ChartAccount::findOrFail($payment->account_id)->id; //Bank Account
                }
                $totalAmountReceived = $payment->amount + $payment->advance_amount;
                $debit_account_amount[] = ($payment->return_amount > 0) ? $totalAmountReceived  - $payment->return_amount : $totalAmountReceived ;
                $narration[] = 'Purchase Payment';

                // creating voucher
                $voucher->create([
                    'voucher_type' => $payment->payment_method == "bank" ? 'BV' : 'CV',
                    'amount' => ($payment->return_amount > 0) ?$totalAmountReceived  - $payment->return_amount : $totalAmountReceived ,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'payment_type' => 'voucher_payment',
                    'credit_account_id' => $credit_account_id,
                    'credit_account_amount' => ($payment->return_amount > 0) ? $totalAmountReceived  - $payment->return_amount : $totalAmountReceived ,
                    'credit_account_narration' => 'Payment given by ' . $payment->payment_method,
                    'debit_account_id' => $debit_account_id,
                    'debit_account_amount' => $debit_account_amount,
                    'debit_account_narration' => $narration,
                    'narration' => 'Payment given by ' . $payment->payment_method,
                    'cheque_no' => null,
                    'cheque_date' => null,
                    'bank_name' => $payment->payment_method == "bank" ? $payment->bank_name : null,
                    'bank_branch' => $payment->payment_method == "bank" ? $payment->branch : null,
                    'sale_id' => $purchase->id,
                    'sale_class' => get_class(new PurchaseOrder),
                    'is_approve' => (app('business_settings')->where('type', 'purchase_voucher_approval')->first()->status == 1) ? 1 : 0,
                    'is_purchase' => 1,
                ]);
            }
        }

        $purchase->status = 1;

        // saving purchase
        $purchase->save();
        if ($acoountBalance < 0) {
            $extra_amount = abs($chart_account->balanceAmount);
            foreach ($purchase->supplier->purchases->where('is_paid', '!=', 2)->where('is_approved',1) as $key => $order) {
                if ($order->is_paid != 2) {
                    $due_amount = $order->payable_amount - $order->payments()->where('payment_type','pay')->sum('amount');
                    if ($due_amount > 0 && $extra_amount > 0) {

                        if ($extra_amount >= $due_amount) {
                            $purchase_payment = new Payment([
                                'payment_method' => 'Balance adjust',
                                'amount' => $due_amount,
                                'payable_id' => $order->id,
                                'payable_type' => 'Modules\Purchase\Entities\PurchaseOrder',
                            ]);
                            $order->is_paid = 2;
                            $order->payments()->save($purchase_payment);
                            $order->save();
                        }else {
                            $purchase_payment = new Payment([
                                'payment_method' => 'Balance adjust',
                                'amount' => $extra_amount,
                                'payable_id' => $order->id,
                                'payable_type' => 'Modules\Purchase\Entities\PurchaseOrder',
                            ]);
                            $order->is_paid = 1;
                            $order->payments()->save($purchase_payment);
                            $order->save();
                        }
                        $extra_amount -= $due_amount;
                    }
                }
            }
        }
        return $purchase;
    }

    public function delete($id)
    {
        $order = $this->find($id);
        \LogActivity::successLog('Delete Approved - ' . $order->invoice_no, route('activity_log'), "Delete Approved");
        foreach ($order->receiveProducts as $receive_item) {
            $existStock = StockReport::where('houseable_type', $order->purchasable_type)
                ->where('houseable_id', $order->purchasable_id)
                ->where('product_sku_id', $receive_item->product_sku_id)
                ->first();
            if ($existStock) {
                $existStock->update([
                    'stock' => (float)$existStock->stock - (float)$receive_item->receive_quantity,
                    'in' => (float)$existStock->in - (float)$receive_item->receive_quantity,
                ]);
            }
        }
        foreach ($order->costs as $cost) {
            $cost->productSku->update([
                'cost_of_goods' => $cost->previous_cost_of_goods_sold,
            ]);
        }
        foreach ($order->items as $item) {
            $product_sku_tbl =  ProductSku::find($item->product_sku_id);
            $selling_history = ProductSellingPriceHistory::where('purchase_order_id', $order->id)->first();
            if ($selling_history) {
                $selling_history->delete();
            }

            $last_record = ProductItemDetail::where('itemable_type', 'Modules\Purchases\Entities\PurchaseOrder')
                ->where('productable_type', 'Modules\Product\Entities\ProductSku')
                ->where('productable_id', $item->product_sku_id)
                ->latest()->first();
            if ($last_record) {
                $product_sku_tbl->update([
                    'selling_price' => $last_record->final_selling_price,
                    'min_selling_price' => $last_record->selling_price,
                ]);
            }
            $existStock = StockReport::where('houseable_type', $order->purchasable_type)
                ->where('houseable_id', $order->purchasable_id)
                ->where('product_sku_id', $item->product_sku_id)
                ->first();
            if ($existStock) {
                $existStock->update([
                    'stock' => (float)$existStock->stock + (float)$item->return_quantity,
                    'out' => (float)$existStock->out - (float)$item->return_quantity,
                ]);
            }
            $item->part_number()->delete();
            $item->part_number_details()->delete();
        }

        $order->receiveProducts()->delete();
        $order->costs()->delete();
        $order->items()->delete();
        $order->houses()->delete();
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            foreach ($order->proRefers as $key => $voucher) {
                $voucherRepo = new ProVoucherRepository();
                $voucherRepo->deleteApproved($voucher->id);
            }
        } else {
            foreach ($order->refers as $key => $voucher) {
                $voucher->transactions()->delete();
                $voucher->delete();
            }
        }

        $order->payments()->delete();
        $order->delete();
        return "success";
    }

    public function itemList()
    {
        return PurchaseOrder::with('items','items.productSku')->where('return_status', '!=', 2)->latest()->get();
    }

    public function itemUpdate(array $data, $items, $id)
    {
        $purchase = PurchaseOrder::find($id);
        if ($purchase->return_status == 1) {
            $purchase->items()->update(['return_amount' => 0, 'return_quantity' => 0]);
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                foreach ($purchase->proRefers->where('narration', 'Purchase Return Journal') as $key => $voucher) {
                        $voucherRepo = new ProVoucherRepository();
                        $voucherRepo->deleteApproved($voucher->id);
                }
            } else {
                foreach ($purchase->refers->where('narration', 'Purchase Return Account') as $key => $voucher) {
                    $voucher->transactions()->delete();
                    $voucher->delete();
                }
            }
            $purchase->houses()->where('type', 'purchase_return')->delete();
            foreach ($purchase->items as $p_product) {
                $stocks = StockReport::where('houseable_type', $purchase->purchasable_type)->where('houseable_id', $purchase->purchasable_id)->where('product_sku_id', $p_product->product_sku_id)->first();
                $stocks->stock += $p_product->return_quantity;
                $stocks->out -= $p_product->return_quantity;
                $stocks->save();
            }
        }

        foreach ($items as $key => $item) {
            $products = ProductItemDetail::find($item['item_id']);
            if ($products) {
                $decreaseQuantity = $item['quantity'] - $products->return_quantity;
                $products->return_quantity = $item['quantity'];
                // $sub_total = ($products->price * $item['quantity']);
                // $products->return_amount = $sub_total;
                $products->return_amount = $item['return_subtotal']; /* for return subtotal amount issue fix */
                $products->return_date = Carbon::now();
                $products->status = 0;
                $products->save();

                $history = ProductHistory::where('houseable_id', $products->itemable_id)->where('houseable_type', $products->itemable_type)
                    ->where('product_sku_id', $products->product_sku_id)->first();
                if ($history) {
                    $history->in_out -= $decreaseQuantity;
                    $history->status = 0;
                    $history->save();
                    if ($item['quantity'] > 0) {
                        $history = ProductHistory::where('houseable_id', $products->itemable_id)->where('houseable_type', $products->itemable_type)->where('product_sku_id', $products->product_sku_id)->first();
                        $productHistory = new ProductHistory;
                        $productHistory->type = 'purchase_return';
                        $productHistory->date = Carbon::now()->toDateString();
                        $productHistory->in_out = $item['quantity'];
                        $productHistory->product_sku_id = $products->product_sku_id;
                        $productHistory->houseable_id = $products->itemable_id;
                        $productHistory->houseable_type = $products->itemable_type;
                        $productHistory->itemable_id = $history->itemable_id;
                        $productHistory->itemable_type = $history->itemable_type;
                        $productHistory->save();
                    }
                }
            }
        }


        $purchase->total_return_amount = $data['total_return_amount'];
        $purchase->return_status = 0;

        $purchase->save();
        if (app('business_settings')->where('type', 'purchase_return_approval')->first()->status == 1) {
            $this->returnApprove($purchase->id);
        }
    }

    public function returnApprove($id)
    {
        $order = PurchaseOrder::findOrFail($id);
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $this->proJournalEntryPurchaseReturn($order);
        } else {
            $total_amount = 0;
            foreach ($order->items as $product) {
                $history = ProductHistory::where('type', 'purchase_return')->where('houseable_id', $product->itemable_id)->where('houseable_type', $product->itemable_type)
                    ->where('product_sku_id', $product->product_sku_id)->first();
                if ($history) {
                    $history->status = 1;
                    $history->save();
                    $stocks = StockReport::where('houseable_type', $history->itemable_type)->where('houseable_id', $history->itemable_id)->where('product_sku_id', $history->product_sku_id)->first();
                    $stocks->stock -= $history->in_out;
                    $stocks->out += $history->in_out;
                    $stocks->save();
                }
            }
            if ($order->items->sum('return_amount') > 0) {
                $debit_account_id[] = $this->GetAccountId($order->supplier_id, get_class(new ContactModel));
                $debit_account_amount[] = $order->items->sum('return_amount');
                $narration[] = 'Purchase Return Supplier Account';
                $total_amount += $order->items->sum('return_amount');

                $purchase_account = $this->defaultPurchaseReturnAccount(); //Purchase Return Account
                $repo = new JournalRepository();
                $repo->create([
                    'voucher_type' => 'JV',
                    'amount' => $total_amount,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'account_type' => 'credit',
                    'payment_type' => 'journal_voucher',
                    'account_id' => $purchase_account->id,
                    'main_amount' => $total_amount,
                    'narration' => 'Purchase Return Account',

                    'sub_account_id' => $debit_account_id,
                    'sub_amount' => $debit_account_amount,
                    'sub_narration' => $narration,
                    'sale_id' => $order->id,
                    'sale_class' => get_class(new PurchaseOrder),
                    'is_approve' => (app('business_settings')->where('type', 'purchase_return_voucher_approval')->first()->status == 1) ? 1 : 0,
                ]);

                $order->return_status = 1;
            } else {
                $order->return_status = 2;
            }

            $order->save();
        }

        return $order;
    }

    public function payments(array $payments, $id)
    {
        $order = PurchaseOrder::find($id);

        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $this->proJournalEntryPayment($order, $payments, $id);
        } else {
            $total_amount = 0;
            $voucher = new VoucherRepository();

            $paid_amount_before = $order->payments()->where('payment_type','pay')->sum('amount');
            $dueAmount = $order->payable_amount - $paid_amount_before;
            foreach ($payments as $key => $payment) {

                if( $payment['amount'] >= $dueAmount ){
                    if ($dueAmount > 0) {
                        $amount =  $dueAmount;
                        $advance_amount = $payment['amount'] - $amount ;
                    }else {
                        $amount =  0;
                        $advance_amount = $payment['amount'] ;
                    }

                }else{
                    $amount = $payment['amount'] ;
                    $advance_amount =  0;
                }

                $sale_payment = new Payment([
                    'payment_method' => $payment['payment_method'],
                    'amount' => (float)$amount,
                    'payment_type' => 'pay',
                    'advance_amount' => (float)$advance_amount,
                    'account_id' => array_key_exists('account_id', $payment) ? $payment['account_id'] : '',
                    'bank_name' => array_key_exists('bank_name', $payment) ? $payment['bank_name'] : '',
                    'branch' => array_key_exists('branch', $payment) ? $payment['branch'] : '',
                    'account_no' => array_key_exists('account_no', $payment) ? $payment['account_no'] : '',
                    'account_owner' => array_key_exists('account_owner', $payment) ? $payment['account_owner'] : '',
                ]);

                $order->payments()->save($sale_payment);

                if ($order->status != 0) {

                    $debit_account_id = [];
                    $debit_account_amount = [];
                    $narration = [];
                    $txAmountValue = $payment['amount'];


                    $debit_account_id = $this->GetAccountId($order->supplier_id, get_class(new ContactModel));

                    if ($payment['payment_method'] == "cash" || $payment['payment_method'] == "quick cash") {
                        $credit_account_id = $this->GetAccountId($order->purchasable_id, $order->purchasable_type);
                    } else {
                        $credit_account_id = ChartAccount::findOrFail($payment['account_id'])->id;
                    }

                    $tx_amount = (float)$txAmountValue;
                    $debit_account_amount = $tx_amount;
                    $tx_naration = 'Purchase Payment by '. $payment['payment_method'];
                    $narration = $tx_naration;
                    $voucher->create([
                        'voucher_type' => $payment['payment_method'] == "bank" ? 'BV' : 'CV',

                        'amount' => $txAmountValue,
                        'date' => Carbon::now()->format('Y-m-d'),
                        'payment_type' => 'voucher_payment',
                        'credit_account_id' => $credit_account_id,

                        'credit_account_amount' => (float)$txAmountValue,
                        'credit_account_narration' => 'Payment given by ' . $payment['payment_method'],

                        'debit_account_id' => $debit_account_id,
                        'debit_account_amount' => $debit_account_amount,
                        'debit_account_narration' => $narration,
                        'narration' => 'Payment given by ' . $payment['payment_method'],
                        'cheque_no' => null,
                        'cheque_date' => null,
                        'bank_name' => $payment['payment_method'] == "bank" ? $payment['bank_name'] : null,
                        'bank_branch' => $payment['payment_method'] == "bank" ? $payment['branch'] : null,
                        'sale_id' => $order->id,
                        'sale_class' => get_class(new PurchaseOrder),
                        'is_approve' => (app('business_settings')->where('type', 'purchase_voucher_approval')->first()->status == 1) ? 1 : 0,
                        'is_purchase' => 1,
                    ]);
                    $dueAmount -= $payment['amount'];
                }
            }

            $paid_amount = $order->payments()->where('payment_type','pay')->sum('amount');

            // $paid_amount = array_sum(array_column($payments, 'amount')) + $amounts;

            if ($order->payable_amount <= $paid_amount) {
                $order->payments()->where('payment_method', 'quick cash')->update(['return_amount' => $paid_amount - $order->payable_amount]);
                $order->is_paid = 2;
            }
            if ($order->payable_amount > $paid_amount) {
                $order->is_paid = 1;
            }
            $order->save();
        }
    }

    public function returnPayments(array $payments, $id)
    {
        $order = PurchaseOrder::find($id);

        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $this->proJournalEntryPayment($order, $payments, $id);
        } else {
            $total_amount = 0;
            $voucher = new VoucherRepository();

            $paid_amount_before = $order->payments()->where('payment_type','return')->sum('amount');
            $dueAmount = $order->total_return_amount - $paid_amount_before;
            foreach ($payments as $key => $payment) {

                if( $payment['amount'] >= $dueAmount ){
                    if ($dueAmount > 0) {
                        $amount =  $dueAmount;
                        $advance_amount = $payment['amount'] - $amount ;
                    }else {
                        $amount =  0;
                        $advance_amount = $payment['amount'] ;
                    }

                }else{
                    $amount = $payment['amount'] ;
                    $advance_amount =  0;
                }

                $sale_payment = new Payment([
                    'payment_method' => $payment['payment_method'],
                    'amount' => (float)$amount,
                    'payment_type' => 'return',
                    'advance_amount' => (float)$advance_amount,
                    'account_id' => array_key_exists('account_id', $payment) ? $payment['account_id'] : '',
                    'bank_name' => array_key_exists('bank_name', $payment) ? $payment['bank_name'] : '',
                    'branch' => array_key_exists('branch', $payment) ? $payment['branch'] : '',
                    'account_no' => array_key_exists('account_no', $payment) ? $payment['account_no'] : '',
                    'account_owner' => array_key_exists('account_owner', $payment) ? $payment['account_owner'] : '',
                ]);

                $order->payments()->save($sale_payment);

                if ($order->status != 0) {

                    $debit_account_id = [];
                    $debit_account_amount = [];
                    $narration = [];
                    $txAmountValue = $payment['amount'];


                    $credit_account_id = $this->GetAccountId($order->supplier_id, get_class(new ContactModel));

                    if ($payment['payment_method'] == "cash" || $payment['payment_method'] == "quick cash") {
                        $debit_account_id = $this->GetAccountId($order->purchasable_id, $order->purchasable_type);
                    } else {
                        $debit_account_id = ChartAccount::findOrFail($payment['account_id'])->id;
                    }

                    $tx_amount = (float)$txAmountValue;
                    $debit_account_amount = $tx_amount;
                    $tx_naration = 'Purchase Return Payment by '. $payment['payment_method'];
                    $narration = $tx_naration;
                    $voucher->create([
                        'voucher_type' => $payment['payment_method'] == "bank" ? 'BV' : 'CV',

                        'amount' => $txAmountValue,
                        'date' => Carbon::now()->format('Y-m-d'),
                        'payment_type' => 'voucher_payment',

                        'debit_account_id' => $debit_account_id,
                        'debit_account_amount' => (float)$txAmountValue,
                        'debit_account_narration' => 'Purchase Return Payment given by ' . $payment['payment_method'],

                        'credit_account_id' => $credit_account_id,
                        'credit_account_amount' => $debit_account_amount,
                        'credit_account_narration' => $narration,

                        'narration' => 'Purchase Return Payment given by ' . $payment['payment_method'],
                        'cheque_no' => null,
                        'cheque_date' => null,
                        'bank_name' => $payment['payment_method'] == "bank" ? $payment['bank_name'] : null,
                        'bank_branch' => $payment['payment_method'] == "bank" ? $payment['branch'] : null,
                        'sale_id' => $order->id,
                        'sale_class' => get_class(new PurchaseOrder),
                        'is_approve' => (app('business_settings')->where('type', 'purchase_voucher_approval')->first()->status == 1) ? 1 : 0,
                        'is_purchase' => 1,
                    ]);
                    $dueAmount -= $payment['amount'];
                }
            }

            $amounts = $order->payments()->where('payment_type','return')->sum('amount');

            $paid_amount = array_sum(array_column($payments, 'amount')) + $amounts;

            if ($order->total_return_amount <= $paid_amount) {
                $order->payments()->where('payment_method', 'quick cash')->update(['return_amount' => $paid_amount - $order->total_return_amount]);
                $order->is_paid = 2;
            }
            if ($order->total_return_amount > $paid_amount) {
                $order->is_paid = 1;
            }
            $order->save();
        }
    }


    public function addToStock($id, $data)
    {
        $purchase = PurchaseOrder::find($id);
        $house = $purchase->purchasable;

        foreach ($data['product_sku_id'] as $key=> $product_id)
        {
            $receive = new ReceiveProduct();
            $receive->purchase_id = $purchase->id;
            $receive->product_sku_id = $product_id;
            $receive->receive_quantity = $data['quantity'][$key];
            $receive->receive_date	 = Carbon::now();
            $receive->save();
        }

        $flatAmoutDiscount =  $purchase->total_discount / $purchase->items->sum('quantity') ;

        foreach ($purchase->items as $key => $product) {
            $history = ProductHistory::where('type', 'purchase')->where('houseable_id', $product->itemable_id)->where('houseable_type', $product->itemable_type)
                ->where('product_sku_id', $product->product_sku_id)->first();

            $increasedQuantity = $data['quantity'][$key];

            $stock = $house->stocks()->where('product_sku_id', $product->product_sku_id)->first();

            $previous_cost_of_goods_sold = $product->productSku->cost_of_goods;
            if ($stock) {
                $new_cost_of_goods_sold = ($stock->stock * $previous_cost_of_goods_sold + $increasedQuantity * ( $product->price - ($product->discount + $flatAmoutDiscount))) / ($stock->stock + $increasedQuantity);
            } else {
                $new_cost_of_goods_sold = ($increasedQuantity * $product->price) / $increasedQuantity;
            }

            CostOfGoodHistory::create([
                'costable_type' => PurchaseOrder::class,
                'costable_id' => $purchase->id,
                'storeable_type' => $purchase->purchasable_type,
                'storeable_id' => $purchase->purchasable_id,
                'date' => Carbon::now()->toDateString(),
                'product_sku_id' => $product->product_sku_id,
                'previous_remaining_stock' => ($stock) ? $stock->stock : 0,
                'newly_stock' => $increasedQuantity,
                'previous_cost_of_goods_sold' => $previous_cost_of_goods_sold,
                'new_cost_of_goods_sold' => $new_cost_of_goods_sold
            ]);
            $product->productSku->update(['cost_of_goods' => $new_cost_of_goods_sold]);

            if ($stock) {
                $stock->update(['stock' => $stock->stock + $increasedQuantity, 'in' => $stock->in + $increasedQuantity]);
            } else {
                StockReport::create([
                    'houseable_id' => $purchase->purchasable_id,
                    'houseable_type' => $purchase->purchasable_type,
                    'stock' => $increasedQuantity,
                    'in' => $increasedQuantity,
                    'product_sku_id' => $product->product_sku_id
                ]);
            }

            if ($history) {
                $history->status = 1;
                $history->save();
            }

            if (!empty($data['serial_no'])) {
                $serials = explode(',', $data['serial_no'][$key]);
                foreach ($serials as $k => $serial) {
                     if ($serial)
                     {
                         $serial_no = new PartNumber;
                         $serial_no->product_sku_id = $product->product_sku_id;
                         $serial_no->seiral_no = $serials[$k];
                         $serial_no->save();
                     }

                }
            }

            // if (!empty($data['file'])) {
            //     $a = $data['file'][$key]->getRealPath();
            //     foreach (Importer::make('Excel')->load($a)->getCollection()->skip(1) as $ke => $row) {
            //         $serial_no = new PartNumber;
            //         $serial_no->product_sku_id = $product->product_sku_id;
            //         $serial_no->seiral_no = $row[0];
            //         $serial_no->save();

            //     }
            // }
        }
        $received = $purchase->receiveProducts->sum('receive_quantity');

        if ($purchase->total_quantity == $received)
            $purchase->added_to_stock = 1;
        else
            $purchase->added_to_stock = 2;

        $purchase->save();

        return $purchase;
    }

    public function adToStockOpening(array $data)
    {
        $error = '';
        $sender = explode('-', $data['showroom']);

        if ($sender[0] == 'warehouse') {
            $showroom = WareHouse::find($sender[1]);
        } else {
            $showroom = ShowRoom::find($sender[1]);
        }
        $repo = new ProductRepository();
        $productSku = $repo->findSku($data['product_sku_id']);
        $product = $productSku->product;
        $productHistory = new ProductHistory([
            'type' => 'begining',
            'date' => date('Y-m-d', strtotime($data['stock_date'])),
            'in_out' => $data['stock_quantity'],
            'product_sku_id' => $data['product_sku_id'],
            'itemable_id' => $showroom->id,
            'itemable_type' => get_class($showroom),
        ]);
        $product->houses()->save($productHistory);

        if (!empty($data['serial_no'])) {
            $serials = explode(',', $data['serial_no']);
            foreach ($serials as $key => $value) {
                $serial_no = new PartNumber;
                $serial_no->product_sku_id = $productSku->id;
                $serial_no->seiral_no = $serials[$key];
                $serial_no->save();
            }
        }
        if (!empty($data['file'])) {
            $a = $data['file']->getRealPath();
            foreach (Importer::make('Excel')->load($a)->getCollection()->skip(1) as $key => $value) {
                $serial_no = new PartNumber;
                $serial_no->product_sku_id = $productSku->id;
                $serial_no->seiral_no = $value[0];
                $serial_no->save();
            }
        }
        $existStock = StockReport::where('houseable_type', get_class($showroom))->where('houseable_id', $showroom->id)->where('product_sku_id', $data['product_sku_id'])->first();

        $previous_cost_of_goods_sold = $productSku->cost_of_goods;
        if ($existStock) {
            $new_cost_of_goods_sold = ($existStock->stock * $previous_cost_of_goods_sold + $data['stock_quantity'] * $data['purchase_price']) / ($existStock->stock + $data['stock_quantity']);
        } else {
            $new_cost_of_goods_sold = ($data['stock_quantity'] * $data['purchase_price']) / $data['stock_quantity'];
        }

        CostOfGoodHistory::create([
            'costable_type' => ProductHistory::class,
            'costable_id' => $productHistory->id,
            'storeable_type' => get_class($showroom),
            'storeable_id' => $showroom->id,
            'date' => Carbon::now()->toDateString(),
            'product_sku_id' => $data['product_sku_id'],
            'previous_remaining_stock' => ($existStock) ? $existStock->stock : 0,
            'newly_stock' => $data['stock_quantity'],
            'previous_cost_of_goods_sold' => $previous_cost_of_goods_sold,
            'new_cost_of_goods_sold' => $new_cost_of_goods_sold
        ]);
        $productSku->update([
            'cost_of_goods' => $new_cost_of_goods_sold,
            'purchase_price' => ($data['purchase_price'] > 0) ? $data['purchase_price'] : $new_cost_of_goods_sold,
            'selling_price' => $data['selling_price'],
        ]);

        if ($existStock) {
            $existStock->update(['stock' => $existStock->stock + $data['stock_quantity'], 'in' => $existStock->in + $data['stock_quantity']]);
        } else {
            $stocks = new StockReport([
                'stock' => $data['stock_quantity'],
                'in' => $data['stock_quantity'],
                'product_sku_id' => $data['product_sku_id'],
                'houseable_id' => $showroom->id,
                'houseable_type' => get_class($showroom),
                'stock_date' => date('Y-m-d', strtotime($data['stock_date'])),
            ]);
            $showroom->stocks()->save($stocks);
        }

        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $productPrice = ($data['purchase_price'] * $productSku->tax) / 100 + $data['purchase_price'];
            $main_amount = $productPrice * $data['stock_quantity'];

            $credit_amounts[] = $main_amount;
            $credit_account_id[] = Settings('default_capital_account');
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
            $credit_narration[] = "Add Openning Stock Entry - " . $showroom->name;

            $debit_amounts[] = $main_amount;
            $debit_account_id[] = Leadger::where('id', 7)->first()->id;
            $debit_partner_id[] = 0;
            $debit_cash_flow_account_id[] = 0;
            $debit_narration[] = "Openning Stock - " . $showroom->name;
            $journalRecieveRepository = new ProJournalRepository();
            $voucher = $journalRecieveRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount'=> $credit_amounts[0],
                'date'=> Carbon::now()->format('Y-m-d'),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> "Add Openning Stock Entry Journal",
                'referable_type'=> get_class($productHistory),
                'referable_id'=> $productHistory->id,
                'is_invoiced'=> 0,
                'is_manual_entry'=> 0,

                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => 1,
                'sale_or_purchase' => null,
                'ref_no' => null,
            ]);
        } else {
            $productPrice = ($data['purchase_price'] * $productSku->tax) / 100 + $data['purchase_price'];
            $main_amount = $productPrice * $data['stock_quantity'];
            $sub_account_id[] = ChartAccount::where('code', '02-09-11')->first()->id;
            $sub_amount[] = $main_amount;
            $sub_narration[] = 'Beginning Stock Added By Branch - ' . $showroom->name;

            $repo = new JournalRepository();
            $repo->create([
                'voucher_type' => 'JV',
                'amount' => $main_amount,
                'date' => Carbon::now()->format('Y-m-d'),
                'account_type' => 'debit',
                'payment_type' => 'journal_voucher',
                'account_id' => $this->defaultPurchaseAccount(),
                'main_amount' => $main_amount,
                'narration' => 'Beginning Stock Added By Branch',
                'sub_account_id' => $sub_account_id,
                'sub_amount' => $sub_amount,
                'sub_narration' => $sub_narration,
                'sale_id' => null,
                'sale_class' => null,
                'is_approve' => (app('business_settings')->where('type', 'beginning_stock_voucher_approval')->first()->status == 1) ? 1 : 0,
            ]);
        }
        return $productSku;
    }

    public function purchasePayments($type)
    {
        if (session()->get('showroom_id') == 1)
        {
            return PurchaseOrder::where('status', 1)->get(['id','payable_amount','amount']);
        }
        else
        {
            return PurchaseOrder::where('purchasable_type', ShowRoom::class)->where('purchasable_id', session()->get('showroom_id'))->get(['id','payable_amount','amount']);
        }
    }

    public function purchaseDue($type)
    {
        if (session()->get('showroom_id') == 1)
        {
            return Payment::whereHasMorph('payable', PurchaseOrder::class, function ($query) {
                $query->where('status', 1);
            })->Payment($type)->get(['id','amount']);
        }
        else
        {
            return Payment::whereHasMorph('payable', PurchaseOrder::class, function ($query) {
                $query->where('status', 1)->where('purchasable_type', ShowRoom::class)
                    ->where('purchasable_id', session()->get('showroom_id'));
            })->Payment($type)->get(['id','amount']);
        }
    }

    public function supplierProducts($supplier)
    {
        return StockReport::with('suggestProducts')->where('houseable_type', 'Modules\Inventory\Entities\ShowRoom')
            ->where('houseable_id', session()->get('showroom_id'))->whereHas('suggestProducts', function ($query) use ($supplier) {
                $query->whereColumn('alert_quantity', '>=', 'stock_reports.stock');
            })->whereHas('items', function ($query) use ($supplier) {
                $query->whereHasMorph('itemable', [PurchaseOrder::class], function ($query) use ($supplier) {
                    $query->where('purchasable_id', session()->get('showroom_id'))->where('purchasable_type', 'Modules\Inventory\Entities\ShowRoom')
                        ->where('supplier_id', $supplier);
                });
            })->get();
    }

    private function proJournalEntryPurchaseReturn($order)
    {
        $total_amount = 0;
        $total_tax_amount = 0;
        $tax_amount = 0;
        $price_after_discount = 0;
        foreach ($order->items as $product) {
            $history = ProductHistory::where('type', 'purchase_return')->where('houseable_id', $product->itemable_id)->where('houseable_type', $product->itemable_type)
                ->where('product_sku_id', $product->product_sku_id)->first();
            if ($history) {
                $history->status = 1;
                $history->save();
                $stocks = StockReport::where('houseable_type', $history->itemable_type)->where('houseable_id', $history->itemable_id)->where('product_sku_id', $history->product_sku_id)->first();
                $stocks->stock -= $history->in_out;
                $stocks->out += $history->in_out;
                $stocks->save();
            }

            // $tax_amount = ((float)$price_after_discount / 100 * (float)$product_item_detail->tax) * (float)$product->rtn_qty;
            // $total_tax_amount += $tax_amount;
            // $credit_account_id[] = $this->defaultProductTaxAccount();
            // $credit_partner_id[] = 0;
            // $credit_cash_flow_account_id[] = 0;
            // $credit_amounts[] = $tax_amount;
            // $credit_narration[] = 'Product Tax Purchase Return';
        }

        if ($order->items->sum('return_amount') > 0) {

            $debit_account_id[] = $this->PartnerAccountFind($order->supplier_id, get_class(new ContactModel))->leadger_id;
            $debit_partner_id[] = $this->PartnerAccountFind($order->supplier_id, get_class(new ContactModel))->id;
            $debit_cash_flow_account_id[] = 0;
            $debit_account_amount[] = $order->items->sum('return_amount');
            $debit_narration[] = 'Purchase Return Supplier Account';
            $total_amount += floatval($order->items->sum('return_amount'));

            $credit_account_id[] = $this->defaultPurchaseReturnAccount();
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
            $credit_amounts[] = $total_amount;
            $credit_narration[] = 'Purchase Return Supplier Account';

            $is_approved = 1;

            $journalRecieveRepository = new ProJournalRepository();
            $voucher = $journalRecieveRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount' => $total_amount,
                'date' => Carbon::now()->format('Y-m-d'),
                'credit_account_id' => $credit_account_id,
                'credit_sub_account_id' => $credit_partner_id,
                'credit_cash_flow_account_id' => $credit_cash_flow_account_id,
                'credit_account_amount' => $credit_amounts,
                'credit_narration' => $credit_narration,
                'narration_voucher' => "Purchase Return Journal",
                'referable_type' => get_class($order),
                'referable_id' => $order->id,
                'is_invoiced' => 0,
                'is_manual_entry' => 0,

                'debit_account_id' => $debit_account_id,
                'debit_sub_account_id' => $debit_partner_id,
                'debit_cash_flow_account_id' => $debit_cash_flow_account_id,
                'debit_account_amount' => $debit_account_amount,
                'debit_narration' => $debit_narration,
                'is_approve' => $is_approved,
                'sale_or_purchase' => "p",
                'ref_no' => null,
            ]);

            $order->return_status = 1;
        } else {
            $order->return_status = 2;
        }
        $order->save();
    }

    private function proJournalEntryPayment($order, $payments, $id)
    {
        $total_amount = 0;
        $has_discount = 0;
        $total_discount_amount = 0;
        $total_with_discount = 0;
        $original_amount = 0;
        $paid_amount_before = (float)$order->payments()->where('payment_type','pay')->sum('amount');
        $dueAmount = (float)$order->payable_amount - $paid_amount_before;
        foreach ($payments as $key => $payment) {

            if ($payment['amount'] >= $dueAmount) {
                if ($dueAmount > 0) {
                    $amount =  $dueAmount;
                    $advance_amount = (float)$payment['amount'] - $amount;
                } else {
                    $amount =  0;
                    $advance_amount = $payment['amount'];
                }
            } else {
                $amount = $payment['amount'];
                $advance_amount =  0;
            }

            $sale_payment = new Payment([
                'payment_method' => $payment['payment_method'],
                'amount' => (float)$amount,
                'advance_amount' => (float)$advance_amount,
                'account_id' => array_key_exists('account_id', $payment) ? $payment['account_id'] : '',
                'bank_name' => array_key_exists('bank_name', $payment) ? $payment['bank_name'] : '',
                'branch' => array_key_exists('branch', $payment) ? $payment['branch'] : '',
                'account_no' => array_key_exists('account_no', $payment) ? $payment['account_no'] : '',
                'account_owner' => array_key_exists('account_owner', $payment) ? $payment['account_owner'] : '',
            ]);

            $order->payments()->save($sale_payment);

            if ($order->status != 0) {

                $txAmountValue = (float)$payment['amount'];
                $total_amount += $txAmountValue;

                if ($payment['payment_method'] == "cash" || $payment['payment_method'] == "quick cash") {
                    $credit_purchase_account_id[] = $this->GetAccountId($order->purchasable_id, $order->purchasable_type)->id; //Cash Account
                } else {
                    $credit_purchase_account_id[] = Leadger::findOrFail($payment['account_id'])->id; //Bank Account
                }

                $credit_purchase_sub_account_id[] = 0;
                $credit_purchase_cash_flow_account_id[] = Settings('default_purchase_cash_flow_account');
                $credit_purchase_account_amount[] = $txAmountValue;
                $credit_purchase_narration[] = 'Purchase Payment';
            }
        }
        if ($order->status != 0) {
            if ($total_discount_amount > 0) {
                $credit_purchase_account_id[] = Settings('purchase_discount_recieve_at_payment_time');
                $credit_purchase_sub_account_id[] = 0;
                $credit_purchase_cash_flow_account_id[] = 0;
                $credit_purchase_account_amount[] = $total_discount_amount;
                $credit_purchase_narration[] = 'Purchase Discount on Payment';
            }
            $debit_purchase_amounts[] = $total_amount + $total_discount_amount;
            $debit_purchase_account_id[] = $this->PartnerAccountFind($order->supplier_id, ContactModel::class)->leadger_id;
            $debit_purchase_partner_id[] = $this->PartnerAccountFind($order->supplier_id, ContactModel::class)->id;
            $debit_purchase_narration[] = 'Payment From For Purchase Purpose';
            $debit_purchase_cash_flow_account_id[] = 0;

            $is_approved = 1;


            $journalRecieveRepository = new ProJournalRepository();
            $voucher = $journalRecieveRepository->create([
                'type' => $payment['payment_method'] == "bank" ? 'pay_bank' : 'pay_cash',
                'is_cash_flow_journal' => 0,
                'amount' => $total_amount + $total_discount_amount,
                'date' => Carbon::now()->format('Y-m-d'),
                'credit_account_id' => $credit_purchase_account_id,
                'credit_sub_account_id' => $credit_purchase_sub_account_id,
                'credit_cash_flow_account_id' => $credit_purchase_cash_flow_account_id,
                'credit_account_amount' => $credit_purchase_account_amount,
                'credit_narration' => $credit_purchase_narration,
                'narration_voucher' => 'Purchase Payment',
                'referable_type' => get_class($order),
                'referable_id' => $order->id,
                'is_invoiced' => 0,
                'is_manual_entry' => 0,

                'debit_account_id' => $debit_purchase_account_id,
                'debit_sub_account_id' => $debit_purchase_partner_id,
                'debit_cash_flow_account_id' => $debit_purchase_cash_flow_account_id,
                'debit_account_amount' => $debit_purchase_amounts,
                'debit_narration' => $debit_purchase_narration,
                'is_approve' => $is_approved,
                'sale_or_purchase' => "p",
                'ref_no' => $order->invoice_no,
            ]);
        }

        $amounts = $order->payments()->where('payment_type','pay')->sum('amount');

        $paid_amount = array_sum(array_column($payments, 'amount')) + $amounts;

        if ($order->payable_amount <= $paid_amount) {
            $order->payments()->where('payment_method', 'quick cash')->update(['return_amount' => (float)$paid_amount - (float)$order->payable_amount]);
            $order->is_paid = 2;
        }
        if ($order->payable_amount > $paid_amount) {
            $order->is_paid = 1;
        }
        $order->save();
    }

    private function proJournalEntryPurchase($purchase,$tax_account_amount,$main_amount)
    {
        $journalRepository = new JournalRepository();
        $debit_account_id[] = Settings('default_purchase_account');
        $debit_partner_id[] = 0;
        $debit_cash_flow_account_id[] = 0;
        $debit_amount[] = (float)$purchase->amount - ((float)$purchase->total_discount + $tax_account_amount);
        $debit_narration[] = 'Product Purchase';

        if ($tax_account_amount > 0) {
            $debit_account_id[] = $this->defaultProductTaxAccount();
            $debit_partner_id[] = 0;
            $debit_cash_flow_account_id[] = 0;
            $debit_amount[] = $tax_account_amount;
            $debit_narration[] = 'Purchase Tax';
        }

        if ($purchase->total_vat > 0) {
            $taxDetails = Tax::findOrFail($purchase->tax_id);
            $debit_account_id[] = $this->GetAccountId($purchase->tax_id, 'Modules\Setup\Entities\Tax')->id;
            $debit_partner_id[] = 0;
            $debit_cash_flow_account_id[] = 0;
            $debit_amount[] = (float)$purchase->amount * (float)$purchase->total_vat / 100;
            $debit_narration[] =  $taxDetails->name . ' ' .  $taxDetails->rate . 'Tax on Purchase';
        }

        if ($purchase->shipping_charge > 0 || $purchase->other_charge > 0) {
            $debit_account_id[] = $this->shippingOrOthersChargeExpense();
            $debit_partner_id[] = 0;
            $debit_cash_flow_account_id[] = 0;
            $debit_amount[] = (float)$purchase->shipping_charge + (float)$purchase->other_charge;
            $debit_narration[] = 'Purchase Expense (Shipping and others charge)';
        }

        $chart_account = $this->PartnerAccountFind($purchase->supplier_id, get_class(new ContactModel));

        $credit_account_id[] = $chart_account->leadger_id;
        $credit_partner_id[] = $chart_account->id;
        $credit_cash_flow_account_id[] = 0;
        $credit_amounts[] = $main_amount;
        $credit_narration[] = 'Purchase Entry';

        $is_approved = 1;

        $journalRecieveRepository = new ProJournalRepository();
        $voucher = $journalRecieveRepository->create([
            'type' => "misc",
            'is_cash_flow_journal' => 0,
            'amount' => $main_amount,
            'date' => Carbon::now()->format('Y-m-d'),
            'credit_account_id' => $credit_account_id,
            'credit_sub_account_id' => $credit_partner_id,
            'credit_cash_flow_account_id' => $credit_cash_flow_account_id,
            'credit_account_amount' => $credit_amounts,
            'credit_narration' => $credit_narration,
            'narration_voucher' => "Purchase Entry",
            'referable_type' => get_class($purchase),
            'referable_id' => $purchase->id,
            'is_invoiced' => 0,
            'is_manual_entry' => 0,

            'debit_account_id' => $debit_account_id,
            'debit_sub_account_id' => $debit_partner_id,
            'debit_cash_flow_account_id' => $debit_cash_flow_account_id,
            'debit_account_amount' => $debit_amount,
            'debit_narration' => $debit_narration,
            'is_approve' => $is_approved,
            'sale_or_purchase' => "p",
            'ref_no' => $purchase->invoice_no,
        ]);

        $journalRepository = new JournalRepository();
        foreach ($purchase->payments as $key => $payment) {
            //Transaction Money
            $totalAmountReceived = (float)$payment->amount + (float)$payment->advance_amount;

            $main_amount = ($payment->return_amount > 0) ? (float)$totalAmountReceived  - (float)$payment->return_amount : $totalAmountReceived;

            if ($payment->payment_method == "cash" || $payment->payment_method == "quick cash") {
                $credit_purchase_account_id[] = $this->GetAccountId($purchase->purchasable_id, $purchase->purchasable_type)->id; //Cash Account
            } else {
                $credit_purchase_account_id[] = Leadger::findOrFail($payment->account_id)->id; //Bank Account
            }

            $credit_purchase_sub_account_id[] = 0;
            $credit_purchase_cash_flow_account_id[] = Settings('default_purchase_cash_flow_account');
            $credit_purchase_account_amount[] = $main_amount;
            $credit_purchase_narration[] = 'Purchase Payment';



            $debit_purchase_amounts[] = $main_amount;
            $debit_purchase_account_id[] = $this->PartnerAccountFind($purchase->supplier_id, ContactModel::class)->leadger_id;
            $debit_purchase_partner_id[] = $this->PartnerAccountFind($purchase->supplier_id, ContactModel::class)->id;
            $debit_purchase_narration[] = 'Payment From ' . $payment->payment_method . ' For Purchase Purpose';
            $debit_purchase_cash_flow_account_id[] = 0;

            $is_approved = 1;

            $voucher = $journalRecieveRepository->create([
                'type' => $payment->payment_method == "bank" ? 'pay_bank' : 'pay_cash',
                'is_cash_flow_journal' => 0,
                'amount' => $main_amount,
                'date' => Carbon::now()->format('Y-m-d'),
                'credit_account_id' => $credit_purchase_account_id,
                'credit_sub_account_id' => $credit_purchase_sub_account_id,
                'credit_cash_flow_account_id' => $credit_purchase_cash_flow_account_id,
                'credit_account_amount' => $credit_purchase_account_amount,
                'credit_narration' => $credit_purchase_narration,
                'narration_voucher' => 'Purchase Payment Done',
                'referable_type' => get_class($purchase),
                'referable_id' => $purchase->id,
                'is_invoiced' => 0,
                'is_manual_entry' => 0,

                'debit_account_id' => $debit_purchase_account_id,
                'debit_sub_account_id' => $debit_purchase_partner_id,
                'debit_cash_flow_account_id' => $debit_purchase_cash_flow_account_id,
                'debit_account_amount' => $debit_purchase_amounts,
                'debit_narration' => $debit_purchase_narration,
                'is_approve' => $is_approved,
                'sale_or_purchase' => "p",
                'ref_no' => $purchase->invoice_no,
            ]);
        }
    }
}
