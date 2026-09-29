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


class ExpenseImport implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection  $rows)
    {
        $total_sum = 0;

        foreach ($rows->skip(1) as $row)
        {
            $total_sum += $row[2];
            $credit_amounts[] = $row[2];
            $credit_account_id[] = Leadger::where('morphable_type', 'Modules\Inventory\Entities\ShowRoom')->where('morphable_id', session()->get('showroom_id'))->first()->id;
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
            $credit_narration[] = $row[1];
        }

        $debit_amounts[] = $total_sum;
        $debit_account_id[] = Settings('default_expense_account');
        $debit_partner_id[] = 0;
        $debit_cash_flow_account_id[] = 0;
        $debit_narration[] = "Expense of : ".Carbon::now()->format('Y-m-d');

        if ($total_sum >= 0) {
            $approval = 1;

            $journalRecieveRepository = new JournalRepository();
            $journalRecieveRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount'=> $total_sum,
                'date'=> Carbon::now(),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=>"Expense of : ".Carbon::now()->format('Y-m-d'),
                'referable_type'=> null,
                'referable_id'=> null,
                'is_invoiced'=> 0,
                'is_advanced'=> 0,
                'is_manual_entry'=> 1,

                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => $approval,
                'sale_or_purchase' => "exp",
                'ref_no' => null,
                'is_sale_purchase' => 0,
            ]);
        }
        
    }
}
