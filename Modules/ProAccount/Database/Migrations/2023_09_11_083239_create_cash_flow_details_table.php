<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashFlowDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_flow_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('showroom_id')->nullable();
            $table->unsignedBigInteger('voucher_id')->unsigned();
            $table->unsignedBigInteger('cash_flow_account_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('type')->nullable()->comment('dr/cr');
            $table->double('amount', 28,4)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_flow_details');
    }
}
