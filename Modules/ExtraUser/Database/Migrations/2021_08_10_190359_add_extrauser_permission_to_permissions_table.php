<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtrauserPermissionToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = [

            // Dashboard
            ['id' => 1036, 'module_id' => 9, 'parent_id' => 163, 'name' => 'Extra User', 'route' => 'extra_user', 'type' => 2],

            ['id' => 1037, 'module_id' => 9, 'parent_id' => 1036, 'name' => 'Create', 'route' => 'extrauser.store', 'type' => 3],
            ['id' => 1038, 'module_id' => 9, 'parent_id' => 1036, 'name' => 'Agency List', 'route' => 'agencies.index', 'type' => 3],
            ['id' => 1039, 'module_id' => 9, 'parent_id' => 1036, 'name' => 'Strategic Partner List', 'route' => 'strategic-partner.index', 'type' => 3],
            ['id' => 1040, 'module_id' => 9, 'parent_id' => 1036, 'name' => 'Bench List', 'route' => 'benches.index', 'type' => 3],
            ['id' => 1041, 'module_id' => 9, 'parent_id' => 1036, 'name' => 'Kiosk List', 'route' => 'kiosks.index', 'type' => 3],

            ['id' => 1041, 'module_id' => 13, 'parent_id' => 225, 'name' => 'Affiliate Sale List', 'route' => 'affiliate.index', 'type' => 2],
            ['id' => 1041, 'module_id' => 13, 'parent_id' => 225, 'name' => 'Commissioner Sale List', 'route' => 'commissioner.index', 'type' => 2],
        ];

        DB::table('permissions')->insert($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
