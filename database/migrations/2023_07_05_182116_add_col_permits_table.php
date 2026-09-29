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
        Schema::table('permissions', function (Blueprint $table) {
            $table->tinyInteger('searchable')->after('status')->default(0);
        });

        $sql = [
            ['id'  => 403, 'searchable' => 1, 'module_id' => 2, 'parent_id' => 2, 'name' => 'Print Label', 'route' => 'print_label_generate', 'type' => 2 ],
            ['id'  => 404, 'searchable' => 0, 'module_id' => 2, 'parent_id' => 403, 'name' => 'Label', 'route' => 'print_label_generate', 'type' => 3 ],
            ['id'  => 405, 'searchable' => 0, 'module_id' => 2, 'parent_id' => 403, 'name' => 'Generate', 'route' => 'print.labels', 'type' => 3 ],

            ['id'  => 406, 'searchable' => 0, 'module_id' => 13, 'parent_id' => null, 'name' => 'POS', 'route' => 'pos_menu', 'type' => 1 ],
            ['id'  => 407, 'searchable' => 1, 'module_id' => 13, 'parent_id' => 406, 'name' => 'POS', 'route' => 'pos-order.products', 'type' => 2 ],
            ['id'  => 408, 'searchable' => 1, 'module_id' => 13, 'parent_id' => 406, 'name' => 'POS Sale', 'route' => 'pos-order.index', 'type' => 2 ],
        ];
        DB::table('permissions')->insert($sql);

        DB::statement("INSERT INTO `role_permission` (`permission_id`, `role_id`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
                        (403, 3, 1, 1, 1, NULL, NULL),
                        (404, 3, 1, 1, 1, NULL, NULL),
                        (405, 3, 1, 1, 1, NULL, NULL),
                        (406, 3, 1, 1, 1, NULL, NULL),
                        (407, 3, 1, 1, 1, NULL, NULL),
                        (408, 3, 1, 1, 1, NULL, NULL);"
                    );
        DB::statement("UPDATE `permissions` SET `searchable`=1, `route` = 'home' WHERE id=1;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=289;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=290;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=295;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=296;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=301;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=308;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=283;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=314;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=315;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=316;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=317;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=318;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=319;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=320;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=316;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=315;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=62;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=164;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=165;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=169;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=318;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=320;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=322;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=233;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=239;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=1008;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=248;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=1009;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=203;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=171;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=325;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=175;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=324;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=931;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=326;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=38;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=50;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=37;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=31;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=15;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=20;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=10;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=3;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=218;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=950;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=223;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=930;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=255;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=226;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=231;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=89;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=125;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=121;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=117;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=111;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=106;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=95;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=1011;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=1012;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=800;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=1025;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=240;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=178;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=195;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=184;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=189;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=188;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=1017;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=191;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=192;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=338;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=339;");
        DB::statement("UPDATE `permissions` SET `searchable`=1 WHERE id=193;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            //
        });
    }
};
