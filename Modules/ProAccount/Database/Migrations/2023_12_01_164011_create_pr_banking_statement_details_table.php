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
        Schema::create('pro_banking_statement_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banking_statement_id')->nullable();
            $table->date('date')->default(date("Y-m-d"));
            $table->text('narration')->nullable();
            $table->double('amount', 28,5)->default(0);
            $table->boolean('is_matched')->default(0);
            $table->boolean('sign')->default(0)->comment("1 => positive ; 0 => negative");
            $table->tinyInteger('is_approve')->default(0)->comment('0 => pending, 1 => Approve, 2 => Cancelled');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->unsignedBigInteger("checked_by")->nullable();
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
        Schema::dropIfExists('pro_banking_statement_details');
    }
};
