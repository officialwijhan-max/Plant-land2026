<?php

use Illuminate\Database\Seeder;

/**
 * Full test dataset for the landscaping business: real product catalog,
 * roles/employees, clients/suppliers, projects, purchases/sales, and
 * ~2 months of attendance/payroll.
 *
 * Deliberately NOT part of the default `php artisan db:seed` (which only
 * runs SuperAdminSeeder - see database/seeds/DatabaseSeeder.php). Run this
 * on its own, only when you actually want demo data:
 *
 *   php artisan db:seed --class=DemoDataSeeder
 *
 * Every individual seeder below is idempotent (skips if its table already
 * has data), so re-running this is safe.
 */
class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ReferenceDataSeeder::class,
            ProductCatalogSeeder::class,
            RoleSeeder::class,
            EmployeeSeeder::class,
            ContactSeeder::class,
            ProjectSeeder::class,
            BankAccountSeeder::class,
            ChartAccountSeeder::class,
            CapitalInjectionSeeder::class,
            PurchaseSeeder::class,
            SaleSeeder::class,
            AttendanceSeeder::class,
            PayrollSeeder::class,
        ]);
    }
}
