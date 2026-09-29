<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit trail for mandatory top-up/deposit entries used to cover a Cash
 * Vault or Bank Account shortfall before a blocked payout can proceed
 * (see App\Traits\ChecksAccountBalance). The actual balance change is
 * posted as a real journal entry (Dr the target account, Cr the app's
 * "Capital (Opening Balance Purpose)" account) - this table just records
 * who/why/when for reporting, since `source` is a required field per the
 * business rule and isn't captured anywhere in the generic Voucher model.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('account_deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chart_account_id');
            $table->foreign('chart_account_id')->references('id')->on('chart_accounts')->onDelete('cascade');
            $table->double('amount', 16, 2);
            $table->string('source');
            $table->date('date');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_deposits');
    }
};
