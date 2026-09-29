<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Inventory\Entities\StockReport;
use Modules\Inventory\Entities\StockTransfer;
use Modules\Product\Entities\ProductHistory;

class AddInOutToStockReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_reports', function (Blueprint $table) {
            $table->integer('in')->after('product_sku_id')->default(0);
            $table->integer('out')->after('in')->default(0);
        });

        $stock_reports = StockReport::all();

        foreach ($stock_reports as $stock) {
            $histories = ProductHistory::where('product_sku_id', $stock->product_sku_id)->where('itemable_type', $stock->houseable_type)->where('itemable_id', $stock->houseable_id)->get();
            $in = 0;
            $out = 0;
            foreach ($histories as $history) {
                if ($history->type == 'stock_adjustment' and $history->status) {
                    $out += $history->in_out;
                } elseif (in_array($history->type, ['begining', 'purchase', 'sales_return'])) {
                    $in += $history->in_out;
                } elseif (in_array($history->type, ['sales', 'purchase_return'])) {
                    $out += $history->in_out;
                }
            }

            $out_transfers = StockTransfer::where('sendable_id', $stock->houseable_id)->where('sendable_type', $stock->houseable_type)->with('items')->get();
            foreach ($out_transfers as $transfer) {
                $items = $transfer->items()->where('productable_id', $stock->product_sku_id)->get();

                foreach ($items as $item) {
                    $out += $item->quantity;
                }
            }

            $in_transfers = StockTransfer::where('receivable_id', $stock->houseable_id)->where('receivable_type', $stock->houseable_type)->with('items')->get();
            foreach ($in_transfers as $transfer) {
                $items = $transfer->items()->where('productable_id', $stock->product_sku_id)->get();

                foreach ($items as $item) {
                    $in += $item->quantity;
                }
            }

            $stock->in = $in;
            $stock->out = $out;

            $stock->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_reports', function (Blueprint $table) {
            $table->dropColumn(['in', 'out']);
        });
    }
}
