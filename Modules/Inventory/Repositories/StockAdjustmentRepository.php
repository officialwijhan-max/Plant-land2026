<?php

namespace Modules\Inventory\Repositories;

use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Inventory\Exports\StockAdjustmentExport;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Product\Entities\ProductSku;
use Modules\Inventory\Entities\WareHouse;
use Modules\Product\Entities\ProductHistory;
use Modules\Inventory\Entities\StockReport;
use Modules\Account\Entities\ChartAccount;
use Modules\Inventory\Entities\StockAdjustment;
use Modules\Inventory\Entities\StockAdjustmentProduct;
use Modules\Account\Repositories\JournalRepository;
use Modules\ProAccount\Repositories\VoucherRepository as ProVoucherRepository;
use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;

class StockAdjustmentRepository implements StockAdjustmentRepositoryInterface
{
    public function all()
    {
        return StockAdjustment::latest()->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/stock-adjustment-list.xlsx"))) {
            unlink(public_path("uploads/csv/stock-adjustment-list.xlsx"));
        }
        return Excel::store(new StockAdjustmentExport($data), 'uploads/csv/stock-adjustment-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count, $quick_search, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = StockAdjustment::with($relational_data);

        if ($quick_search != null) {
            $items = $items->whereLike(['ref_no', 'reason', 'date', 'recovery_amount'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = StockAdjustment::count();

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

    public function create(array $data)
    {

        $type = explode('-', $data['warehouse_id']);
        if ($type[0] == "warehouse") {
            $w = WareHouse::find($type[1]);
        }else {
            $w = ShowRoom::find($type[1]);
        }
        $stock_adjustment = new StockAdjustment;
        $stock_adjustment->ref_no = $data['ref_no'];
        $stock_adjustment->recovery_amount = $data['recovery_amount'];
        $stock_adjustment->date = date('Y-m-d', strtotime($data['date']));
        $stock_adjustment->reason = $data['notes'];
        $stock_adjustment->adjustable_id = $w->id;
        $stock_adjustment->adjustable_type = get_class($w);
        $stock_adjustment->save();
        if (!empty($data['product_id'])) {
            foreach ($data['product_id'] as $key => $id) {
                $price = ProductSku::find($id)->purchase_price;
                $sub_total = $price * $data['product_quantity'][$key];
                $stock_adjustment_product = new StockAdjustmentProduct;
                $stock_adjustment_product->stock_adjustment_id = $stock_adjustment->id;
                $stock_adjustment_product->unit_price = $price;
                $stock_adjustment_product->product_sku_id = $data['product_id'][$key];
                $stock_adjustment_product->qty = $data['product_quantity'][$key];
                $stock_adjustment_product->subtotal = $sub_total;
                $stock_adjustment_product->save();
                $productHistory = new ProductHistory([
                    'type' => 'stock_adjustment',
                    'date' => Carbon::now()->toDateString(),
                    'in_out' => $data['product_quantity'][$key],
                    'product_sku_id' => $data['product_id'][$key],
                    'itemable_id' => $w->id,
                    'itemable_type' => get_class($w),
                ]);
                $stock_adjustment->houses()->save($productHistory);
            }
        }
    }

    public function find($id)
    {
        return StockAdjustment::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $type = explode('-', $data['warehouse_id']);
        if ($type[0] == "warehouse") {
            $w = WareHouse::find($type[1]);
        }else {
            $w = ShowRoom::find($type[1]);
        }
        $stock_adjustment = StockAdjustment::find($id);
        $stock_adjustment->ref_no = $data['ref_no'];
        $stock_adjustment->recovery_amount = $data['recovery_amount'];
        $stock_adjustment->date = date('Y-m-d', strtotime($data['date']));
        $stock_adjustment->reason = $data['notes'];
        $stock_adjustment->adjustable_id = $w->id;
        $stock_adjustment->adjustable_type = get_class($w);
        $stock_adjustment->save();
        foreach ($stock_adjustment->houses as $productHistory) {
            $productHistory->delete();
        }
        foreach ($stock_adjustment->stock_adjustments_products as $stock_adjustments_product) {
            $stock_adjustments_product->delete();
        }

        if (!empty($data['product_id'])) {
            foreach ($data['product_id'] as $key => $id) {
                $price = ProductSku::find($id)->purchase_price;
                $sub_total = $price * $data['product_quantity'][$key];
                $stock_adjustment_product = new StockAdjustmentProduct;
                $stock_adjustment_product->stock_adjustment_id = $stock_adjustment->id;
                $stock_adjustment_product->unit_price = $price;
                $stock_adjustment_product->product_sku_id = $data['product_id'][$key];
                $stock_adjustment_product->qty = $data['product_quantity'][$key];
                $stock_adjustment_product->subtotal = $sub_total;
                $stock_adjustment_product->save();
                $productHistory = new ProductHistory([
                    'type' => 'stock_adjustment',
                    'date' => Carbon::now()->toDateString(),
                    'in_out' => $data['product_quantity'][$key],
                    'product_sku_id' => $data['product_id'][$key],
                    'itemable_id' => $w->id,
                    'itemable_type' => get_class($w),
                ]);
                $stock_adjustment->houses()->save($productHistory);
            }
        }
    }

    public function delete($id)
    {
        $stock_adjustment = StockAdjustment::find($id);
        foreach ($stock_adjustment->houses as $history){
            $history->delete();
        }
        foreach ($stock_adjustment->stock_adjustments_products as $stock_adjustments_product){
            $stock_adjustments_product->findOrFail($stock_adjustments_product->id)->delete();
        }
        $stock_adjustment->status = 1;
        $stock_adjustment->save();
        return StockAdjustment::findOrFail($id)->delete();
    }

    public function statusChange($id)
    {
        $stock_adjustment = StockAdjustment::with(['houses','adjustable'])->find($id);
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $total_amount = $stock_adjustment->stock_adjustments_products->sum('subtotal');

            $credit_amounts[] = $total_amount;
            $credit_account_id[] = Settings('default_purchase_account');
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
            $credit_narration[] = "Stock Adjustment Journal for - " . $stock_adjustment->ref_no;

            $debit_amounts[] = $stock_adjustment->recovery_amount;
            $debit_account_id[] = $stock_adjustment->adjustable->morph->id;
            $debit_partner_id[] = 0;
            $debit_cash_flow_account_id[] = 0;
            $debit_narration[] = "Stock Adjustment Journal for Recovery Amount - " . $stock_adjustment->ref_no;

            $journalRecieveRepository = new ProJournalRepository();
            $voucher = $journalRecieveRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount' => $total_amount,
                'date' => now(),
                'credit_account_id' => $credit_account_id,
                'credit_sub_account_id' => $credit_partner_id,
                'credit_cash_flow_account_id' => $credit_cash_flow_account_id,
                'credit_account_amount' => $credit_amounts,
                'credit_narration' => $credit_narration,
                'narration_voucher' => "Stock Adjustment Journal for - " . $stock_adjustment->ref_no,
                'referable_type' => get_class($stock_adjustment),
                'referable_id' => $stock_adjustment->id,
                'is_invoiced' => 0,
                'is_manual_entry' => 1,

                'debit_account_id' => $debit_account_id,
                'debit_sub_account_id' => $debit_partner_id,
                'debit_cash_flow_account_id' => $debit_cash_flow_account_id,
                'debit_account_amount' => $debit_amounts,
                'debit_narration' => $debit_narration,
                'is_approve' => 1,
                'sale_or_purchase' => "adjustment",
                'ref_no' => $stock_adjustment->ref_no,
                'is_sale_purchase' => 1,
            ]);
        }
        foreach ($stock_adjustment->houses as $history){
            $history->status =  1;
            $history->save();
            $stocks = StockReport::where('houseable_type', $history->itemable_type)->where('houseable_id', $history->itemable_id)->where('product_sku_id', $history->product_sku_id)->first();
            $stocks->stock -= $history->in_out;
            $stocks->out += $history->in_out;
            $stocks->save();
        }
        $stock_adjustment->status = 1;
        $stock_adjustment->save();
        
        if (Settings('current_active_accounting') != "pro") {
            $sub_account_id[] = ChartAccount::where('code', '01-07')->first()->id;
            $sub_amount[] = $stock_adjustment->recovery_amount;
            $sub_narration[] = 'Product Adjustment for '.$stock_adjustment->adjustable->name;

            $repo = new JournalRepository();
            
            $repo->create([
                'voucher_type' => 'JV',
                'amount' => $stock_adjustment->recovery_amount,
                'date' => Carbon::now()->format('Y-m-d'),
                'account_type' => 'debit',
                'payment_type' => 'journal_voucher',
                'account_id' => $stock_adjustment->adjustable->contact->id,
                'main_amount' => $stock_adjustment->recovery_amount,
                'narration' => 'Product Adjustment for '.$stock_adjustment->adjustable->name,

                'sub_account_id' => array_reverse($sub_account_id),
                'sub_amount' => array_reverse($sub_amount),
                'sub_narration' => array_reverse($sub_narration),

                'sale_id' => $stock_adjustment->id,
                'sale_class' => get_class($stock_adjustment),
                'is_approve' => 1,
                'is_purchase' => 0,
            ]);
        }
        

        return $stock_adjustment;
    }

    public function checkQuantity($data)
    {
        $type = explode('-', $data['house']);
        if ($type[0] == "warehouse") {
            $house = WareHouse::find($type[1]);
        }else {
            $house = ShowRoom::find($type[1]);
        }
        return $house->stocks()->where('product_sku_id',$data['id'])->first();

    }
}
