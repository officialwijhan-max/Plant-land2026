<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * transaction.index.cost (the Cost Report, gated by the 'permission'
 * middleware - see Modules/Account/Routes/web.php) never had a row in
 * the permissions table, so no non-system_user role could ever see or
 * open it - the menu link stays hidden (permissionCheck() returns
 * false) and the route itself 401s. Adding it under the same "Reports"
 * group (parent_id 313) as the sibling transaction.index/statement.index/
 * profit.index/account.balance.index rows.
 *
 * Re-run RoleSeeder (`php artisan db:seed --class="\DemoDataSeeder"` or
 * just the role sync) after this to attach it to Accountant/Head of
 * Accountants - RoleSeeder resyncs by module_id, so this is picked up
 * automatically, no seeder code change needed.
 */
return new class extends Migration
{
    public function up()
    {
        if (DB::table('permissions')->where('route', 'transaction.index.cost')->exists()) {
            return;
        }

        DB::table('permissions')->insert([
            'id' => 5001,
            'module_id' => 18,
            'parent_id' => 313,
            'name' => 'Cost Report',
            'route' => 'transaction.index.cost',
            'type' => 3,
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }

    public function down()
    {
        DB::table('permissions')->where('route', 'transaction.index.cost')->delete();
    }
};
