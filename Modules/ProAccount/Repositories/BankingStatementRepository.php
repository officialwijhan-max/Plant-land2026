<?php

namespace Modules\ProAccount\Repositories;

use Modules\ProAccount\Entities\BankingStatement;
use Modules\ProAccount\Entities\BankingStatementDetail;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ProAccount\Export\BankingStatementExport;
use Modules\ProAccount\Imports\BankingStatementImport;

class BankingStatementRepository
{
    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/banking_statement.xlsx"))) {
          unlink(public_path("uploads/csv/banking_statement.xlsx"));
        }
        return Excel::store(new BankingStatementExport($data), 'uploads/csv/banking_statement.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column)
    {
        $items = BankingStatement::query();
        $items = $items;
        if ($quick_search != null) {
            $items = $items->whereLike(['date'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = BankingStatement::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number);
            }else {
                return $items->latest()->paginate($total_number);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count);
            }else {
                return $items->latest()->paginate($row_count);
            }
        }
    }

    public function create($data)
    {
        Excel::import(new BankingStatementImport($data), $data['file']->store('temp'));
    }

    public function find($id)
    {
        return BankingStatement::with(['banking_statement_details'])->findOrFail($id);
    }

    public function findStatementDetail($id)
    {
        return BankingStatementDetail::with(['banking_statement', 'banking_statement.leadger'])->findOrFail($id);
    }

    public function findToReconcile($data)
    {
        $statement_detail = BankingStatementDetail::findOrFail($data['id']);
        \LogActivity::successLog($statement_detail->narration.' has been matched ('.$statement_detail->date.')',route('banking_statement.index'), $statement_detail->date);
        $items = $statement_detail->MatcheAmount->where('id', $data['transaction_id']);
        foreach ($items as $key => $item) {
            $item->update(['is_reconciled' => 1, 'reconciled_date' => now(), 'banking_statement_detail_id' => $data['id']]);
            $item->GetOppositeSideAccount()->where('leadger_id', $statement_detail->banking_statement->leadger_id)->first()->update(['is_reconciled' => 1, 'reconciled_date' => now(), 'banking_statement_detail_id' => $data['id']]);
        }
        $statement_detail->update(['checked_by' => auth()->user()->id, 'is_matched' => 1]);

        return $statement_detail;
    }

    public function undoReconcile($data)
    {
        $statement_detail = BankingStatementDetail::findOrFail($data['id']);
        \LogActivity::successLog($statement_detail->narration.' has been matched ('.$statement_detail->date.') and undo now',route('banking_statement.reconciled',$statement_detail->banking_statement->id), $statement_detail->date);
        $items = $statement_detail->reconciled_amounts;
        foreach ($items as $key => $item) {
            $item->update(['is_reconciled' => 0, 'reconciled_date' => now(), 'banking_statement_detail_id' => 0]);
        }
        $statement_detail->update(['checked_by' => null, 'is_matched' => 0]);
        return $statement_detail;
    }

    public function approveReconcile($data)
    {
        $statement_detail = BankingStatementDetail::findOrFail($data['id']);
        \LogActivity::successLog($statement_detail->narration.' has been approved ('.$statement_detail->date.')',route('banking_statement.reconciled',$statement_detail->banking_statement->id), $statement_detail->date);
        $items = $statement_detail->reconciled_amounts;
        foreach ($items as $key => $item) {
            $item->update(['is_reconciled' => 0, 'reconciled_date' => now(), 'banking_statement_detail_id' => 0]);
        }
        $statement_detail->update(['is_approve' => 1]);
        return $statement_detail;
    }

    public function doneReconcile($data)
    {
        $response = BankingStatement::with(['banking_statement_details'])->findOrFail($data['id']);
        \LogActivity::successLog($response->leadger->name.' statement has been closed ',route('banking_statement.reconciled',$response->id), $response->leadger->name);
        $response->update(['re_conciled' => 1]);
    }

    public function destroy($id)
    {
        $statement = BankingStatement::with(['banking_statement_details'])->findOrFail($id);

        foreach($statement->banking_statement_details as $key => $statement_detail){
            $items = $statement_detail->reconciled_amounts;
            foreach ($items as $key => $item) {
                $item->update(['is_reconciled' => 0, 'reconciled_date' => now(), 'banking_statement_detail_id' => 0]);
            }
            $statement_detail->reconciled_amounts()->delete();
            $statement_detail->delete();
        }
        \LogActivity::successLog('Bank Transaction Statement Deleted',route('banking_statement.index'), $statement->leadger->name.'('.$statement->date.')');
        $statement->delete();
    }
}
