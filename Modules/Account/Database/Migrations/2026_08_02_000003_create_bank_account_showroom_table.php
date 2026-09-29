<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a BankAccount be scoped to one branch (dedicated) or several
 * (shared), instead of every bank account being visible/usable from
 * every branch with no way to restrict it - which was the previous
 * behavior (AccountController::cash_bank_account_select /
 * ChartAccountRepository::listForSelectAccountCashBank() returned every
 * cash+bank account system-wide regardless of the current branch).
 *
 * A bank account with NO rows here is treated as shared with every
 * branch - this keeps existing single-branch setups working unchanged;
 * branch scoping only kicks in once an account is explicitly assigned.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('bank_account_showroom', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_account_id');
            $table->unsignedBigInteger('showroom_id');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade');
            $table->foreign('showroom_id')->references('id')->on('show_rooms')->onDelete('cascade');
            $table->unique(['bank_account_id', 'showroom_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_account_showroom');
    }
};
