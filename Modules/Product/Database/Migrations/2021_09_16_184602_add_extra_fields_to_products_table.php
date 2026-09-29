<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('products', function (Blueprint $table) {
            $table->string('hsn',191)->nullable();
            $table->string('length',191)->nullable();
            $table->string('height',191)->nullable();
            $table->string('zip_length',191)->nullable();
            $table->string('flap_length',191)->nullable();
            $table->string('stitches',191)->nullable();
            $table->text('fabric')->nullable();
            $table->string('front_sheet',191)->nullable();
            $table->string('wall',191)->nullable();
            $table->string('zipper',191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['hsn', 'length', 'height', 'zip_length', 'flap_length', 'stitches', 'fabric', 'front_sheet', 'wall', 'zipper'] );
        });
    }
}
