<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\ProAccount\Entities\FinancialYear;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pro_financial_years', function (Blueprint $table) {
            $table->id();
            $table->date('start_date')->default(date("Y-m-d"));
            $table->date('end_date')->nullable();
            $table->boolean("is_locked")->default(0);
            $table->timestamps();
        });

        FinancialYear::create(['start_date' => \Carbon\Carbon::now()->format('Y-m-d')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_financial_years');
    }
};
