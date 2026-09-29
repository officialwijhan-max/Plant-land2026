<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\ShowRoom;
use Modules\Account\Entities\ChartAccount;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Inventory\Exports\ShowRoomExport;
use Modules\ProAccount\Entities\Leadger;

class ShowRoomRepository implements ShowRoomRepositoryInterface
{
    public function all()
    {
        return ShowRoom::with('stocks','contact')->latest()->get();
    }

    public function serachBased($search_keyword)
    {
        return ShowRoom::whereLike(['name', 'email', 'address', 'phone'], $search_keyword)->get();
    }

    public function activeShoowroom()
    {
        return ShowRoom::latest()->active()->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/showroom_list.xlsx"))) {
            unlink(public_path("uploads/csv/showroom_list.xlsx"));
        }
        return Excel::store(new ShowRoomExport($data), 'uploads/csv/showroom_list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count, $quick_search, $name, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = ShowRoom::query();
        if ($quick_search != null) {
            $items = $items->whereLike(['name','email','phone'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = ShowRoom::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number,$selected_data);
            } else {
                return $items->latest()->paginate($total_number,$selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count,$selected_data);
            } else {
                return $items->latest()->paginate($row_count,$selected_data);
            }
        }
    }

    public function showroomForSelect($company_id, $search)
    {
        if ($search != '') {
            $items = ShowRoom::whereLike(['name'], $search)->paginate(10);
        } else {
            $items = ShowRoom::paginate(10);
        }

        $response = [];
        foreach ($items as $item) {
            $response[]  = [
                'id'    => $item->id,
                'text'  => $item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function create(array $data)
    {
        $warehouse = new ShowRoom();
        $warehouse->fill($data)->save();
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $chart_account = new Leadger;
            $chart_account->code = $warehouse->name . ' - ' . sprintf("%03d", $warehouse->id);
            $chart_account->level = 3;
            $chart_account->is_cost_center = 0;
            $chart_account->name = 'Cash in ' . $warehouse->name;
            $chart_account->description = null;
            $chart_account->is_active = 1;
            $chart_account->parent_id = 13;
            $chart_account->type = 1;
            $chart_account->acc_type = "cash";
            $chart_account->morphable_type = get_class(new ShowRoom);
            $chart_account->morphable_id = $warehouse->id;
            $chart_account->save();
        } else {
            $chart_account = new ChartAccount;
            $chart_account->level = 2;
            $chart_account->is_group = 0;
            $chart_account->name = $warehouse->name . '(Cash)';
            $chart_account->description = null;
            $chart_account->configuration_group_id = 1;
            $chart_account->status = 1;
            $chart_account->parent_id = 1;
            $chart_account->type = 1;
            $chart_account->contactable_type = get_class(new ShowRoom);
            $chart_account->contactable_id = $warehouse->id;
            $chart_account->save();
            ChartAccount::findOrFail($chart_account->id)->update(['code' => '0' . $chart_account->type . '-' . sprintf("%02d",$chart_account->parent_id) . '-' . $chart_account->id]);
        }
        return $warehouse;
    }

    public function find($id)
    {
        return ShowRoom::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $warehouse = ShowRoom::findOrFail($id);

        $warehouse->update($data);
    }

    public function delete($id)
    {
        return ShowRoom::findOrFail($id)->delete();
    }
}
