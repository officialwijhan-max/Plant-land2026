<?php

use Illuminate\Database\Seeder;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Entities\ChartAccount;

/**
 * A single default bank account, created the same way BankAccountRepository
 * ::create() does it (a ChartAccount of type=1/parent_id=3/is_actual_bank=1
 * plus a linked bank_accounts row) - NOT via ChartAccountSeeder, which only
 * handles customer/supplier ledger accounts.
 *
 * Purchase/SaleSeeder need this because a "bank" Payment must carry an
 * account_id pointing at a real bank ChartAccount - SaleRepository::
 * statusChange() / PurchaseOrderRepository::approve() do
 * `ChartAccount::find($payment->account_id)->id` with no null check, so a
 * missing/wrong account_id is a fatal error, not a soft failure.
 */
class BankAccountSeeder extends Seeder
{
    public function run()
    {
        if (BankAccount::count() > 0) {
            $this->command->info('bank_accounts already has data, skipping.');
            return;
        }

        $chartAccount = new ChartAccount();
        $chartAccount->level = 2;
        $chartAccount->is_group = 0;
        $chartAccount->name = 'Plant Land Main Bank Account';
        $chartAccount->type = 1; // asset
        $chartAccount->parent_id = 3;
        $chartAccount->status = 1;
        $chartAccount->configuration_group_id = 2;
        $chartAccount->is_actual_bank = 1;
        $chartAccount->save();

        $bankAccount = new BankAccount();
        $bankAccount->chart_account_id = $chartAccount->id;
        $bankAccount->bank_name = 'CIB Egypt';
        $bankAccount->branch_name = 'Cairo Main Branch';
        $bankAccount->account_name = 'Plant Land Landscaping';
        $bankAccount->account_no = '100' . str_pad((string) $chartAccount->id, 7, '0', STR_PAD_LEFT);
        $bankAccount->description = 'Default operating account seeded for demo data.';
        $bankAccount->save();

        $chartAccount->update(['code' => '03-' . $chartAccount->id]);

        $this->command->info("Seeded default bank account (chart_account_id={$chartAccount->id}).");
    }
}
