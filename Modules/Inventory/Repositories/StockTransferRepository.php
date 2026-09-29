<?php



namespace Modules\Inventory\Repositories;



use Carbon\Carbon;

use Illuminate\Support\Arr;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;

use Modules\Inventory\Exports\ProductInfoExport;

use Modules\Product\Exports\OpeningStockAddExport;

use Modules\Inventory\Exports\StockTransferedExport;

use Modules\Inventory\Exports\StockListExport;

use Modules\Inventory\Entities\ShowRoom;

use Modules\Inventory\Entities\WareHouse;

use Modules\Product\Entities\ProductHistory;

use Modules\Product\Entities\ProductSku;

use Modules\Inventory\Entities\StockReport;

use Modules\Inventory\Entities\StockTransfer;

use Modules\Product\Repositories\ProductRepository;

use Modules\Account\Repositories\JournalRepository;

use Modules\ProAccount\Repositories\VoucherRepository as ProVoucherRepository;

use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;

use Modules\Product\Entities\ComboProductDetail;

use Modules\Product\Repositories\ProductRepositoryInterface;

use Modules\Purchase\Entities\ProductItemDetail;

use Modules\Sale\Entities\Sale;



class StockTransferRepository implements StockTransferRepositoryInterface

{



    public function all()

    {

        return StockTransfer::latest()->get();

    }



    public function allStockProduct()

    {

        return StockReport::with('productSku.product')->latest()->get();

    }



    public function csvDownloadAddOpeningStock($data)

    {

        if (file_exists(public_path("uploads/csv/opening-stock-add.xlsx"))) {

            unlink(public_path("uploads/csv/opening-stock-add.xlsx"));

        }

        return Excel::store(new OpeningStockAddExport($data), 'uploads/csv/opening-stock-add.xlsx', 'public_folder');

    }



    public function allProductListByShowroomListQuery($search_keyword, $row_count, $column, $relational_data = [], $selected_data = ['*'])

    {

        $items = ProductHistory::query();



        $items = $items->with($relational_data)->where('type', 'begining')->with('itemable', 'stock');



        if (auth()->user()->role->type != "system_user") {

            $items = $items->where('itemable_id', session()->get('showroom_id'));

        }

        if ($search_keyword != null) {

            $items->whereHas('productSku', function ($items) use ($search_keyword) {

                $items->whereLike(['product.product_name'],$search_keyword);

            });

        }

        if ($row_count == "all") {

            $total_number = ProductHistory::count();



            if ($column != null) {

                return $items->orderBy($column, 'asc')->paginate($total_number,$selected_data);

            }else {

                return $items->latest()->paginate($total_number,$selected_data);

            }

        }else {

            if ($column != null) {

                return $items->orderBy($column, 'asc')->paginate($row_count,$selected_data);

            }else {

                return $items->latest()->paginate($row_count,$selected_data);

            }

        }

    }



    public function csvDownload($type, $data)

    {

        if (file_exists(public_path("uploads/csv/stock-transfer-list.xlsx"))) {

            unlink(public_path("uploads/csv/stock-transfer-list.xlsx"));

        }

        return Excel::store(new StockTransferedExport($type, $data), 'uploads/csv/stock-transfer-list.xlsx', 'public_folder');

    }



    public function csvDownloadStockList($data)

    {

        if (file_exists(public_path("uploads/csv/stock_list.xlsx"))) {

            unlink(public_path("uploads/csv/stock_list.xlsx"));

        }

        return Excel::store(new StockListExport($data), 'uploads/csv/stock_list.xlsx', 'public_folder');

    }



    public function withPaginate($row_count, $quick_search, $name, $sort, $column, $type, $relational_data = [], $selected_data = ['*'])

    {

        $items = StockTransfer::query();

        if ($quick_search != null) {

            $items = $items->whereLike(['date'], $quick_search)

                ->orWherehas('sendable', function ($q) use ($quick_search) {

                    $q->whereLike(['name'], $quick_search);

                })

                ->orWherehas('receivable', function ($q) use ($quick_search) {

                    $q->whereLike(['name'], $quick_search);

                });

        }



        if ($type == "sent") {

            $items = $items->with($relational_data)->where('sendable_id', session()->get('showroom_id'));

        }

        if ($type == "rcv") {

            $items = $items->with($relational_data)->where('receivable_id', session()->get('showroom_id'));

        }



        if ($row_count == "all") {

            if ($type == "sent") {

                $total_number = StockTransfer::where('sendable_id', session()->get('showroom_id'))->count();

            }

            if ($type == "rcv") {

                $total_number = StockTransfer::where('receivable_id', session()->get('showroom_id'))->count();

            }



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



    public function csvDownloadStockProduct($data)

    {

        if (file_exists(public_path("uploads/csv/product-info-list.xlsx"))) {

            unlink(public_path("uploads/csv/product-info-list.xlsx"));

        }

        return Excel::store(new ProductInfoExport($data), 'uploads/csv/product-info-list.xlsx', 'public_folder');

    }



    public function allStockProductwithPaginate($row_count, $search_keyword, $sort, $column)

    {

        $items = StockReport::query();



        if (auth()->user()->role->type != "system_user") {

            $items = $items->where('houseable_type', 'Modules\Inventory\Entities\ShowRoom')->where('houseable_id', request()->get('showroom_id', session()->get('showroom_id')));

        }

        if ($search_keyword != null) {

            $like = 'LIKE';

            $stockReportIds = DB::table('stock_reports')

                ->join('product_sku', 'product_sku.id', 'stock_reports.product_sku_id')

                ->join('products', 'products.id', 'product_sku.product_id')

                ->join('brands', 'brands.id', 'products.brand_id')

                ->join('models', 'models.id', 'products.model_id')

                ->where('product_sku.sku', $like, '%' . $search_keyword . '%')

                ->orWhere('models.name', $like, '%' . $search_keyword . '%')

                ->orWhere('brands.name', $like, '%' . $search_keyword . '%')

                ->orWhere('products.product_name', $like, '%' . $search_keyword . '%')

                ->orWhere('products.origin', $like, '%' . $search_keyword . '%')

                ->orWhere('stock', $like, '%' . $search_keyword . '%')

                ->select('stock_reports.id as id')

                ->pluck('id');

            $items = $items->whereIn('id', $stockReportIds);

        }



        if ($row_count == "all") {

            $total_number = StockReport::count();



            if ($column != null) {

                return $items->orderBy($column, $sort)->paginate($total_number);

            } else {

                return $items->latest()->paginate($total_number);

            }

        } else {

            if ($column != null) {

                return $items->orderBy($column, $sort)->paginate($row_count);

            } else {

                return $items->latest()->paginate($row_count);

            }

        }

    }



    public function checkQty($product_sku_id, $showroom_id)

    {

        $house = ShowRoom::find($showroom_id);

        $sku = ProductSku::with('product')->find($product_sku_id);

        if ($sku->product->product_type != "Service") {

            return $house->stocks()->where('product_sku_id', $product_sku_id)->first();

        } else {

            return "pass";

        }

    }



    public function checkQtyForCombo($combo_id, $showroom_id)

    {

        $house = ShowRoom::find($showroom_id);

        $combo_products = ComboProductDetail::where('combo_product_id', $combo_id)->get();

        foreach ($combo_products as $key => $product) {

            $sku = ProductSku::with('product')->find($product->product_sku_id);

            if ($sku->product->product_type != "Service") {

                $stock = $house->stocks()->where('product_sku_id', $product->product_sku_id)->first();

                if ($product->product_qty > $stock->stock) {

                    return 0;

                }

            } else {

                return "pass";

            }

        }

        return 1;

    }



    public function create(array $data)

    {

        $sender = explode('-', $data['from']);



        $receiver = explode('-', $data['to']);



        if ($sender[0] == 'warehouse')

            $from = WareHouse::find($sender[1]);

        else

            $from = ShowRoom::find($sender[1]);



        if ($receiver[0] == 'warehouse')

            $to = WareHouse::find($receiver[1]);

        else

            $to = ShowRoom::find($receiver[1]);



        $documents = array();

        if (!empty($data['documents'])) {



            foreach ($data['documents'] as $file) {

                $name = uniqid() . $file->getClientOriginalName();

                $file->move(public_path() . '/uploads/stock_transfer/', $name);

                $documents[] = '/uploads/stock_transfer/' . $name;

            }

        }



        $transfer = new StockTransfer([

            'date' => date('Y-m-d', strtotime($data['date'])),

            'notes' => $data['notes'],

            'documents' => $documents,

            'receivable_id' => $to->id,

            'receivable_type' => get_class($to),

        ]);



        $from->sends()->save($transfer);



        if (!empty($data['product_id'])) {



            foreach ($data['product_id'] as $key => $id) {

                $price = $data['product_price'][$key];



                $sub_total = (($price * $data['quantity'][$key]));

                $product = new ProductItemDetail([

                    'product_sku_id' => $data['product_id'][$key],

                    'price' => $price,

                    'quantity' => $data['quantity'][$key],

                    'sub_total' => $sub_total,

                    'productable_id' => $data['product_id'][$key],

                    'productable_type' => get_class(new ProductSku),

                ]);

                $transfer->items()->save($product);

            }

        }

        return $transfer;

    }



    public function find($id)

    {

        return StockTransfer::findOrFail($id);

    }



    public function update(array $data, $id)

    {

        $sender = explode('-', $data['from']);



        $receiver = explode('-', $data['to']);



        if ($sender[0] == 'warehouse')

            $from = WareHouse::find($sender[1]);

        else

            $from = ShowRoom::find($sender[1]);



        if ($receiver[0] == 'warehouse')

            $to = WareHouse::find($receiver[1]);

        else

            $to = ShowRoom::find($receiver[1]);



        $documents = array();

        if (!empty($data['documents'])) {



            foreach ($data['documents'] as $file) {

                $name = uniqid() . $file->getClientOriginalName();

                $file->move(public_path() . '/uploads/stock_transfer/' . $name);

                $documents[] = '/uploads/stock_transfer/' . $name;

            }

        }



        $transfer = StockTransfer::find($id);

        $transfer->date = date('Y-m-d', strtotime($data['date']));

        $transfer->notes = $data['notes'];

        $transfer->documents = $documents;

        $transfer->receivable_id = $to->id;

        $transfer->sendable_id = $from->id;

        $transfer->receivable_type = get_class($to);

        $transfer->sendable_type = get_class($to);

        $transfer->save();



        if (!empty($data['items'])) {

            foreach ($data['items'] as $key => $cart) {

                $productSku = ProductItemDetail::where('itemable_id', $transfer->id)->where('itemable_type', get_class(new StockTransfer()))

                    ->where('productable_type', get_class(new ProductSku))->where('product_sku_id', $data['items'][$key])->first();

                if ($productSku) {



                    $sub_total = ($data['item_price'][$key] * $data['item_quantity'][$key]);



                    $productSku->update([

                        'price' => $data['item_price'][$key],

                        'quantity' => $data['item_quantity'][$key],

                        'sub_total' => $sub_total

                    ]);

                }

            }

        }

        if (!empty($data['product_id'])) {

            foreach ($data['product_id'] as $key => $id) {

                $price = $data['product_price'][$key];



                $sub_total = (($price * $data['quantity'][$key]));

                $product = new ProductItemDetail([

                    'product_sku_id' => $data['product_id'][$key],

                    'price' => $price,

                    'quantity' => $data['quantity'][$key],

                    'sub_total' => $sub_total,

                    'productable_id' => $data['product_id'][$key],

                    'productable_type' => get_class(new ProductSku),

                ]);

                $transfer->items()->save($product);

            }

        }

    }



    public function delete($id)

    {

        $transfer = StockTransfer::find($id);



        $repo = new ProductRepository();



        foreach ($transfer->items as $item)

        {

            $repo->increaseQuantity($item->product_sku_id, $item->quantity);

        }



        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {

            foreach ($transfer->proRefers as $key => $voucher) {

                $voucherRepo = new ProVoucherRepository();

                $voucherRepo->deleteApproved($voucher->id);

            }

        } else {

            foreach ($transfer->refers as $key => $voucher) {

                $voucher->transactions()->delete();

                $voucher->delete();

            }

        }

        $transfer->delete();

    }



    public function statusChange($id)

    {

        $stock_transfer = $this->find($id);



        $total_amount = 0;



        foreach ($stock_transfer->items as $key => $item) {

            ProductHistory::create([

                'type' => 'transferred',

                'date' => Carbon::now()->toDateString(),

                'status' => 1,

                'in_out' => $item->quantity,

                'product_sku_id' => $item->product_sku_id,

                'itemable_id' => $stock_transfer->sendable_id,

                'itemable_type' => $stock_transfer->sendable_type,

                'houseable_id' => $stock_transfer->id,

                'houseable_type' => get_class($stock_transfer),

            ]);



            $total_amount += (float)$item->price * (float)$item->quantity;

        }



        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {

            $debit_account_id[] = Settings('default_purchase_account');

            $debit_partner_id[] = 0;

            $debit_cash_flow_account_id[] = 0;

            $debit_amount[] = $total_amount;

            $debit_narration[] = 'Product Purchase';



            $credit_account_id[] = $stock_transfer->sendable->morph->id;

            $credit_partner_id[] = 0;

            $credit_cash_flow_account_id[] = 0;

            $credit_amounts[] = $total_amount;

            $credit_narration[] = 'Sent Stock';



            $journalRecieveRepository = new ProJournalRepository();

            $voucher = $journalRecieveRepository->create([

                'type' => "misc",

                'is_cash_flow_journal' => 0,

                'amount' => $total_amount,

                'date' => Carbon::now()->format('Y-m-d'),

                'credit_account_id' => $debit_account_id,

                'credit_sub_account_id' => $debit_partner_id,

                'credit_cash_flow_account_id' => $debit_cash_flow_account_id,

                'credit_account_amount' => $debit_amount,

                'credit_narration' => $debit_narration,

                'narration_voucher' => "Sent Stock",

                'referable_type' => get_class($stock_transfer),

                'referable_id' => $stock_transfer->id,

                'is_invoiced' => 0,

                'is_manual_entry' => 0,



                'debit_account_id' => $credit_account_id,

                'debit_sub_account_id' => $credit_partner_id,

                'debit_cash_flow_account_id' => $credit_cash_flow_account_id,

                'debit_account_amount' => $credit_amounts,

                'debit_narration' => $credit_narration,

                'is_approve' => 1,

                'sale_or_purchase' => "transfer_sent",

                'ref_no' => null,

            ]);

        } else {

            $sub_account_id[] = $stock_transfer->sendable->contact->id;

            $sub_amount[] = $total_amount;

            $sub_narration[] = 'Product Transfered';



            $repo = new JournalRepository();



            $repo->create([

                'voucher_type' => 'JV',

                'amount' => $total_amount,

                'date' => Carbon::now()->format('Y-m-d'),

                'account_type' => 'debit',

                'payment_type' => 'journal_voucher',

                'account_id' => $stock_transfer->receivable->contact->id,

                'main_amount' => $total_amount,

                'narration' => 'Product Transfered from '.$stock_transfer->sendable->name .' to '.$stock_transfer->receivable->name,



                'sub_account_id' => array_reverse($sub_account_id),

                'sub_amount' => array_reverse($sub_amount),

                'sub_narration' => array_reverse($sub_narration),



                'sale_id' => $stock_transfer->id,

                'sale_class' => get_class($stock_transfer),

                'is_approve' => 1,

                'is_purchase' => 0,

            ]);

        }



        return StockTransfer::where('id', $id)->update(['status' => 1]);

    }



    public function sendToHouse($id)

    {

        $transfer = StockTransfer::find($id);

        $transfer->sent_at = Carbon::now();

        $transfer->save();

        return $transfer;

    }



    public function stockReceive($id)

    {

        $error = 1;

        $transfer = StockTransfer::find($id);

        $house = $transfer->receivable;

        $total_amount = 0;

        $to = $transfer->sendable;

        foreach ($transfer->items as $product) {



            $total_amount += (float)$product->price * (float)$product->quantity;

            $product_sku = $product->product_sku_id;

            $exists = $to->stocks()->where('product_sku_id', $product_sku)->first();

            if ($exists && $exists->stock >= $product->quantity) {

                $history = ProductHistory::where('type', 'purchase')->where('houseable_id', $product->itemable_id)->where('houseable_type', $product->itemable_type)

                    ->where('product_sku_id', $product->product_sku_id)->first();



                $increasedQuantity = $product->quantity - $product->return_quantity;

                $stock = $house->stocks()->where('product_sku_id', $product_sku)->first();

                if ($stock) {

                    $stock->update(['stock' => $stock->stock + $increasedQuantity, 'in' => $stock->stock + $increasedQuantity]);

                } else {

                    StockReport::create([

                        'stock' => $increasedQuantity,

                        'in' => $increasedQuantity,

                        'product_sku_id' => $product->product_sku_id,

                        'houseable_id' => $transfer->receivable_id,

                        'houseable_type' => $transfer->receivable_type,

                    ]);

                }



                $exists->update([

                    'stock' => $exists->stock - $increasedQuantity,

                    'out' => $exists->out + $increasedQuantity

                ]);

                if ($history) {

                    $history->status = 1;

                    $history->save();

                }

            } else {

                return $error;

            }

        }

        $transfer->received_at = Carbon::now();

        $transfer->save();

        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {

            $debit_account_id[] = Settings('default_purchase_account');

            $debit_partner_id[] = 0;

            $debit_cash_flow_account_id[] = 0;

            $debit_amount[] = $total_amount;

            $debit_narration[] = 'Transfered Product Recieve';



            $credit_account_id[] = $house->morph->id;

            $credit_partner_id[] = 0;

            $credit_cash_flow_account_id[] = 0;

            $credit_amounts[] = $total_amount;

            $credit_narration[] = 'Sent Stock Recieve';



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

                'narration_voucher' => "Sent Stock Recieve",

                'referable_type' => get_class($transfer),

                'referable_id' => $transfer->id,

                'is_invoiced' => 0,

                'is_manual_entry' => 0,



                'debit_account_id' => $debit_account_id,

                'debit_sub_account_id' => $debit_partner_id,

                'debit_cash_flow_account_id' => $debit_cash_flow_account_id,

                'debit_account_amount' => $debit_amount,

                'debit_narration' => $debit_narration,

                'is_approve' => 1,

                'sale_or_purchase' => "transfer_rcv",

                'ref_no' => null,

            ]);

        }

        return $transfer;

    }



    public function stockList()
    {
        // Get all records from ShowRoom with houseable_type
        $showRooms = ShowRoom::select(
            'id',
            'name',
            'created_at',
            'updated_at',
            DB::raw("'Modules\\\\Inventory\\\\Entities\\\\ShowRoom' as houseable_type")
        );

        // Get all records from WareHouse with houseable_type and combine with ShowRoom
        $allRecords = WareHouse::select(
            'id',
            'name',
            'created_at',
            'updated_at',
            DB::raw("'Modules\\\\Inventory\\\\Entities\\\\WareHouse' as houseable_type")
        )
            ->union($showRooms)
            ->get();

        return $allRecords;
    }



    // public function stockList()

    // {

    //     return StockReport::with('productSku.product','houseable', 'inProductHistory')->groupBy('houseable_id')->where('houseable_type', ShowRoom::class)->get();

    // }



    public function stock($id)

    {

        return StockReport::with('productSku.item')->find($id);

    }



    public function suggestList($relational_data=['suggestProducts'],$selected_data=['*'])

    {

        if (session()->get('showroom_id') != 1)

            return StockReport::with($relational_data)->where('houseable_type', 'Modules\Inventory\Entities\ShowRoom')

                ->where('houseable_id', session()->get('showroom_id'))->whereHas('suggestProducts', function ($query) {

                    $query->whereColumn('alert_quantity', '>=', 'stock_reports.stock');

                })->latest()->get($selected_data);

        else

            return StockReport::with($relational_data)->whereHas('suggestProducts', function ($query) {

                    $query->whereColumn('alert_quantity', '>=', 'stock_reports.stock');

                })->latest()->get($selected_data);

    }

}

