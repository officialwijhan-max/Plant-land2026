<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Contact\Entities\ContactModel;

/**
 * Fictional clients (villas, a hospital, compounds, a hotel, a business
 * complex) and suppliers (nurseries, irrigation, hardscape materials) for
 * a landscaping business. Names are deliberately generic/invented, not
 * real Egyptian companies.
 */
class ContactSeeder extends Seeder
{
    public function run()
    {
        if (ContactModel::where('id', '!=', 1)->exists()) {
            $this->command->info('contacts already has data beyond the default Walk In Customer, skipping.');
            return;
        }

        $adminId = DB::table('users')->where('role_id', 1)->value('id');
        if ($adminId) {
            Auth::loginUsingId($adminId);
        }

        $customers = [
            ['فيلا خاصة - أ. محمد رشدي', '01012345601', 'New Cairo'],
            ['فيلا خاصة - د. سلمى عبد العزيز', '01012345602', 'Sheikh Zayed'],
            ['مستشفى الشفاء التخصصي', '0223456701', 'Nasr City'],
            ['كمبوند الياسمين - إدارة الحدائق', '01098765401', '6th of October'],
            ['فيلا خاصة - م. أحمد الجندي', '01012345603', 'Katameya'],
            ['كمبوند واحة النخيل - لجنة تنسيق الحدائق', '01098765402', 'New Cairo'],
            ['فندق النيل الكبير - قسم الحدائق', '0223456702', 'Giza'],
            ['فيلا خاصة - د. هبة يوسف', '01012345604', '6th of October'],
            ['مجمع إداري النور - المساحات الخضراء', '0223456703', 'Sheikh Zayed'],
            ['كمبوند الأمل السكني - الحديقة المركزية', '01098765403', 'New Cairo'],
        ];

        $suppliers = [
            ['مشتل الواحة الخضراء', '01055512301', 'Qalyubia'],
            ['مشتل النيل للنباتات والأشجار', '01055512302', 'Giza'],
            ['شركة الصفا للري الحديث', '0223478801', '10th of Ramadan City'],
            ['مؤسسة الدلتا للتوريدات الزراعية', '01055512303', 'Sharqia'],
            ['شركة النور لمواد البناء والهارد سكيب', '0223478802', 'Cairo'],
        ];

        $count = 0;
        foreach ($customers as [$name, $mobile, $address]) {
            ContactModel::create([
                'contact_type' => 'Customer',
                'name' => $name,
                'mobile' => $mobile,
                'address' => $address,
                'pay_term' => '30',
                'opening_balance' => 0,
            ]);
            $count++;
        }

        foreach ($suppliers as [$name, $mobile, $address]) {
            ContactModel::create([
                'contact_type' => 'Supplier',
                'name' => $name,
                'mobile' => $mobile,
                'address' => $address,
                'pay_term' => '30',
                'opening_balance' => 0,
            ]);
            $count++;
        }

        $this->command->info("Seeded {$count} contacts (10 customers, 5 suppliers).");
    }
}
