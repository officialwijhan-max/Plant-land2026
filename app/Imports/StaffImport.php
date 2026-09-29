<?php

namespace App\Imports;

use App\User;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;
use Modules\ProAccount\Entities\SubLeadger;
use Modules\Account\Entities\ChartAccount;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StaffImport implements WithStartRow, WithCustomCsvSettings, ToCollection
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

    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row)
        {
            if ($row[0] != null) {
                if($this->checkEmail($row[1])){
                    $user = User::create([
                        $rows[0][0] => $row[0],
                        $rows[0][1] => $row[1],
                        $rows[0][2] => $row[2],
                        $rows[0][3] => bcrypt($row[3]),
                        'role_id' => 3,
                        'email_verified_at' => date('Y-m-d H:m:s')
                    ]);
                }else{
                    $user = User::create([
                        $rows[0][0] => $row[0],
                        $rows[0][2] => $row[2],
                        $rows[0][3] => bcrypt($row[3]),
                        'role_id' => 3,
                        'email_verified_at' => date('Y-m-d H:m:s')
                    ]);
                }
                $staff = Staff::create([
                    'user_id' => $user->id,
                    'department_id' => 1,
                    'showroom_id' => 1,
                    $rows[0][4] => $row[4],
                    $rows[0][5] => Carbon::parse($row[5])->format('Y-m-d'),
                    $rows[0][6] => $row[6],
                    $rows[0][7] => $row[7],
                    $rows[0][8] => $row[8],
                    $rows[0][9] => $row[9],
                    $rows[0][10] => $row[10],
                    $rows[0][11] => $row[11],
                    $rows[0][12] => $row[12],
                    $rows[0][13] => $row[13],
                    $rows[0][14] => $row[14],
                    $rows[0][15] => empty($row[15]) ? null : Carbon::parse($row[15])->format('Y-m-d'),
                ]);

                if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                    $sub_leadger = Subleadger::create([
                        'leadger_id' => Settings('leadger_account_for_employee') ? Settings('leadger_account_for_employee') : 0,
                        'code' => 'Emp-' . sprintf("%06d", $staff->id),
                        'name' => $user->name . ' ' . sprintf("%03d", $staff->id),
                        'morphable_type' => get_class($staff),
                        'morphable_id' => $staff->id,
                        'description' => 'Employee Account Created when added Employee'
                    ]);

                    if ($staff->opening_balance != null || $staff->opening_balance > 0) {
                        $debit_amounts[] = null;
                        $debit_account_id[] = null;
                        $debit_partner_id[] = null;
                        $debit_narration[] = null;
                        $debit_cash_flow_account_id[] = 0;

                        $credit_amounts[] = $staff->opening_balance;
                        $credit_account_id[] = Settings('leadger_account_for_employee');
                        $credit_partner_id[] = $sub_leadger->id;
                        $credit_narration[] = $user->name.' - Opening Balance Amount Staff';
                        $credit_cash_flow_account_id[] = 0;

    
                        $journalRecieveRepository = new ProJournalRepository();
                        $voucher = $journalRecieveRepository->create([
                            'type' => "misc",
                            'is_cash_flow_journal' => 0,
                            'amount'=> $staff->opening_balance,
                            'date'=> Carbon::now()->format('Y-m-d'),
                            'credit_account_id'=> $credit_account_id,
                            'credit_sub_account_id'=> $credit_partner_id,
                            'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                            'credit_account_amount'=> $credit_amounts,
                            'credit_narration'=> $credit_narration,
                            'narration_voucher'=> $user->name.' - Opening Balance Amount Staff',
                            'referable_type'=> null,
                            'referable_id'=> null,
                            'is_invoiced'=> 0,
                            'is_manual_entry'=> 0,
                
                            'debit_account_id'=> $debit_account_id,
                            'debit_sub_account_id'=> $debit_partner_id,
                            'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                            'debit_account_amount'=> $debit_amounts,
                            'debit_narration'=> $debit_narration,
                            'is_approve' => 1,
                            'sale_or_purchase' => null,
                            'ref_no' => null,
                        ]);
                    }
                } else{
                    $chart_account = new ChartAccount;
                    $chart_account->level = 2;
                    $chart_account->is_group = 0;
                    $chart_account->name = $staff->user->name;
                    $chart_account->description = null;
                    $chart_account->parent_id = 9;
                    $chart_account->status = 1;
                    $chart_account->configuration_group_id = 4;
                    $chart_account->type = 1;
                    $chart_account->contactable_type = "App\User";
                    $chart_account->contactable_id = $user->id;
                    $chart_account->save();
                    ChartAccount::findOrFail($chart_account->id)->update(['code' => '0' . $chart_account->type . '-' . $chart_account->parent_id . '-' . $chart_account->id]);
                    if ($staff->opening_balance != null || $staff->opening_balance > 0) {

                        $repo = new OpeningBalanceHistoryRepository;
                        $repo->createForUser([
                            'asset_account_id' => $chart_account->id,
                            'asset_amount' => $staff->opening_balance,
                            'date' => Carbon::now()->format('Y-m-d'),
                            'time_period_id' => TimePeriodAccount::latest()->first()->id,
                            'liability_account_id' => ChartAccount::where('code', '02-09')->first()->id,
                            'liability_amount' => $staff->opening_balance,
                        ]);
                        $repo->createForHistory([
                            'account_id' => $chart_account->id,
                            'type' => 'staff',
                            'amount' => $staff->opening_balance,
                        ]);
                    }
                }
            }
        }
    }

    function checkEmail($email) {
        $find1 = strpos($email, '@');
        $find2 = strpos($email, '.');
        return ($find1 !== false && $find2 !== false && $find2 > $find1);
    }
}
