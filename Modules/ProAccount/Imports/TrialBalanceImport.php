<?php


namespace Modules\ProAccount\Imports;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\ProAccount\Repositories\JournalRepository;
use Modules\ProAccount\Entities\Leadger;
use Carbon\Carbon;


class TrialBalanceImport implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection  $rows)
    {
        $total_sum = 0;
        $credit_amounts = [];
        $credit_account_id = [];
        $credit_partner_id = [];
        $credit_cash_flow_account_id = [];
        $credit_narration = [];

        $debit_amounts = [];
        $debit_account_id = [];
        $debit_partner_id = [];
        $debit_cash_flow_account_id = [];
        $debit_narration = [];
        foreach ($rows as $row)
        {
            $leadger = Leadger::where('code', $row[0])->first();
            if($leadger){
                if($row[2] > 0){
                    $debit_amounts[] = $row[2];
                    $debit_account_id[] = $leadger->id;
                    $debit_partner_id[] = 0;
                    $debit_cash_flow_account_id[] = 0;
                    $debit_narration[] = null;
                }
                if($row[3] > 0){
                    $credit_amounts[] = $row[3];
                    $credit_account_id[] = $leadger->id;
                    $credit_partner_id[] = 0;
                    $credit_cash_flow_account_id[] = 0;
                    $credit_narration[] = null;
                    $total_sum += $row[3];
                }
            }
        }
        if ($total_sum >= 0) {
            $journalRepo = new JournalRepository();
            $journalRepo->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount'=> $total_sum,
                'date'=> Carbon::now()->format('Y-m-d'),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> "Opening Balance For Trial Balance for First Starting",
                'referable_type'=> null,
                'referable_id'=> null,
                'is_invoiced'=> 0,
                'is_advanced'=> 0,
                'is_manual_entry'=> 1,
                'is_opening'=> 1,
    
                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => 1,
            ]);
        }
        
    }
}
