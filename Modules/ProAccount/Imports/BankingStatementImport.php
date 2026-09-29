<?php


namespace Modules\ProAccount\Imports;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\ProAccount\Entities\BankingStatement;
use Modules\ProAccount\Entities\BankingStatementDetail;
use Carbon\Carbon;


class BankingStatementImport implements ToCollection, WithStartRow
{
    protected $data;

    function __construct($data) {
        $this->given_data = $data;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection  $rows)
    {
        $total_sum = 0;
        $fill_data = $this->given_data;
        $balance = 0;
        $statement = BankingStatement::create([
            'date' => now(),
            'leadger_id' => $fill_data['credit_account_id'],
            'created_by' => auth()->user()->id,
        ]);
        foreach ($rows as $row)
        {
            $balance += floatval($row[2]);
            BankingStatementDetail::create([
                'banking_statement_id' => $statement->id,
                'date' => Carbon::parse($row[0])->format('Y-m-d'),
                'narration' => $row[1],
                'amount' => abs($row[2]),
                'sign' => (floatval($row[2]) > 0) ? 1 : 0,
            ]);
        }
        $statement->update(['balance' => $balance]);
        \LogActivity::successLog('Bank Register Uploaded',route('banking_statement.show',$statement->id), 'Banking Transaction');
    }
}
