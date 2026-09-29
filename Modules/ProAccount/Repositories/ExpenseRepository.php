<?php

namespace Modules\ProAccount\Repositories;

use Modules\ProAccount\Entities\Voucher;
use Maatwebsite\Excel\Facades\Excel;
use update\Modules\ProAccount\Exports\VoucherExport;
use Modules\ProAccount\Imports\ExpenseImport;

class ExpenseRepository
{
    public function csvDownloadExpense()
    {
        if (file_exists(public_path("uploads/csv/expense.xlsx"))) {
          unlink(public_path("uploads/csv/expense.xlsx"));
        }
        return Excel::store(new VoucherExport("expense"), 'uploads/csv/expense.xlsx', 'public_folder');
    }

    public function csvUpload($data)
    {
        Excel::import(new ExpenseImport, $data['file']->store('temp'));
    }

    public function withPaginateExpense($row_count,$quick_search,$sort,$column,$relational_data = [], $selected_data = ['*'])
    {
        $items = Voucher::query();
        $items = $items->with($relational_data)->where('showroom_id', session()->get('showroom_id'))->where('sale_or_purchase', 'exp');
        if ($quick_search != null) {
            $items = $items->whereLike(['txn_id','narration','date','amount'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = Voucher::where('sale_or_purchase', 'exp')->count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }
    public function allQuery($filter_date)
    {
        return Voucher::when($filter_date, function($query) use($filter_date){
                $query->whereBetween('date',filterDateFormatingForSearchQuery($filter_date));
            })->where('sale_or_purchase', 'exp')->latest();
    }

    public function find($id)
    {
        return Voucher::find($id);
    }
}
