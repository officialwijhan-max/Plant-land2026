<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cost Centers only existed inside the disabled ProAccount module
 * (Leadger::is_cost_center) - a completely different accounting engine
 * from the live ChartAccount one everything in this app actually runs
 * on. This adds the same concept to the live engine: any account can be
 * flagged as a cost center for grouping in the Cost Center report.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->boolean('is_cost_center')->default(0)->after('is_actual_bank');
        });
    }

    public function down()
    {
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->dropColumn('is_cost_center');
        });
    }
};
