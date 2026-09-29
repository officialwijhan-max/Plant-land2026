<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ChartAccount's $fillable, BankAccountRepository::create(), and
 * ChartAccountRepository both read/write `is_actual_bank`, but no
 * migration ever created the column - a pre-existing gap in the shipped
 * app, not something introduced by demo seeding. Without it, the native
 * "Add Bank Account" feature (Settings > Accounts > Bank Accounts) fails
 * on any fresh install with a "column not found" error, same as
 * BankAccountSeeder hit here.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->boolean('is_actual_bank')->default(0)->after('configuration_group_id');
        });
    }

    public function down()
    {
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->dropColumn('is_actual_bank');
        });
    }
};
