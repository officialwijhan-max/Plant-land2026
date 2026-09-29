<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeReturnDateNullableOnProductItemDetails extends Migration
{
    /**
     * product_item_details.return_date was created as a required timestamp
     * with no default, even though its sibling columns (return_quantity,
     * return_amount) both default to 0/"not returned yet" - most line
     * items are never returned, so this should never have been required.
     * On a strict-mode MySQL server this makes every single sale/purchase
     * line insert fail outright unless the app happens to pass a
     * return_date explicitly (it doesn't, per SaleRepository/
     * PurchaseOrderRepository) - this likely only "worked" in production
     * because that MySQL connection isn't running in strict mode.
     *
     * @return void
     */
    public function up()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return; // SQLite has no ALTER ... MODIFY; nullability isn't enforced there anyway.
        }
        DB::unprepared('ALTER TABLE `product_item_details` CHANGE `return_date` `return_date` TIMESTAMP NULL DEFAULT NULL');
    }

    /**
     * @return void
     */
    public function down()
    {
        //
    }
}
