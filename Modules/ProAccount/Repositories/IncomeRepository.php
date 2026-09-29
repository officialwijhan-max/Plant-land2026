<?php

namespace Modules\ProAccount\Repositories;

use Modules\ProAccount\Entities\Voucher;
use Maatwebsite\Excel\Facades\Excel;
use update\Modules\ProAccount\Exports\VoucherExport;
use Modules\ProAccount\Imports\IncomeImport;

class IncomeRepository
{
    public function csvDownloadExpense()
    {
        if (file_exists(public_path("uploads/csv/income.xlsx"))) {
          unlink(public_path("uploads/csv/income.xlsx"));
        }
        return Excel::store(new VoucherExport("income"), 'uploads/csv/income.xlsx', 'public_folder');
    }

    public function csvUpload($data)
    {
        Excel::import(new IncomeImport, $data['file']->store('temp'));
    }

    public function withPaginateExpense($row_count,$quick_search,$sort,$column,$relational_data = [], $selected_data = ['*'])
    {
        $items = Voucher::query();
        $items = $items->with($relational_data)->where('showroom_id', session()->get('showroom_id'))->where('sale_or_purchase', 'inc');
        if ($quick_search != null) {
            $items = $items->whereLike(['txn_id','narration','date','amount'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = Voucher::where('sale_or_purchase', 'inc')->count();

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
            })->where('sale_or_purchase', 'inc')->latest();
    }

    public function find($id)
    {
        return Voucher::find($id);
    }
}
