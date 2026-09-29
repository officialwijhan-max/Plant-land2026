<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pro_financial_year_leadger_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('showroom_id')->nullable();
            $table->unsignedBigInteger('leadger_id')->unsigned();
            $table->unsignedBigInteger('accounting_period_id')->unsigned();
            $table->double('balance', 28,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_financial_year_leadger_balances');
    }
};
