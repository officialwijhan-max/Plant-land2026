<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Need to be added permission id in deleted_ids array for Permission Update, but not need for New Permission */
        $deleted_ids = [168, 305];
        DB::table('permissions')->whereIn('id', $deleted_ids)->delete();

        $sql = [

            ['id'  => 168, 'module_id' => 9, 'parent_id' => 166, 'name' => 'Delete', 'route' => 'add_contact.delete', 'type' => 3], // for update

            ['id'  => 25, 'module_id' => 2, 'parent_id' => 20, 'name' => 'CSV Download', 'route' => 'model.csv_download', 'type' => 3],
            ['id'  => 26, 'module_id' => 2, 'parent_id' => 15, 'name' => 'CSV Download', 'route' => 'brand.csv_download', 'type' => 3],
            ['id'  => 27, 'module_id' => 2, 'parent_id' => 10, 'name' => 'CSV Download', 'route' => 'unit_type.csv_download', 'type' => 3],

            ['id'  => 216, 'module_id' => 13, 'parent_id' => 218, 'name' => 'Convert to Purchase', 'route' => 'convert.purchase', 'type' => 3],
            ['id'  => 345, 'module_id' => 13, 'parent_id' => 226, 'name' => 'Clone to Sale', 'route' => 'sale.quotation_to_store', 'type' => 3],

            ['id'  => 305, 'module_id' => 18, 'parent_id' => 301, 'name' => 'Delete', 'route' => 'bank.account.delete', 'type' => 3], //for update
            ['id'  => 501, 'module_id' => 18, 'parent_id' => 301, 'name' => 'CSV Upload', 'route' => 'bank_account.csv_upload.create', 'type' => 3],

            ['id'  => 502, 'module_id' => 20, 'parent_id' => 244, 'name' => 'Show', 'route' => 'showroom.destroy', 'type' => 3],

            ['id'  => 327, 'module_id' => 10, 'parent_id' => 171, 'name' => 'Leave download', 'route' => 'leave.application.download', 'type' => 3],
            ['id'  => 328, 'module_id' => 10, 'parent_id' => 171, 'name' => 'Update Carry Forward', 'route' => 'carry.forward.update', 'type' => 3],

            ['id'  => 934, 'module_id' => 10, 'parent_id' => 931, 'name' => 'Holiday Store', 'route' => 'holidays.store', 'type' => 3],
            ['id'  => 935, 'module_id' => 10, 'parent_id' => 931, 'name' => 'Holiday Delete', 'route' => 'holiday.delete', 'type' => 3],

            ['id'  => 915, 'module_id' => 18, 'parent_id' => 264, 'name' => 'Transfer Money ', 'route' => 'transfer_showroom.index', 'type' => 2],
            ['id'  => 916, 'module_id' => 18, 'parent_id' => 915, 'name' => 'Make Money Transfer', 'route' => 'transfer_showroom.create', 'type' => 3],
            ['id'  => 917, 'module_id' => 18, 'parent_id' => 915, 'name' => 'Make Money Transfer Update', 'route' => 'transfer_showroom.edit', 'type' => 3],
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
        Schema::table('', function (Blueprint $table) {
        });
    }
}
