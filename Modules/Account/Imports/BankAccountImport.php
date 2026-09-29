<?php

namespace Modules\Account\Imports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Entities\ChartAccount;
use Modules\Account\Entities\TimePeriodAccount;
use Modules\Account\Repositories\OpeningBalanceHistoryRepository;

class BankAccountImport implements ToModel, WithStartRow, WithCustomCsvSettings
{
    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $bankAccount = BankAccount::create([
            'bank_name' => $row[0],
            'branch_name' => $row[1],
            'account_name' => $row[2],
            'account_no' => $row[3],
            'openning_balance' => $row[4],
            'description' => $row[5],
        ]);
        $chart_account = ChartAccount::create([
            'level' => 2,
            'configuration_group_id' => 2,
            'is_actual_bank' => 1,
            'is_group' => 0,
            'name' => $bankAccount->bank_name,
            'type' => 1,
            'parent_id' => 3,
            'status' => 1,
            'contactable_id' => $bankAccount->id,
            'contactable_type' => 'Modules\Account\Entities\BankAccount',
        ]);
        $chart_account->update([
            'code' => '03-' . $chart_account->id,
        ]);

        $bankAccount->chart_account_id = $chart_account->id;
        $bankAccount->save();

        return $this->create_chart_account($bankAccount, $chart_account);
        // return true;
    }
    public function create_chart_account($bankAccount, $chart_account)
    {
        if ($bankAccount->openning_balance != null && $bankAccount->openning_balance > 0) {
            $repo = new OpeningBalanceHistoryRepository();
            $repo->createForUser([
                'asset_account_id' => $chart_account->id,
                'asset_amount' => $bankAccount->openning_balance,
                'date' => Carbon::now()->format('Y-m-d'),
                'time_period_id' => TimePeriodAccount::where('is_closed', 0)->latest()->first()->id,
                'liability_account_id' => ChartAccount::where('code', '02-09-11')->first()->id,
                'liability_amount' => $bankAccount->openning_balance,
            ]);
            $repo->createForHistory([
                'account_id' => $chart_account->id,
                'type' => 'bank',
                'amount' => $bankAccount->openning_balance,
            ]);
        }
    }
}
