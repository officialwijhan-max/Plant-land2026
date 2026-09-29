<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddProAccountToModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $modules = [];
        $modules[] = ['name' => 'ProAccount', 'details' => "This is Por account module for Infix Biz. Thanks for using.", 'created_at' => now(), 'updated_at' => now(), 'order' => 1];

        DB::table('modules')->insert(
            $modules
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Modules\ModuleManager\Entities\Module::where('name', 'ProAccount')->delete();
    }
}
