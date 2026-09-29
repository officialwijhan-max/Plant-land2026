<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMinSellingPriceToDouble extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite') {
            return; // SQLite is dynamically typed; column precision isn't enforced there anyway.
        }
        \Illuminate\Support\Facades\DB::unprepared('ALTER TABLE product_sku CHANGE min_selling_price min_selling_price DOUBLE(16, 2) NOT NULL DEFAULT 0.00');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
