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
        Schema::create('pro_banking_statements', function (Blueprint $table) {
            $table->id();
            $table->date('date')->default(date("Y-m-d"));
            $table->unsignedBigInteger('leadger_id')->nullable();
            $table->double('balance', 28,5)->default(0);
            $table->boolean('re_conciled')->default(0);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("re_conciled_by")->nullable();
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
        Schema::dropIfExists('pro_banking_statements');
    }
};
