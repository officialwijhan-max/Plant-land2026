<?php

namespace Modules\Product\Imports;

use Modules\Purchases\Entities\CostOfGoodHistory;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Product\Entities\ProductSku;
use Modules\Product\Entities\Product;
use Modules\Inventory\Entities\StockReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Modules\Account\Entities\ChartAccount;
use Modules\Product\Entities\ProductHistory;
use Modules\Account\Repositories\JournalRepository;
use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;
use Modules\ProAccount\Entities\Leadger;
use Illuminate\Support\Str;
use App\Traits\Accounts;
use Carbon\Carbon;

class ProductImport implements WithStartRow, WithCustomCsvSettings, ToCollection
{
    use Accounts;

    public function startRow(): int
    {
        return 1;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $main_amount = 0;
        foreach ($rows->skip(1) as $row) 
        {
            $product = Product::create([
                $rows[0][0] => $row[0],
                $rows[0][1] => $row[1],
                $rows[0][2] => $row[2],
                $rows[0][3] => $row[3],
                $rows[0][4] => $row[4],
                $rows[0][5] => $row[5],
                $rows[0][6] => $row[6],
                $rows[0][7] => $row[7],
                $rows[0][8] => $row[8],
            ]);
            $product_sku = ProductSku::create([
                'product_id' => $product->id,
                'barcode_id' => '1000-' . $product->id . '-' . Str::random(12),
                'barcode_type' => 'C39',
                'tax_type' => '%',
                $rows[0][9] => ($row[9] != null) ? $row[9] : '1000-' . $product->id . '-' . Str::random(12),
                $rows[0][10] => $row[10],
                $rows[0][11] => $row[11],
                $rows[0][12] => $row[12],
                $rows[0][13] => $row[13],
                $rows[0][14] => $row[14],
                $rows[0][15] => $row[15],
            ]);
            $stock_report = StockReport::create([
                'product_sku_id' => $product_sku->id,
                'houseable_id' => session()->get('showroom_id'),
                'houseable_type' => 'Modules\Inventory\Entities\ShowRoom',
                'stock_date' => date('Y-m-d'),
                $rows[0][15] => ($row[15] != null) ? $row[15] : 0,
            ]);

            if ($stock_report->stock > 0) {
                $productHistory = ProductHistory::create([
                    'type' => 'begining',
                    'date' => Carbon::now()->toDateString(),
                    'in_out' => $stock_report->stock,
                    'product_sku_id' => $stock_report->product_sku_id,
                    'itemable_id' => session()->get('showroom_id'),
                    'itemable_type' => 'Modules\Inventory\Entities\ShowRoom',
                    'houseable_id' => $product->id,
                    'houseable_type' => get_class($product),
                ]);
            }
            $main_amount += (float) ($product_sku->purchase_price ?? 0) * (int) ($stock_report->stock ?? 0);
        }
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $credit_amounts[] = $main_amount;
            $credit_account_id[] = Settings('default_capital_account');
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
            $credit_narration[] = "Add Openning Stock Entry Bulk Upload";

            $debit_amounts[] = $main_amount;
            $debit_account_id[] = Leadger::where('id', 7)->first()->id;
            $debit_partner_id[] = 0;
            $debit_cash_flow_account_id[] = 0;
            $debit_narration[] = "Openning Stock Bulk Upload";
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
                'referable_type'=> null,
                'referable_id'=> null,
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
            $sub_account_id[0] = ChartAccount::where('code', '02-09-11')->first()->id;
            $sub_amount[0] = $main_amount;
            $sub_narration[0] = 'Beginning Stock Added By Showroom - ' . ShowRoom::find(session()->get('showroom_id'))->name;
            if ($stock_report->stock > 0) {
                $repo = new JournalRepository();
                $repo->create([
                    'voucher_type' => 'JV',
                    'amount' => $main_amount,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'account_type' => 'debit',
                    'payment_type' => 'journal_voucher',
                    'account_id' => $this->defaultPurchaseAccount(),
                    'main_amount' => $main_amount,
                    'narration' => 'Beginning Stock Added By Showroom',
                    'sub_account_id' => $sub_account_id,
                    'sub_amount' => $sub_amount,
                    'sub_narration' => $sub_narration,
                    'sale_id' => null,
                    'sale_class' => null,
                    'is_approve' => 1,
                ]);
            }
        }
    }
} 