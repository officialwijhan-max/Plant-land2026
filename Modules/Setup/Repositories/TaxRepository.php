<?php

namespace Modules\Setup\Repositories;

use Modules\Setup\Entities\Tax;
use Modules\Account\Entities\ChartAccount;
use Modules\ProAccount\Entities\Leadger;
use Auth;
use Modules\Setup\Repositories\TaxRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Setup\Exports\TaxExport;

class TaxRepository implements TaxRepositoryInterface
{
    public function all()
    {
        return Tax::latest()->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/tax.xlsx"))) {
          unlink(public_path("uploads/csv/tax.xlsx"));
        }
        return Excel::store(new TaxExport($data), 'uploads/csv/tax.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$sort,$column, $relational_data = [], $selected_data = ['*'])
    {
        $items = Tax::query();
        if ($quick_search != null) {
            $items = $items->whereLike(['name'],$quick_search);
        }
        if ($row_count == "all") {
            $total_number = Tax::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number,$selected_data);
            }else {
                return $items->latest()->paginate($total_number,$selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count,$selected_data);
            }else {
                return $items->latest()->paginate($row_count,$selected_data);
            }
        }
    }

    public function activeTax()
    {
        return Tax::where('status',1)->latest()->get();
    }

    public function serachBased($search_keyword)
    {
        return Tax::whereLike(['name', 'description', 'rate'], $search_keyword)->get();
    }

    public function create(array $data)
    {
        $tax = Tax::create($data);
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $chart_account = new Leadger;
            $chart_account->code = $tax->name.' - '. sprintf("%03d", $tax->id);
            $chart_account->level = 4;
            $chart_account->is_cost_center = 0;
            $chart_account->name = $tax->name;
            $chart_account->description = null;
            $chart_account->is_active = 1;
            $chart_account->parent_id = 17;
            $chart_account->type = 2;
            $chart_account->acc_type = "others";
            $chart_account->morphable_type = get_class(new Tax);
            $chart_account->morphable_id = $tax->id;
            $chart_account->save();
        } else {
            $chart_account = new ChartAccount;
            $chart_account->level = 2;
            $chart_account->is_group = 0;
            $chart_account->name = $tax->name;
            $chart_account->description = null;
            $chart_account->configuration_group_id = null;
            $chart_account->status = 1;
            $chart_account->parent_id = 5;
            $chart_account->type = 2;
            $chart_account->contactable_type = get_class(new Tax);
            $chart_account->contactable_id = $tax->id;
            $chart_account->save();
            ChartAccount::findOrFail($chart_account->id)->update(['code' => '0'.$chart_account->type.'-'.$chart_account->id]);
        }
    }

    public function update_status(array $data)
    {
        return Tax::findOrFail($data['id'])->update($data);
    }

    public function find($id)
    {
        return Tax::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        return Tax::findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        return Tax::findOrFail($id)->delete();
    }
}
