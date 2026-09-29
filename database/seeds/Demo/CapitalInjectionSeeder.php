<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Entities\ChartAccount;
use Modules\Account\Entities\Voucher;
use Modules\Account\Repositories\JournalRepository;
use Modules\Inventory\Entities\ShowRoom;

/**
 * Posts an owner-capital journal entry (Dr Cash-in-Hand + Dr Bank, Cr the
 * "Capital (Opening Balance Purpose)" account, code 02-09-11 - the same
 * account the app's own showroom_openning_balance_store() posts to) before
 * any purchases/sales run.
 *
 * Without this, the Cash/Bank dashboard figures are pure transaction flow
 * with no starting balance: a business that stocks up on inventory before
 * it has sold much will show a deeply negative cash position, which is
 * mathematically correct given zero starting capital but doesn't look like
 * a real, funded business. Real businesses start with investment/loan
 * capital, not $0.
 */
class CapitalInjectionSeeder extends Seeder
{
    public function run()
    {
        if (Voucher::where('narration', 'Owner capital injection to fund initial operations')->exists()) {
            $this->command->info('Capital injection already seeded, skipping.');
            return;
        }

        $adminId = DB::table('users')->where('role_id', 1)->value('id');
        if ($adminId) {
            Auth::loginUsingId($adminId);
        }

        $cashAccountId = ChartAccount::where('contactable_id', 1)
            ->where('contactable_type', ShowRoom::class)
            ->value('id');
        $bankAccountId = BankAccount::value('chart_account_id');
        $capitalAccount = ChartAccount::where('code', '02-09-11')->first();

        if (! $cashAccountId || ! $bankAccountId || ! $capitalAccount) {
            $this->command->warn('Missing cash/bank/capital chart account - run BankAccountSeeder first.');
            return;
        }

        $cashAmount = 200000;
        $bankAmount = 450000;

        (new JournalRepository())->create([
            'voucher_type' => 'JV',
            'amount' => $cashAmount + $bankAmount,
            'date' => now()->subDays(65)->format('Y-m-d'),
            'account_type' => 'credit',
            'payment_type' => 'journal_voucher',
            'account_id' => $capitalAccount->id,
            'main_amount' => $cashAmount + $bankAmount,
            'narration' => 'Owner capital injection to fund initial operations',
            'sub_account_id' => [$cashAccountId, $bankAccountId],
            'sub_amount' => [$cashAmount, $bankAmount],
            'sub_narration' => ['Owner capital - cash', 'Owner capital - bank'],
            'is_approve' => 1,
        ]);

        $this->command->info('Seeded owner capital injection: ' . number_format($cashAmount) . ' cash + ' . number_format($bankAmount) . ' bank.');
    }
}
