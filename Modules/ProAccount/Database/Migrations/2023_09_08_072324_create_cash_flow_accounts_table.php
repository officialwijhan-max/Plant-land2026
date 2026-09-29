<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCashFlowAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_flow_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(0);
            $table->string('code')->unique();
            $table->string('type')->nullable()->comment('3 => Expence, 4 => Income');
            $table->string('name')->nullable();
            $table->boolean("is_active")->default(1);
            $table->boolean("is_blocked")->default(0);
            $table->timestamps();
        });

        DB::table('cash_flow_accounts')->insert(array (
          0 => 
          array (
            'company_id' => 0,
            'code' => 'CFC-10001',
            'type' => '4',
            'name' => 'Cash In Flow',
            'is_active' => 1,
            'is_blocked' => 0,
          ),
          1 => 
          array (
            'company_id' => 0,
            'code' => 'CFC-10002',
            'type' => '4',
            'name' => 'Cash In Others',
            'is_active' => 1,
            'is_blocked' => 0,
          ),
          2 => 
          array (
            'company_id' => 0,
            'code' => 'CFC-20001',
            'type' => '3',
            'name' => 'Cash Out Flow Others',
            'is_active' => 1,
            'is_blocked' => 0,
          ),
          3 => 
          array (
            'company_id' => 0,
            'code' => 'CFC-20002',
            'type' => '3',
            'name' => 'Cash Out Flow',
            'is_active' => 1,
            'is_blocked' => 0,
          ),
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_flow_accounts');
    }
}
