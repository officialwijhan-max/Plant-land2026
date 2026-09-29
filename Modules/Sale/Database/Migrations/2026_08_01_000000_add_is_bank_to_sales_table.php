<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsBankToSalesTable extends Migration
{
    /**
     * SaleController/SaleRepository/HomeController have always read and
     * written a `sales.is_bank` column (1 => bank payment, 0 => cash/due),
     * but no migration ever created it — it only existed on the old
     * database because of an undocumented manual ALTER TABLE at some point
     * in its history. A fresh `migrate` never had it, breaking the
     * dashboard's bank/cash totals.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('sales', 'is_bank')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->tinyInteger('is_bank')->default(0)->comment('0 => Cash/Due, 1 => Bank');
            });
        }
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('is_bank');
        });
    }
}
