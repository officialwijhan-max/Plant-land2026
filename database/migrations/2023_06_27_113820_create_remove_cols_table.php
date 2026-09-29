<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite') {
            // SQLite's ALTER TABLE only supports one column operation per
            // statement (no comma-separated multi-DROP like MySQL).
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn(['state', 'city']);
            });
        } else {
            \Illuminate\Support\Facades\DB::unprepared('ALTER TABLE `contacts` DROP `state`, DROP `city`');
        }

        $sql = [

            ['id'  => 401, 'module_id' => 14, 'parent_id' => 233, 'name' => 'Print', 'route' => 'stock-transfer.print_view', 'type' => 3 ],
            ['id'  => 402, 'module_id' => 13, 'parent_id' => 223, 'name' => 'Return', 'route' => 'purchase.order.return', 'type' => 3 ],
    
        ];
    
        DB::table('permissions')->insert($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remove_cols');
    }
};
