<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Unit types, product categories, and tax rates for the landscaping
 * business, matching the client's own reference lists (unit_type_list.md,
 * category.md) exactly - including IDs, since ProductCatalogSeeder's
 * product data references these IDs directly.
 */
class ReferenceDataSeeder extends Seeder
{
    public function run()
    {
        $adminId = DB::table('users')->where('role_id', 1)->value('id') ?? 1;

        if (DB::table('unit_types')->count() === 0) {
            DB::table('unit_types')->insert([
                ['id' => 1, 'name' => 'قطعة', 'description' => 'بيع بالقطعة', 'status' => 1, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'name' => 'متر مربع', 'description' => null, 'status' => 1, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 3, 'name' => 'متر طولى', 'description' => null, 'status' => 1, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 4, 'name' => 'عدد', 'description' => null, 'status' => 1, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 5, 'name' => 'طن', 'description' => null, 'status' => 1, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 6, 'name' => 'شكارة', 'description' => null, 'status' => 1, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 7, 'name' => 'عبوة', 'description' => null, 'status' => 1, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 8, 'name' => 'مقطوعية', 'description' => null, 'status' => 1, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 9, 'name' => 'عود', 'description' => null, 'status' => 1, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
            ]);
            $this->command->info('Seeded 9 unit types.');
        } else {
            $this->command->info('unit_types already has data, skipping.');
        }

        if (DB::table('categories')->count() === 0) {
            DB::table('categories')->insert([
                ['id' => 1, 'name' => 'أعمال زراعة', 'code' => '01', 'description' => null, 'status' => 1, 'level' => 0, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'name' => 'اعمال هارد سكيب', 'code' => null, 'description' => null, 'status' => 1, 'level' => 0, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 3, 'name' => 'اعمال رى', 'code' => null, 'description' => null, 'status' => 1, 'level' => 0, 'created_by' => $adminId, 'created_at' => now(), 'updated_at' => now()],
            ]);
            $this->command->info('Seeded 3 product categories.');
        } else {
            $this->command->info('categories already has data, skipping.');
        }

        if (DB::table('taxes')->count() === 0) {
            DB::table('taxes')->insert([
                ['id' => 1, 'name' => 'No Tax', 'description' => null, 'rate' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'name' => 'VAT 14%', 'description' => 'Egypt standard VAT rate', 'rate' => 14, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
            $this->command->info('Seeded 2 tax rates.');
        } else {
            $this->command->info('taxes already has data, skipping.');
        }
    }
}
