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
        Schema::create('pro_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->default(date("Y-m-d"));
            $table->unsignedBigInteger('showroom_id')->nullable();
            $table->boolean('is_approve')->default(0)->comment('0 => pending, 1 => Approve, 2 => Cancelled');
            $table->unsignedBigInteger('voucher_id')->unsigned();
            $table->unsignedBigInteger('leadger_id')->unsigned();
            $table->integer('sub_leadger_id')->default(0);
            $table->string('type')->nullable()->comment('dr / cr');
            $table->double('amount', 28,4)->default(0);
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('banking_statement_detail_id')->nullable();
            $table->boolean('is_reconciled')->default(0);
            $table->date('reconciled_date')->default(date("Y-m-d"));
            $table->boolean('is_closing')->default(0);
            $table->boolean('is_opening')->default(0);
            $table->double('last_balance', 28,4)->default(0);
            $table->boolean('is_cash_flow_journal')->default(0);
            $table->unsignedBigInteger('accounting_period_id')->nullable();
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
        Schema::dropIfExists('pro_transactions');
    }
};
