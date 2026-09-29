<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColInGeneralSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'currency_symbol_position')) {
                $table->string('currency_symbol_position')->default('left')->after('currency_symbol');
            }
            if (!Schema::hasColumn('general_settings', 'decimal_limit')) {
                $table->string('decimal_limit')->default(2)->after('currency_symbol_position');
            }
            if (!Schema::hasColumn('general_settings', 'is_tax_return')) {
                $table->string('is_tax_return')->default('yes')->after('remarks_body');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn('currency_symbol_position');
            $table->dropColumn('decimal_limit');
            $table->dropColumn('is_tax_return');
        });
    }
}
