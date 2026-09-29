<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Account\Entities\ChartAccount;
use Modules\Contact\Entities\ContactModel;

/**
 * A ledger sub-account per seeded contact. Without this, SaleRepository::
 * statusChange() / PurchaseOrderRepository::approve() (the real
 * accounting-posting methods - see PurchaseSeeder/SaleSeeder) can't find
 * an account to post the customer/supplier side of each transaction to,
 * and the dashboard's bank/cash/profit figures (which read from
 * ChartAccount balances, not from sales/purchases directly) stay at zero
 * or go nonsensical.
 *
 * parent_id 5 = "Account Receivable" (customers), 8 = "Account Payable"
 * (suppliers) - both seeded by chart_accounts' own migration.
 */
class ChartAccountSeeder extends Seeder
{
    public function run()
    {
        $adminId = DB::table('users')->where('role_id', 1)->value('id') ?? 1;

        $contacts = ContactModel::whereIn('contact_type', ['Customer', 'Supplier'])
            ->where('id', '!=', 1) // Walk In Customer already has one (code 02-05-29)
            ->get();

        $created = 0;
        $nextCustomerCode = 100;
        $nextSupplierCode = 200;

        foreach ($contacts as $contact) {
            $exists = ChartAccount::where('contactable_id', $contact->id)
                ->where('contactable_type', ContactModel::class)
                ->exists();

            if ($exists) {
                continue;
            }

            if ($contact->contact_type === 'Customer') {
                $code = '01-05-' . $nextCustomerCode++;
                $parentId = 5;
                $type = '1';
            } else {
                $code = '02-08-' . $nextSupplierCode++;
                $parentId = 8;
                $type = '2';
            }

            // contactable_id/contactable_type aren't in ChartAccount's
            // $fillable, so create() would silently drop them - set them
            // directly instead, which is the whole point of this row.
            $account = new ChartAccount([
                'code' => $code,
                'level' => 2,
                'is_group' => 0,
                'name' => $contact->name,
                'type' => $type,
                'description' => $contact->contact_type . ' ledger account',
                'parent_id' => $parentId,
                'status' => 1,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);
            $account->contactable_type = ContactModel::class;
            $account->contactable_id = $contact->id;
            $account->save();

            $created++;
        }

        $this->command->info("Seeded {$created} contact ledger accounts.");
    }
}
