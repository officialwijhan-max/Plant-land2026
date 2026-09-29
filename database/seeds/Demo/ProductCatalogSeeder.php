<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSku;

/**
 * The client's real product catalog (trees, ground plants, irrigation
 * supplies, hardscape materials) - 76 products across the 3 categories
 * seeded by ReferenceDataSeeder. Also seeds opening stock in the default
 * "Main Branch" showroom (id 1) so PurchaseSeeder/SaleSeeder have real
 * stock to move against.
 *
 * category_id: 1 = أعمال زراعة (planting), 2 = اعمال هارد سكيب (hardscape),
 * 3 = اعمال رى (irrigation). unit_type_id matches ReferenceDataSeeder.
 */
class ProductCatalogSeeder extends Seeder
{
    public function run()
    {
        if (Product::count() > 0) {
            $this->command->info('products already has data, skipping.');
            return;
        }

        $adminId = DB::table('users')->where('role_id', 1)->value('id') ?? 1;
        $today = now()->toDateString();

        // [name, category_id, unit_type_id, purchase_price, selling_price, min_selling_price, cost_of_goods, stock, notes]
        $rows = [
            ['كف مريم ابيض', 1, 4, 100, 150, 140, 100, 10, null],
            ['خيار شنبر', 1, 4, 200, 500, 450, 200, 10, 'في صندوق'],
            ['جهنميه بلدي', 1, 4, 100, 200, 180, 100, 15, 'في صندوق'],
            ['جهنميه جلابرا', 1, 4, 80, 150, 140, 80, 10, null],
            ['ايفوربيا', 1, 4, 5, 20, 15, 5, 40, null],
            ['صبار مشكل', 1, 4, 2, 20, 15, 2, 100, null],
            ['بونسيانا', 1, 4, 100, 350, 320, 100, 29, 'في صندوق'],
            ['لبخ', 1, 4, 150, 400, 350, 150, 10, 'في صندوق'],
            ['اكاسيا ندوزا', 1, 4, 500, 800, 750, 500, 10, 'في صندوق'],
            ['اكاسيا جلوكه', 1, 4, 200, 600, 500, 200, 10, 'في صندوق'],
            ['خف الجمل', 1, 4, 200, 500, 450, 200, 15, null],
            ['جكارندا', 1, 4, 200, 600, 500, 200, 26, 'في صفيحة'],
            ['ياسمين هندي', 1, 4, 200, 500, 400, 200, 24, 'في صندوق'],
            ['فرشه زجاج', 1, 4, 150, 450, 400, 150, 14, 'في صندوق'],
            ['بنجامينا', 1, 4, 150, 250, 230, 150, 30, null],
            ['انتناطه', 1, 4, 20, 50, 45, 20, 14, 'في قصاري'],
            ['تريمناليا', 1, 4, 400, 800, 700, 400, 10, 'في صندوق'],
            ['اوجستا', 1, 4, 150, 600, 500, 150, 265, 'في قصاري'],
            ['تمر حنه', 1, 4, 150, 400, 350, 150, 43, 'في قصاري'],
            ['دورنتا ليمون', 1, 4, 10, 30, 25, 10, 15, 'في قصاري'],
            ['بونظاي', 1, 4, 200, 400, 350, 200, 10, 'في قصاري'],
            ['اريكا صفراء', 1, 4, 300, 900, 800, 300, 10, 'في صندوق'],
            ['دراسينا', 1, 4, 150, 500, 450, 150, 37, 'في صندوق'],
            ['دركوا', 1, 4, 300, 600, 500, 300, 10, 'في قصاري'],
            ['أرليا كسترو', 1, 4, 50, 120, 100, 50, 12, 'في قصاري'],
            ['رابس', 1, 4, 100, 250, 200, 100, 90, 'في قصاري'],
            ['سيكاس', 1, 4, 200, 750, 600, 200, 33, 'في صندوق'],
            ['زاميا', 1, 4, 500, 800, 700, 500, 10, 'في صندوق'],
            ['شمادوريا', 1, 4, 100, 300, 250, 100, 95, 'في قصاري'],
            ['مانجو', 1, 4, 100, 350, 300, 100, 10, 'في صندوق'],
            ['فلفل ناعم', 1, 4, 400, 750, 650, 400, 18, 'في صندوق'],
            ['زيتون بعرشه', 1, 4, 500, 800, 700, 500, 24, 'في صندوق'],
            ['زيتون جديد', 1, 4, 1000, 2000, 1800, 1000, 79, null],
            ['زيتون قديم', 1, 4, 1000, 2200, 2000, 1000, 10, null],
            ['كايا', 1, 4, 500, 700, 600, 500, 10, 'في صندوق'],
            ['فوكستيل', 1, 4, 2000, 3500, 3000, 2000, 10, 'في صندوق'],
            ['ملوكي', 1, 4, 2500, 4000, 3500, 2500, 12, 'في صندوق'],
            ['كوكس', 1, 4, 500, 1000, 800, 500, 15, 'في صندوق'],
            ['كرزيا', 1, 4, 100, 250, 200, 100, 13, null],
            ['كوردالين', 1, 4, 10, 25, 20, 10, 10, 'في قصاري'],
            ['دورنتا مبرقشه', 1, 4, 20, 50, 40, 20, 15, null],
            ['ليكوفليم', 1, 4, 20, 50, 40, 20, 10, null],
            ['جهنميه مقزمه', 1, 4, 80, 120, 110, 80, 10, null],
            ['كف مريم احمر', 1, 4, 30, 50, 40, 30, 35, null],
            ['فيكس نتدا', 1, 4, 200, 350, 320, 200, 10, null],
            ['بنجامينا كبير', 1, 4, 150, 300, 270, 150, 10, null],
            ['بكابوديوم', 1, 4, 100, 250, 230, 100, 15, null],
            ['فيكس لسان عصفور', 1, 4, 100, 200, 180, 100, 10, null],
            ['تيكوما صفراء', 1, 4, 100, 300, 270, 100, 10, 'في صندوق'],
            ['جازانيا', 1, 4, 1, 10, 8, 1, 10, null],
            ['الزلط', 2, 6, 45, 90, 50, 45, 20, null],
            ['كمبوست', 1, 6, 45, 90, 50, 45, 20, null],
            ['جيكوستيل', 2, 2, 11.5, 25, 20, 11.5, 50, null],
            ['محبس - كهرباء هانتر 1 بوصه', 3, 4, 1150, 1725, 1610, 1150, 32, 'كهرباء هانتر 1 بوصه'],
            ['محبس - يدوي 1 بوصه كومر', 3, 4, 225, 337.5, 315, 225, 16, 'يدوي 1 بوصه كومر'],
            ['راس خط 32 *4/3 كومر', 3, 4, 16, 24, 22.4, 16, 36, null],
            ['جلبه بسن داخلي 4/3 1بوصه اكوا بولي', 3, 4, 70, 105, 98, 70, 13, null],
            ['لاصق 717 امريكي', 3, 7, 250, 375, 350, 250, 32, null],
            ['خراطيم ساده 50 الصفاء', 3, 3, 175, 262.5, 245, 175, 13, 'ساده 50 الصفاء'],
            ['خراطيم تنقيط 50 الصفاء', 3, 3, 175, 262.5, 245, 175, 28, 'تنقيط 50 الصفاء'],
            ['راس خط 32 كومر', 3, 4, 15, 22.5, 21, 15, 18, null],
            ['طبه 32 كومر', 3, 4, 12, 18, 16.8, 12, 50, null],
            ['نبل 1 بوصه كومر', 3, 4, 15, 22.5, 21, 15, 270, null],
            ['غرفه دائري 10 بوصه', 3, 4, 75, 112.5, 105, 75, 75, null],
            ['متر مراسير 32 كومر', 3, 2, 18, 27, 25.2, 18, 292, null],
            ['خراطيم تنقيط 100م الصفا', 3, 3, 335, 502.5, 469, 335, 12, null],
            ['بكره تيفلون كبيره', 3, 4, 30, 45, 42, 30, 35, null],
            ['تيه 16 مستورد', 3, 4, 5, 7.5, 7, 5, 81, null],
            ['كوع 16 مستورد', 3, 4, 4, 6, 5.6, 4, 9, null],
            ['وصله 16 مستورد', 3, 4, 3, 4.5, 4.2, 3, 10, null],
            ['نضاره', 3, 4, 0.5, 0.75, 0.7, 0.5, 11, null],
            ['لوحه هانتر 6 خط', 3, 4, 6700, 10050, 9380, 6700, 3, null],
            ['حربه تثبيت', 3, 4, 2.25, 3.375, 3.15, 2.25, 200, null],
            ['ديب واي', 3, 4, 90, 135, 126, 90, 40, null],
            ['رمل', 2, 6, 60, 100, 100, 60, 2, null],
            ['اسمنت', 2, 6, 100, 120, 120, 100, 2, null],
        ];

        $count = 0;
        foreach ($rows as [$name, $categoryId, $unitTypeId, $purchasePrice, $sellingPrice, $minSellingPrice, $costOfGoods, $stock, $notes]) {
            $product = Product::create([
                'product_name' => $name,
                'product_type' => 'Single',
                'unit_type_id' => $unitTypeId,
                'category_id' => $categoryId,
                'description' => $notes,
                'created_by' => $adminId,
            ]);

            $sku = ProductSku::create([
                'product_id' => $product->id,
                'sku' => 'PL-' . str_pad($product->id, 5, '0', STR_PAD_LEFT),
                'stock_quantity' => $stock,
                'alert_quantity' => 10,
                'purchase_price' => $purchasePrice,
                'selling_price' => $sellingPrice,
                'min_selling_price' => $minSellingPrice,
                'cost_of_goods' => $costOfGoods,
                'tax' => 0,
            ]);

            // Opening stock in the default "Main Branch" showroom (id 1,
            // auto-created by Inventory's create_show_rooms_table migration).
            DB::table('stock_reports')->insert([
                'houseable_id' => 1,
                'houseable_type' => \Modules\Inventory\Entities\ShowRoom::class,
                'stock_date' => $today,
                'product_sku_id' => $sku->id,
                'stock' => $stock,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $count++;
        }

        $this->command->info("Seeded {$count} products with SKUs and opening stock.");
    }
}
