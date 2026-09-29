<?php

namespace Modules\Sale\Repositories;

use Carbon\Carbon;
use App\Traits\Accounts;
use Modules\Sale\Entities\Payment;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Entities\ProductSku;
use Modules\Product\Entities\PartNumber;
use Modules\Inventory\Entities\WareHouse;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Product\Entities\ComboProduct;
use Modules\Product\Entities\ProductHistory;
use Modules\Inventory\Entities\StockReport;
use Modules\Purchase\Entities\ProductItemDetail;
use Modules\Account\Repositories\VoucherRepository;
use Modules\Account\Entities\ChartAccount;
use Modules\Contact\Entities\ContactModel;
use Modules\Sale\Entities\Sale;
use Modules\ProAccount\Entities\Leadger;
use Modules\Account\Repositories\JournalRepository;
use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;
use Modules\ProAccount\Repositories\VoucherRepository as ProVoucherRepository;
use Modules\Product\Entities\ProductItemDetailPartNumber;
use App\Repositories\UserRepository;
use Modules\Sale\Exports\SaleExport;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Setup\Entities\Tax;

class PosOrderRepository implements PosOrderRepositoryInterface
{
    use Accounts;

    public function all()
    {
        if (auth()->user()->role->type == "system_user") {
            return Sale::with('user', 'customer', 'agentuser', 'items')
                ->where('type', 2)
                ->latest()
                ->get();
        } else {
            return Sale::with('user', 'customer', 'agentuser', 'items')
                ->whereHasMorph('saleable', 'Modules\Inventory\Entities\ShowRoom')
                ->where('saleable_id', request()->get('showroom_id', session()->get('showroom_id')))
                ->where('type', 2)
                ->latest()
                ->get();
        }
    }

    public function csvDownload($type, $is_draft, $is_approved, $data)
    {
        if (file_exists(public_path("uploads/csv/pos_sales_list.xlsx"))) {
          unlink(public_path("uploads/csv/pos_sales_list.xlsx"));
        }
        return Excel::store(new SaleExport($type, $is_draft, $is_approved, $data), 'uploads/csv/pos_sales_list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$name,$sort,$column,$is_draft,$is_approved, $relational_data = [], $selected_data = ['*'])
    {
        $items = Sale::query();
        $items = $items->with($relational_data)
                        ->where('type', 2);
        if (auth()->user()->role->type != "system_user") {
            $items = $items->whereHasMorph('saleable', 'Modules\Inventory\Entities\ShowRoom')
                            ->where('saleable_id', request()->get('showroom_id', session()->get('showroom_id')));
        }

        if ($is_draft == "draft") {
            $items = $items->where('is_draft',1);
        }
        if ($is_draft == "no_draft") {
            $items = $items->where('is_draft',0);
        }
        if ($is_approved == "not_approved") {
            $items = $items->where('is_approved',0);
        }
        if ($quick_search != null) {
            $items = $items->whereLike(['invoice_no','payable_amount','date'] ,$quick_search)->where('type', 2);
        }
        if ($row_count == "all") {
            $total_number = Sale::where('type', 2)->count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function allListQuery($search_keyword,$filter_date)
    {
        if (auth()->user()->role->type == "system_user") {
            if (isset($search_keyword) && $search_keyword !=null) {
                return Sale::with('user', 'customer', 'agentuser', 'items')
                    ->where('type', 2)
                    ->whereLike(['customer.name','agentuser.name','user.name', 'payable_amount', 'created_at', 'invoice_no'], $search_keyword)
                    ->when($filter_date, function($query) use($filter_date){
                        $query->whereBetween('date',filterDateFormatingForSearchQuery($filter_date));
                    })
                    ->latest();
            }else {
                return Sale::with('user', 'customer', 'agentuser', 'items')
                    ->where('type', 2)
                    ->when($filter_date, function($query) use($filter_date){
                        $query->whereBetween('date',filterDateFormatingForSearchQuery($filter_date));
                    })
                    ->latest();
            }
        } else {
            if (isset($search_keyword) && $search_keyword !=null) {
                return Sale::with('user', 'customer', 'agentuser', 'items')
                    ->whereHasMorph('saleable', 'Modules\Inventory\Entities\ShowRoom')
                    ->where('saleable_id', request()->get('showroom_id', session()->get('showroom_id')))
                    ->where('type', 2)
                    ->whereLike(['customer.name','agentuser.name','user.name', 'payable_amount', 'created_at', 'invoice_no'], $search_keyword)
                    ->when($filter_date, function($query) use($filter_date){
                        $query->whereBetween('date',filterDateFormatingForSearchQuery($filter_date));
                    })
                    ->latest();
            }else {
                return Sale::with('user', 'customer', 'agentuser', 'items')
                    ->whereHasMorph('saleable', 'Modules\Inventory\Entities\ShowRoom')
                    ->where('saleable_id', request()->get('showroom_id', session()->get('showroom_id')))
                    ->when($filter_date, function($query) use($filter_date){
                        $query->whereBetween('date',filterDateFormatingForSearchQuery($filter_date));
                    })
                    ->where('type', 2)
                    ->latest();
            }
        }
    }

    public function allPending()
    {
        if (auth()->user()->role->type == "system_user") {
            return Sale::with('user', 'customer', 'agentuser', 'items')
                        ->where('type', 2)
                        ->where('is_approved', 0)
                        ->latest()
                        ->get();
        } else {
            return Sale::with('user', 'customer', 'agentuser', 'items')
                        ->whereHasMorph('saleable', 'Modules\Inventory\Entities\ShowRoom')
                        ->where('saleable_id', request()->get('showroom_id', session()->get('showroom_id')))
                        ->where('type', 2)
                        ->where('is_approved', 0)
                        ->latest()
                        ->get();
        }
    }

    public function draftall()
    {
        return Sale::with('customer','user')
                    ->where('type',2)
                    ->where('is_draft',1)
                    ->latest()
                    ->take(10)
                    ->get();
    }

    public function create(array $data)
    {
        $error =1;
        if (!empty($data['totall_bill']) && $data['draft'] == null) {
            $payable_amount = $data['totall_bill'];
            $t_amount = $data['total_amount'];
        }
        elseif (!empty($data['totall_bill']) && $data['draft'] != null) {
            $payable_amount = $data['total_amount'];
            $t_amount = $data['amounts'];
        }
        else{
            $payable_amount = $data['total_amount'];
            $t_amount = $data['amounts'];
        }
        $tax = explode('-', $data['vat']);
        $user_type = explode('-', $data['customer_id']);
        $repo = new UserRepository();
         $pos = Sale::create([
            'customer_id' => $user_type[0] == "customer" ? $user_type[1] : null,
            'agent_user_id' => $user_type[0] == "retailer" ? @$repo->findAgentByID($user_type[1])->user_id : null,
            'date' => Carbon::now(),
            'user_id' => Auth::id(),
            'type' => 2,
            'is_approved' => 0,
            'total_quantity' => $data['total_quantity'],
            'total_tax' => $tax[0],
            'tax_id' => $tax[1],
            'total_discount' => $data['total_discount'],
            'payable_amount' => $payable_amount,
            'amount' => $t_amount,
            'notes' => $data['note'],
            'shipping_charge' => $data['shipping_charge'],
            'saleable_id' => request()->get('showroom_id', session()->get('showroom_id')),
            'saleable_type' => 'Modules\Inventory\Entities\ShowRoom',
            'is_draft' => $data['draft'] != null ? 1 : 0,
        ]);

        if (!empty($data['gst_group'])) {
            $gst_group = $data['gst_group'];
            if (strtolower($data['gst_group']) == "igst") {
                $tax_rate = $data['product_igst'];
            }
            if (strtolower($data['gst_group']) == "cgst") {
                $tax_rate = $data['product_cgst'];
            }
            if (strtolower($data['gst_group']) == "sgst") {
                $tax_rate = $data['product_sgst'];
            }
            if (strtolower($data['gst_group']) == "cess") {
                $tax_rate = $data['product_cess'];
            }
        }else{
            $gst_group = null;
        }
        $house = ShowRoom::find(request()->get('showroom_id', session()->get('showroom_id')));
        if (!empty($data['product_id'])) {
            $total_amount = 0;
            foreach ($data['product_id'] as $key => $id) {
                $sku_item = ProductSku::find($id);
                if ($sku_item->product->product_type != "Service") {
                    $stock = $house->stocks->where('product_sku_id', $id)->first();
                    if ($stock){
                        if ($stock->stock >= $data['quantity'][$key])
                        {
                            $sub_total = ( floatval($data['product_price'][$key]) - (floatval($data['product_price'][$key]) * floatval($data['product_discount'][$key])/100)) * floatval($data['quantity'][$key]);

                            $price = floatval($data['product_price'][$key]);

                            $sub_total = (($price * floatval($data['quantity'][$key])));
                            $total_amount +=$sub_total;
                            $product = new ProductItemDetail([
                                'product_sku_id' => $data['product_id'][$key],
                                'price' => $price,
                                'tax' => $data['product_tax'][$key],
                                'gst_group' => $gst_group,
                                'igst' => ($gst_group != null) ? $data['product_igst'][$key] : 0,
                                'cgst' => ($gst_group != null) ? $data['product_cgst'][$key] : 0,
                                'sgst' => ($gst_group != null) ? $data['product_sgst'][$key] : 0,
                                'cess' => ($gst_group != null) ? $data['product_cess'][$key] : 0,
                                'cogs' => ($sku_item->cost_of_goods > 0) ? $sku_item->cost_of_goods : $sku_item->purchase_price,
                                'discount' => $data['product_discount'][$key],
                                'quantity' => $data['quantity'][$key],
                                'sub_total' => $sub_total,
                                'productable_id' => $data['product_id'][$key],
                                'productable_type' => ProductSku::class,
                            ]);
                            $pos->items()->save($product);
                            if (!empty($data['serial_no'])) {
                                if (!empty(ProductSku::find($data['product_id'][$key])->part_numbers->whereIn('id', $data['serial_no'])->pluck('id'))) {
                                    $part_ids = ProductSku::find($data['product_id'][$key])->part_numbers->whereIn('id', $data['serial_no'])->pluck('id');
                                    foreach ($part_ids as $ki => $part_number_only) {
                                        $part_number_detail = new ProductItemDetailPartNumber;
                                        $part_number_detail->part_number_id = $part_ids[$ki];
                                        $part_number_detail->sale_id = $pos->id;
                                        $part_number_detail->product_sku_id = $data['product_id'][$key];
                                        $part_number_detail->product_item_detail_id = $product->id;
                                        $part_number_detail->save();
                                    }
                                }
                            }
                            if ($data['draft'] == null) {
                                $productHistory = new ProductHistory([
                                    'type' => 'pos',
                                    'date' => Carbon::now()->toDateString(),
                                    'in_out' => $data['quantity'][$key],
                                    'product_sku_id' => $data['product_id'][$key],
                                    'itemable_id' => $data['product_id'][$key],
                                    'itemable_type' => ProductSku::class,
                                ]);
                                $pos->houses()->save($productHistory);
                            }
                        }
                        else{
                            return $error;
                        }
                    }
                    else{
                        return $error;
                    }
                } else {
                    $sub_total = ( floatval($data['product_price'][$key]) - (floatval($data['product_price'][$key]) * floatval($data['product_discount'][$key])/100)) * floatval($data['quantity'][$key]);

                    $price = floatval($data['product_price'][$key]);

                    $sub_total = (($price * floatval($data['quantity'][$key])));
                    $total_amount +=$sub_total;
                    $product = new ProductItemDetail([
                        'product_sku_id' => $data['product_id'][$key],
                        'price' => $price,
                        'tax' => $data['product_tax'][$key],
                        'gst_group' => $gst_group,
                        'igst' => ($gst_group != null) ? $data['product_igst'][$key] : 0,
                        'cgst' => ($gst_group != null) ? $data['product_cgst'][$key] : 0,
                        'sgst' => ($gst_group != null) ? $data['product_sgst'][$key] : 0,
                        'cess' => ($gst_group != null) ? $data['product_cess'][$key] : 0,
                        'cogs' => ($sku_item->cost_of_goods > 0) ? $sku_item->cost_of_goods : $sku_item->purchase_price,
                        'discount' => $data['product_discount'][$key],
                        'quantity' => $data['quantity'][$key],
                        'sub_total' => $sub_total,
                        'productable_id' => $data['product_id'][$key],
                        'productable_type' => ProductSku::class,
                    ]);
                    $pos->items()->save($product);
                }
            }
            $pos->save();

        }
        if (!empty($data['combo_product_id'])) {
            foreach ($data['combo_product_id'] as $key => $id) {
                $combo = ComboProduct::find($id);
                foreach ($combo->combo_products as $c_product_detail) {
                    $stock = $house->stocks()->where('product_sku_id', $c_product_detail->product_sku_id)->first();
                    $in_out = floatval($data['combo_product_quantity'][$key]) * $c_product_detail->product_qty;
                    if ($stock) {
                        if ($stock->stock >= $in_out)
                        {
                            if ($data['draft'] == null) {
                                $productHistory = new ProductHistory([
                                    'type' => 'sales',
                                    'date' => Carbon::now()->toDateString(),
                                    'in_out' => $in_out, //Kaj krte hobe qty niye ekhane
                                    'product_sku_id' => $c_product_detail->product_sku_id, //Kaj krte hobe qty niye ekhane
                                    'itemable_id' => $house->id,
                                    'itemable_type' => get_class($house),
                                ]);
                                $pos->houses()->save($productHistory);
                            }
                        }
                        else{
                            return $error;
                        }
                    }
                }
                $sub_total = ( floatval($data['combo_product_price'][$key]) - (floatval($data['combo_product_price'][$key]) * floatval($data['combo_product_discount'][$key])/100)) * floatval($data['combo_product_quantity'][$key]);

                $price = floatval($data['combo_product_price'][$key]);

                $comboProduct = new ProductItemDetail([
                    'product_sku_id' => $data['combo_product_id'][$key],
                    'price' => $price,
                    'tax' => $data['product_tax'][$key],
                    'gst_group' => $gst_group,
                    'igst' => ($gst_group != null) ? $data['combo_product_igst'][$key] : 0,
                    'cgst' => ($gst_group != null) ? $data['combo_product_cgst'][$key] : 0,
                    'sgst' => ($gst_group != null) ? $data['combo_product_sgst'][$key] : 0,
                    'cess' => ($gst_group != null) ? $data['combo_product_cess'][$key] : 0,
                    'quantity' => $data['combo_product_quantity'][$key],
                    'discount' => $data['combo_product_discount'][$key],
                    'sub_total' => $sub_total,
                    'productable_id' => $data['combo_product_id'][$key],
                    'productable_type' => ComboProduct::class,
                ]);
                $pos->items()->save($comboProduct);
                if (!empty($data['combo_serial_no'])) {
                    for ($i=0; $i < count($data['combo_serial_no']) ; $i++) {
                        $explode_combo_serial = explode('-', $data['combo_serial_no'][$i]);

                        if ($data['combo_product_id'][$key] == $explode_combo_serial[0]) {
                            $part_number_detail = new ProductItemDetailPartNumber;
                            $part_number_detail->part_number_id = $explode_combo_serial[1];
                            $part_number_detail->sale_id = $pos->id;
                            $part_number_detail->product_sku_id = $explode_combo_serial[2];
                            $part_number_detail->product_item_detail_id = $comboProduct->id;
                            $part_number_detail->save();
                        }

                    }
                }
            }
        }
        $part_number_details = $pos->product_item_details_part_numbers;
        if (count($part_number_details) > 0) {
            foreach ($part_number_details as $key => $part_number_detail) {
                $part_number = PartNumber::find($part_number_detail->part_number_id);
                $part_number->is_sold = 1;
                $part_number->save();
            }
        }
        return $pos;
    }

    public function find($id)
    {
        return Sale::with('items', 'items.product','customer','user')->findOrFail($id);
    }

    public function update(array $payments, $id, $initial_payment = 0)
    {
        $sale = Sale::find($id);
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $this->proJournalEntryPayment($sale, $payments, $id, $initial_payment);
        } else {
            $total_amount = 0;
            $repo = new VoucherRepository();
            $paid_amount_before = $sale->payments()->where('payment_type','pay')->sum('amount');
            $paid_amount = 0;
            $dueAmount = $sale->payable_amount - $paid_amount_before;
            foreach ($payments as $key => $payment) {
                $paid_amount += $payment['amount'];
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
                    'initial_payment' => ($initial_payment == 1) ? 1 : 0,
                    'amount' => (float) $amount,
                    'advance_amount' => (float) $advance_amount,
                    'account_id' => array_key_exists('account_id', $payment) ? $payment['account_id'] : '',
                    'bank_name' => array_key_exists('bank_name', $payment) ? $payment['bank_name'] : '',
                    'branch' => array_key_exists('branch', $payment) ? $payment['branch'] : '',
                    'account_no' => array_key_exists('account_no', $payment) ? $payment['account_no'] : '',
                    'account_owner' => array_key_exists('account_owner', $payment) ? $payment['account_owner'] : '',
                ]);
                $sale->payments()->save($sale_payment);

                if ($sale->is_approved != 0) {
                    $debit_account_id = [];
                    $debit_account_amount = [];
                    $narration = [];
                    $txAmountValue = $payment['amount'];
                    //Transaction Money
                    if ($sale->customer_id != null) {
                        $chart_account = $this->AccountFind($sale->customer_id, get_class(new ContactModel));
                    } else {
                        $chart_account = $this->AccountFind($sale->agent_user_id, get_class(new User));
                    }


                    if ($payment['payment_method'] == "cash" || $payment['payment_method'] == "quick cash") {
                        $debit_account_id = $this->GetAccountId($sale->saleable_id, $sale->saleable_type);
                    } else {
                        $debit_account_id = $this->inventoryBankAccount($payment['account_id']);
                    }
                    $tx_amount = ($dueAmount <= (float)$txAmountValue) ? $dueAmount : (float)$txAmountValue;
                    $debit_account_amount = $tx_amount;
                    $tx_naration = 'Sales '. $payment['payment_method'];
                    $narration = $tx_naration;
                    $dueAmount -= $payment['amount'];
                    $repo->create([
                        'voucher_type' => $payment['payment_method'] == "cash"  || $payment['payment_method'] == "quick cash" ? 'CV' : 'BV' ,
                        'amount'=> ($sale->payable_amount <= (float)$payment['amount']) ? $sale->payable_amount : (float)$payment['amount'],
                        'date'=> Carbon::now()->format('Y-m-d'),
                        'payment_type' => 'voucher_recieve',
                        'credit_account_id'=> $chart_account->id,  //debit side and credit side shoud be same
                        'credit_account_amount'=> ($sale->payable_amount <= (float)$payment['amount']) ? $sale->payable_amount : (float)$payment['amount'],  //debit side and credit side shoud be same
                        'credit_account_narration'=> 'Payment recieved by'. $payment['payment_method'],  //debit side and credit side shoud be same

                        'debit_account_id'=> $debit_account_id,   //debit side and credit side shoud be same
                        'debit_account_amount'=> $debit_account_amount,
                        'debit_account_narration'=> $narration,
                        'narration' => 'Payment recieved by'. $payment['payment_method'],
                        'cheque_no' => null,
                        'cheque_date' => null,
                        'bank_name' => $payment['payment_method'] == "bank" ? $payment['bank_name'] : null,
                        'bank_branch' => $payment['payment_method'] == "bank" ? $payment['branch'] : null,
                        'sale_id' => $sale->id,
                        'sale_class' => get_class(new Sale),
                        'is_approve' => (app('business_settings')->where('type', 'sale_voucher_approval')->first()->status == 1) ? 1 : 0,
                    ]);
                }
            }

            if ($sale->payable_amount <= $paid_amount) {
                $sale->payments()->where('payment_method', 'quick cash')->update(['return_amount' => $paid_amount - $sale->payable_amount]);
                $sale->status = 1;
            }
            if ($sale->payable_amount > $paid_amount && $paid_amount > 0){
                $sale->status = 2;
            }
            if ($paid_amount == 0) {
                $pos_order->status = 0;
            }

            $sale->save();
        }
        return $sale;
    }

    public function approval($id)
    {
        $sale = Sale::find($id);
        $w = $sale->saleable;
        $part_number_details = $sale->product_item_details_part_numbers;
        if (count($part_number_details) > 0) {
            foreach ($part_number_details as $key => $part_number_detail) {
                $part_number = PartNumber::find($part_number_detail->part_number_id);
                $part_number->is_sold = 1;
                $part_number->save();
            }
        }
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            if ($sale->customer_id) {
                $chart_account = $this->PartnerAccountFind($sale->customer_id, get_class(new ContactModel));

                $acoountBalance = $sale->customer->accounts['due'];
            } else {
                $chart_account = $this->PartnerAccountFind($sale->agentuser->agent->id, get_class($sale->agentuser->agent));
                $acoountBalance = $sale->agentuser->accounts['due'];
            }
            $this->proJournalEntrySales($sale,$w,$chart_account);
        } else {
            if ($sale->customer_id) {
                $chart_account = $this->AccountFind($sale->customer_id, get_class(new ContactModel));

                $acoountBalance = $sale->customer->accounts['due'];
            } else {
                $chart_account = $this->AccountFind($sale->agent_user_id, get_class(new User));

                $acoountBalance = $sale->agentuser->accounts['due'];
            }
            $tax_account_amount = 0;
            $single_item_tax = 0;
            $purchase_amount = 0;
            foreach ($sale->items as $item) {
                if ($item->productable_type == "Modules\Product\Entities\ComboProduct") {
                    foreach ($item->productable->combo_products as $key => $combo_product_sku) {
                        $pro_sku = $combo_product_sku->product_sku_id;
                        $qty = $item->quantity * $combo_product_sku->product_qty;
                        $stock = $w->stocks()->where('product_sku_id', $pro_sku)->first();
                        $stock->update(['stock' => $stock->stock - $qty, 'out' => $stock->out + $qty]);
                    }
                }
                else {
                    $productSku = ProductSku::find($item->product_sku_id);

                    if($productSku->product->product_type != 'Service' )
                    {
                        $stock = $w->stocks()->where('product_sku_id', $item->product_sku_id)->first();
                        $stock->update(['stock' => $stock->stock - $item->quantity - $item->return_quantity, 'out' => $stock->out + ($item->quantity - $item->return_quantity)]);
                    }

                }
                $tax_account_amount += (($item->price - $item->discount) * $item->quantity ) * $item->tax / 100;
                if ($item->productable_type == get_class(new ProductSku)) {
                    $purchase_amount += $item->productable->cost_of_goods * $item->quantity;
                } else {
                    $purchase_amount += $item->productable->total_purchase_price * $item->quantity;
                }
            }
            $sub_account_id[] = $this->defaultSalesAccount(); //Sales Transacton Account
            $sub_amount[] = $sale->amount - $tax_account_amount;
            $sub_narration[] = 'Product Sales';

            if ($tax_account_amount > 0) {
                $sub_account_id[] = $this->defaultProductTaxAccount(); //Product Tax
                $sub_amount[] = $tax_account_amount;
                $sub_narration[] = 'Product Sale Tax';
            }

            if ($sale->tax_id != 0) {
                $taxDetails = Tax::findOrFail($sale->tax_id);
                $sub_account_id[] = $this->GetAccountId($sale->tax_id, 'Modules\Setup\Entities\Tax');
                $sub_amount[] = (($sale->amount - $sale->total_discount) * $sale->total_tax) / 100;
                $sub_narration[] =  $taxDetails->name.' '.  $taxDetails->rate . 'Tax on Purchase';
            }
            if ($sale->shipping_charge > 0 || $sale->other_charge > 0) {
                $sub_account_id[] = $this->shippingOrOthersChargeIncome();
                $sub_amount[] = $sale->shipping_charge + $sale->other_charge;
                $sub_narration[] = 'Sales Income (Shipping and others charge)';
            }

            $journal = new JournalRepository();
            $journal->create([
                'voucher_type' => 'JV',
                'amount' => $sale->payable_amount,
                'date' => Carbon::now()->format('Y-m-d'),
                'account_type' => 'debit',
                'payment_type' => 'journal_voucher',
                'account_id' => $chart_account->id,
                'main_amount' => $sale->payable_amount,
                'narration' => 'Product Sales',

                'sub_account_id' => array_reverse($sub_account_id),
                'sub_amount' => array_reverse($sub_amount),
                'sub_narration' => array_reverse($sub_narration),

                'sale_id' => $sale->id,
                'sale_class' => get_class(new Sale),
                'is_approve' => (app('business_settings')->where('type', 'sale_voucher_approval')->first()->status == 1) ? 1 : 0,
            ]);


            $purchase_sub_account_id[] = $this->defaultCostofGoodsSoldAccount();
            $purchase_sub_amount[] = $purchase_amount;
            $purchase_sub_narration[] = 'Cost of goods sold to customer/Retailer';
            $journal->create([
                'voucher_type' => 'JV',
                'amount' => $purchase_amount,
                'date' => Carbon::now()->format('Y-m-d'),
                'account_type' => 'credit',
                'payment_type' => 'journal_voucher',
                'account_id' => $this->defaultPurchaseAccount(), //Purchase & Inventory Account
                'main_amount' => $purchase_amount,
                'narration' => 'Inventory deduct for sales purpose',

                'sub_account_id' => $purchase_sub_account_id,
                'sub_amount' => $purchase_sub_amount,
                'sub_narration' => $purchase_sub_narration,
                'sale_id' => $sale->id,
                'sale_class' => get_class(new Sale),
                'is_approve' => (app('business_settings')->where('type', 'sale_voucher_approval')->first()->status == 1) ? 1 : 0,
            ]);

            $voucher = new VoucherRepository();

            foreach ($sale->payments as $key => $payment) {

                if ($payment->payment_method == "cash" || $payment->payment_method == "quick cash") {
                    $debit_account_id[] = $this->GetAccountId($sale->saleable_id, $sale->saleable_type); //ChartAccount::where('contactable_type',
                } else {
                    $debit_account_id[] = ChartAccount::findOrFail($payment->account_id)->id; //Bank Account
                }
                if ($payment->return_amount > 0) {
                    $debit_account_amount[] = ($payment->amount + $payment->advance_amount) - $payment->return_amount;
                } else {
                    $debit_account_amount[] = $payment->amount + $payment->advance_amount ;
                }
                $narration[] = 'Product Sales';
                $voucher->create([
                    'voucher_type' => $payment->payment_method == "cash" || $payment->payment_method == "quick cash" ? 'CV' : 'BV' ,
                    'amount'=> ($payment->amount + $payment->advance_amount) - $payment->return_amount,
                    'date'=> Carbon::now()->format('Y-m-d'),
                    'payment_type' => 'voucher_recieve',
                    'credit_account_id'=> $chart_account->id, //debit side and credit side shoud be same
                    'credit_account_amount'=> ($payment->amount + $payment->advance_amount) - $payment->return_amount, //debit side and credit side shoud be same
                    'credit_account_narration'=> 'Payment recieved by '. $payment->payment_method, //debit side and credit side shoud be same

                    'debit_account_id'=> $debit_account_id, //debit side and credit side shoud be same
                    'debit_account_amount'=> $debit_account_amount,
                    'debit_account_narration'=> $narration,
                    'narration' => 'Payment recieved by '. $payment->payment_method,
                    'cheque_no' => null,
                    'cheque_date' => null,
                    'bank_name' => $payment->payment_method == "bank" ? $payment->bank_name : null,
                    'bank_branch' => $payment->payment_method == "bank" ? $payment->branch : null,
                    'sale_id' => $sale->id,
                    'sale_class' => get_class(new Sale),
                    'is_approve' => (app('business_settings')->where('type', 'sale_voucher_approval')->first()->status == 1) ? 1 : 0,
                ]);

            }
        }

        $sale->is_approved = 1;

        $sale->save();
        if ($acoountBalance < 0) {

            $extra_amount = abs($acoountBalance);
            if ($sale->customer_id) {
                $customer = ContactModel::find($sale->customer_id);
            }else {
                $customer = User::find($sale->agent_user_id);
            }

            if ($customer->sales) {
                foreach ($customer->sales->where('status', '!=', 1)->where('is_approved',1) as $key => $sale) {
                    if ($sale->status != 1) {
                        $order = $sale;
                        $due_amount = $order->payable_amount - $order->payments()->where('payment_type','pay')->sum('amount');
                        if ($due_amount > 0 && $extra_amount > 0) {
                            if ($extra_amount >= $due_amount) {
                                $sale_payment = new Payment([
                                    'payment_method' => 'Adjustment Balance',
                                    'amount' => $due_amount,
                                    'payable_id' => $order->id,
                                    'payable_type' => 'Modules\Sale\Entities\Sale',
                                ]);
                                $order->status = 1;
                                $order->payments()->save($sale_payment);
                            }else {
                                $sale_payment = new Payment([
                                    'payment_method' => 'Adjustment Balance',
                                    'amount' => $extra_amount,
                                    'payable_id' => $order->id,
                                    'payable_type' => 'Modules\Sale\Entities\Sale',
                                ]);
                                $order->status = 2;
                                $order->payments()->save($sale_payment);
                            }
                            $extra_amount -= $due_amount;
                        }
                        $order->save();
                    }
                }
            }
        }
        return $sale;
    }

    public function stockMinus($sale)
    {
        $w = $sale->saleable;
        foreach ($sale->items as $item) {
            if ($item->productable_type == "Modules\Core\Entities\Product\ComboProduct") {
                foreach ($item->productable->combo_products as $key => $combo_product_sku) {
                    $pro_sku = $combo_product_sku->productSku;
                    $qty = $item->quantity * $combo_product_sku->product_qty;
                        $stock = $w->stocks()->where('product_sku_id', $pro_sku->id)->first();
                        $stock->update(['stock' => $stock->stock - $qty]);
                }
            }
            else {
                $stock = $w->stocks()->where('product_sku_id', $item->product_sku_id)->first();
                    $stock->update(['stock' => $stock->stock - $item->quantity - $item->return_quantity]);
            }
        }
    }

    private function proJournalEntrySales($sale, $w, $chart_account)
    {

        $tax_account_amount = 0;
        $single_item_tax = 0;
        $purchase_amount = 0;
        $prductDiscount = 0;
        foreach ($sale->items as $item) {

            if ($item->productable_type == "Modules\Product\Entities\ComboProduct") {
                foreach ($item->productable->combo_products as $key => $combo_product_sku) {
                    $pro_sku = $combo_product_sku->product_sku_id;
                    $qty = $item->quantity * $combo_product_sku->product_qty;
                    $stock = $w->stocks()->where('product_sku_id', $pro_sku)->first();
                    $stock->update(['stock' => $stock->stock - $qty, 'out' => $stock->out + $qty]);
                }
            }
            else {
                $productSku = ProductSku::find($item->product_sku_id);

                if($productSku->product->product_type != 'Service' )
                {
                    $stock = $w->stocks()->where('product_sku_id', $item->product_sku_id)->first();
                    $stock->update(['stock' => $stock->stock - $item->quantity - $item->return_quantity, 'out' => $stock->out + ($item->quantity - $item->return_quantity)]);
                }

            }
            $tax_account_amount += (($item->price - $item->discount) * $item->quantity ) * $item->tax / 100;
            if ($item->productable_type == get_class(new ProductSku)) {
                $purchase_amount += $item->productable->cost_of_goods * $item->quantity;
            } else {
                $purchase_amount += $item->productable->total_purchase_price * $item->quantity;
            }
        }

        $credit_account_id[] = $this->defaultSalesAccount(); //Sales Transacton Account
        $credit_amounts[] = $sale->amount - $tax_account_amount;
        $credit_narration[] = ($sale->type == 1) ? 'Sales' : 'Conditional Sales / POS';
        $credit_partner_id[] = 0;
        $credit_cash_flow_account_id[] = 0;

        if ($tax_account_amount > 0) {
            $credit_account_id[] = $this->defaultProductTaxAccount(); //Product Tax
            $credit_amounts[] = $tax_account_amount;
            $credit_narration[] = 'Product Sale Tax';
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
        }

        if ($sale->tax_id != 0) {
            $taxDetails = Tax::findOrFail($sale->tax_id);
            $credit_account_id[] = $this->GetAccountId($sale->tax_id, 'Modules\Setup\Entities\Tax')->id;
            $credit_amounts[] = (($sale->amount - $sale->total_discount) * $sale->total_tax) / 100;
            $credit_narration[] =  $taxDetails->name.' '.  $taxDetails->rate . 'Tax on Sales';
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
        }
        if ($sale->shipping_charge > 0 || $sale->other_charge > 0) {
            $credit_account_id[] = $this->shippingOrOthersChargeIncome();
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
            $credit_amounts[] = $sale->shipping_charge + $sale->other_charge;
            $credit_narration[] = 'Sales Income (Shipping and others charge)';
        }

        $debit_amounts[] = $sale->payable_amount;
        $debit_account_id[] = $chart_account->leadger_id;
        $debit_partner_id[] = $chart_account->id;
        $debit_narration[] = ($sale->type == 1) ? 'Sales Entry' : 'Conditional Sales Entry';
        $debit_cash_flow_account_id[] = 0;

        $journalRecieveRepository = new ProJournalRepository();
        $voucher = $journalRecieveRepository->create([
            'type' => "misc",
            'is_cash_flow_journal' => 0,
            'amount'=> $sale->payable_amount,
            'date'=> Carbon::now()->format('Y-m-d'),
            'credit_account_id'=> $credit_account_id,
            'credit_sub_account_id'=> $credit_partner_id,
            'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
            'credit_account_amount'=> $credit_amounts,
            'credit_narration'=> $credit_narration,
            'narration_voucher'=> ($sale->type != 1 && $sale->type != 2) ? 'Conditional Sales Entry' : 'Sales \ POS Entry',
            'referable_type'=> get_class($sale),
            'referable_id'=> $sale->id,
            'is_invoiced'=> 0,
            'is_manual_entry'=> 0,

            'debit_account_id'=> $debit_account_id,
            'debit_sub_account_id'=> $debit_partner_id,
            'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
            'debit_account_amount'=> $debit_amounts,
            'debit_narration'=> $debit_narration,
            'is_approve' => 1,
            'sale_or_purchase' => "s",
            'ref_no' => $sale->invoice_no,
        ]);

        $debit_purchase_account_id[] = $this->defaultCostofGoodsSoldAccount();
        $debit_purchase_sub_account_id[] = 0;
        $debit_purchase_sub_amount[] = $purchase_amount;
        $debit_purchase_sub_narration[] = 'Cost of goods sold to customer/Retailer';
        $debit_purchase_cash_flow_account_id[] = 0;

        $credit_purchase_amounts[] = $purchase_amount;
        $credit_purchase_account_id[] = $this->defaultPurchaseAccount();
        $credit_purchase_partner_id[] = 0;
        $credit_purchase_narration[] = "Purchase Cost Entry at New Sale";
        $credit_purchase_cash_flow_account_id[] = 0;

        if ($purchase_amount != 0) {
            $journalRecieveRepository = new ProJournalRepository();
            $voucher = $journalRecieveRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount'=> $purchase_amount,
                'date'=> Carbon::now()->format('Y-m-d'),
                'credit_account_id'=> $credit_purchase_account_id,
                'credit_sub_account_id'=> $credit_purchase_partner_id,
                'credit_cash_flow_account_id'=> $credit_purchase_cash_flow_account_id,
                'credit_account_amount'=> $credit_purchase_amounts,
                'credit_narration'=> $credit_purchase_narration,
                'narration_voucher'=> ($sale->type != 1 && $sale->type != 2) ? 'Conditional Sales Entry Purchase Cost' : 'Sales \ POS Entry Purchase Cost',
                'referable_type'=> get_class($sale),
                'referable_id'=> $sale->id,
                'is_invoiced'=> 0,
                'is_manual_entry'=> 0,

                'debit_account_id'=> $debit_purchase_account_id,
                'debit_sub_account_id'=> $debit_purchase_sub_account_id,
                'debit_cash_flow_account_id'=> $debit_purchase_cash_flow_account_id,
                'debit_account_amount'=> $debit_purchase_sub_amount,
                'debit_narration'=> $debit_purchase_sub_narration,
                'is_approve' => 1,
                'sale_or_purchase' => "s",
                'ref_no' => $sale->invoice_no,
            ]);
        }


        $journalRecieveRepository = new ProJournalRepository();
        foreach ($sale->payments as $key => $payment) {
            $credit_sales_account_id = [];
            $credit_sales_sub_account_id = [];
            $credit_sales_account_amount = [];
            $credit_sales_narration = [];
            $credit_sales_cash_flow_account_id = [];

            $debit_sales_account_id = [];
            $debit_sales_partner_id = [];
            $debit_sales_amounts = [];
            $debit_sales_narration = [];
            $debit_sales_cash_flow_account_id = [];

            if ($payment->payment_method == "cash" || $payment->payment_method == "quick cash") {
                $credit_sales_account_id[] = $this->GetAccountId($sale->saleable_id, $sale->saleable_type)->id;
                $credit_sales_sub_account_id[] = 0;
            } else {
                $credit_sales_account_id[] = Leadger::findOrFail($payment->account_id)->id; //Bank Account
                $credit_sales_sub_account_id[] = 0;
            }
            if ($payment->return_amount > 0) {
                $credit_sales_account_amount[] = ($payment->amount + $payment->advance_amount) - $payment->return_amount;
            } else {
                $credit_sales_account_amount[] = $payment->amount + $payment->advance_amount ;
            }
            $credit_sales_narration[] = 'Payment recieved by '. $payment->payment_method.' For Sales Purpose';
            $credit_sales_cash_flow_account_id[] = Settings('default_sales_cash_flow_account');

            $debit_sales_amounts[] = ($payment->amount + $payment->advance_amount) - $payment->return_amount;
            $debit_sales_account_id[] = $chart_account->leadger_id;
            $debit_sales_partner_id[] = $chart_account->id;
            $debit_sales_narration[] = 'Payment recieved by '. $payment->payment_method.' For Sales Purpose';
            $debit_sales_cash_flow_account_id[] = 0;

            $voucher = $journalRecieveRepository->create([
                'type' => $payment->payment_method == "cash" || $payment->payment_method == "quick cash" ? 'rec_cash' : 'rec_bank',
                'is_cash_flow_journal' => 0,
                'amount'=> ($payment->amount + $payment->advance_amount) - $payment->return_amount,
                'date'=> Carbon::now()->format('Y-m-d'),
                'credit_account_id'=> $debit_sales_account_id,
                'credit_sub_account_id'=> $debit_sales_partner_id,
                'credit_cash_flow_account_id'=> $debit_sales_cash_flow_account_id,
                'credit_account_amount'=> $debit_sales_amounts,
                'credit_narration'=> $debit_sales_narration,
                'narration_voucher'=> ($sale->type != 1 && $sale->type != 2) ? 'Conditional Sales Entry Payment' : 'A Sales \ POS Entry Payment',
                'referable_type'=> get_class($sale),
                'referable_id'=> $sale->id,
                'is_invoiced'=> 0,
                'is_manual_entry'=> 0,

                'debit_account_id'=> $credit_sales_account_id,
                'debit_sub_account_id'=> $credit_sales_sub_account_id,
                'debit_cash_flow_account_id'=> $credit_sales_cash_flow_account_id,
                'debit_account_amount'=> $credit_sales_account_amount,
                'debit_narration'=> $credit_sales_narration,
                'is_approve' => 1,
                'sale_or_purchase' => "s",
                'ref_no' => $sale->invoice_no,
            ]);
        }
    }

    private function proJournalEntryPayment($sale, $payments, $id, $initial_payment)
    {
        $paid_amount_before = $sale->payments()->where('payment_type','pay')->sum('amount');
        $paid_amount = 0;
        $dueAmount = $sale->payable_amount - $paid_amount_before;
        if ($sale->customer_id) {
            $chart_account = $this->PartnerAccountFind($sale->customer_id, get_class(new ContactModel));
        } else {
            $chart_account = $this->PartnerAccountFind($sale->agentuser->agent->id, get_class($sale->agentuser->agent));
        }
        foreach ($payments as $key => $payment) {
            $paid_amount += $payment['amount'];
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
                'initial_payment' => ($initial_payment == 1) ? 1 : 0,
                'amount' => (float) $amount,
                'advance_amount' => (float) $advance_amount,
                'account_id' => array_key_exists('account_id', $payment) ? $payment['account_id'] : '',
                'bank_name' => array_key_exists('bank_name', $payment) ? $payment['bank_name'] : '',
                'branch' => array_key_exists('branch', $payment) ? $payment['branch'] : '',
                'account_no' => array_key_exists('account_no', $payment) ? $payment['account_no'] : '',
                'account_owner' => array_key_exists('account_owner', $payment) ? $payment['account_owner'] : '',
            ]);
            $sale->payments()->save($sale_payment);

            if ($sale->is_approved != 0) {
                $txAmountValue = (float)$payment['amount'];
                $tx_amount = ($dueAmount <= $txAmountValue) ? $dueAmount : $txAmountValue;

                if ($payment['payment_method'] == "cash" || $payment['payment_method'] == "quick cash") {
                    $credit_sales_account_id[] = $this->GetAccountId($sale->saleable_id, $sale->saleable_type)->id;
                    $credit_sales_sub_account_id[] = 0;
                } else {
                    $credit_sales_account_id[] = Leadger::findOrFail($payment['account_id'])->id; //Bank Account
                    $credit_sales_sub_account_id[] = 0;
                }
                $credit_sales_account_amount[] = $tx_amount ;
                $credit_sales_narration[] = 'Payment recieved by '. $payment['payment_method'].' For Sales Purpose';
                $credit_sales_cash_flow_account_id[] = Settings('default_sales_cash_flow_account');
            }
        }
        if ($sale->is_approved == 1) {

            $debit_sales_amounts[] = $paid_amount;
            $debit_sales_account_id[] = $chart_account->leadger_id;
            $debit_sales_partner_id[] = $chart_account->id;
            $debit_sales_narration[] = 'Payment recieved For Sales Purpose';
            $debit_sales_cash_flow_account_id[] = 0;


            $is_approved = 1;

            $journalRecieveRepository = new ProJournalRepository();
            $voucher = $journalRecieveRepository->create([
                'type' => $payment['payment_method'] == "cash" || $payment['payment_method'] == "quick cash" ? 'rec_cash' : 'rec_bank',
                'is_cash_flow_journal' => 0,
                'amount'=> $paid_amount,
                'date'=> Carbon::now()->format('Y-m-d'),
                'credit_account_id'=> $debit_sales_account_id,
                'credit_sub_account_id'=> $debit_sales_partner_id,
                'credit_cash_flow_account_id'=> $debit_sales_cash_flow_account_id,
                'credit_account_amount'=> $debit_sales_amounts,
                'credit_narration'=> $debit_sales_narration,
                'narration_voucher'=> ($sale->type != 1 && $sale->type != 2) ? 'Conditional Sales Entry Payment' : 'Sales \ POS Entry Payment',
                'referable_type'=> get_class($sale),
                'referable_id'=> $sale->id,
                'is_invoiced'=> 0,
                'is_manual_entry'=> 0,

                'debit_account_id'=> $credit_sales_account_id,
                'debit_sub_account_id'=> $credit_sales_sub_account_id,
                'debit_cash_flow_account_id'=> $credit_sales_cash_flow_account_id,
                'debit_account_amount'=> $credit_sales_account_amount,
                'debit_narration'=> $credit_sales_narration,
                'is_approve' => $is_approved,
                'sale_or_purchase' => "s",
                'ref_no' => $sale->invoice_no,
            ]);
        }

        if ($sale->payable_amount <= $paid_amount) {
            $sale->payments()->where('payment_method', 'quick cash')->update(['return_amount' => $paid_amount - $sale->payable_amount]);
            $sale->status = 1;
        }
        if ($sale->payable_amount > $paid_amount && $paid_amount > 0){
            $sale->status = 2;
        }
        if ($paid_amount == 0) {
            $sale->status = 0;
        }
        $sale->save();
    }
}
