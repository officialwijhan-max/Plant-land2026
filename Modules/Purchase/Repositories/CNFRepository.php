<?php

namespace Modules\Purchase\Repositories;

use Modules\Purchase\Entities\CNF;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Purchase\Exports\CNFExport;
use Modules\Purchase\Entities\ProductItemDetail;
use Modules\Purchase\Entities\PurchaseOrder;

class CNFRepository implements CNFRepositoryInterface
{
    public function all()
    {
        return CNF::latest()->get();
    }

    public function csvDownload()
    {
        if (file_exists(public_path("uploads/csv/cnf-list.xlsx"))) {
            unlink(public_path("uploads/csv/cnf-list.xlsx"));
        }
        return Excel::store(new CNFExport(), 'uploads/csv/cnf-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count, $quick_search, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = CNF::query();
        
        if ($quick_search != null) {
            $items = $items->whereLike(['name', 'email', 'phone', 'address'], $quick_search);
        }
        
        if ($row_count == "all") {
            $total_number = CNF::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number,  $selected_data);
            } else {
                return $items->latest()->paginate($total_number,  $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count,  $selected_data);
            } else {
                return $items->latest()->paginate($row_count,  $selected_data);
            }
        }
    }

    public function create(array $data)
    {

        $cnf = new CNF();
        $cnf->fill($data)->save();
    }

    public function find($id)
    {
        return CNF::findOrFail($id);
    }

    public function update(array $data, $id)
    {

        $cnf = CNF::findOrFail($id);
        $cnf->update($data);
    }

    public function delete($id)
    {
        return CNF::destroy($id);
    }
}
