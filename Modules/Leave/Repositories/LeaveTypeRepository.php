<?php

namespace Modules\Leave\Repositories;

use Illuminate\Support\Facades\Auth;
use Modules\Leave\Entities\LeaveType;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Leave\Exports\LeaveTypeExport;

class LeaveTypeRepository
{
    public function all()
    {
        return LeaveType::latest()->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/leave_type.xlsx"))) {
          unlink(public_path("uploads/csv/leave_type.xlsx"));
        }
        return Excel::store(new LeaveTypeExport($data), 'uploads/csv/leave_type.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$name,$sort,$column)
    {
        $items = LeaveType::query();

        if ($quick_search != null) {
            $items = $items->whereLike(['name'], $quick_search);;
        }
        $items = $items;
        if ($row_count == "all") {
            $total_number = LeaveType::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number);
            }else {
                return $items->latest()->paginate($total_number);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count);
            }else {
                return $items->paginate($row_count);
            }
        }
    }

    public function create(array $data)
    {
        $variant = new LeaveType();
        $data['created_by'] = Auth::id();
        $variant->fill($data)->save();
    }

    public function find($id)
    {
        return LeaveType::findOrFail($id);
    }

    public function update(array $data, $id)
    {

        $variant = LeaveType::findOrFail($id);
        $data['updated_by'] = Auth::id();
        $variant->update($data);
    }

    public function delete($id)
    {
        return LeaveType::destroy($id);
    }

    public function activeTypes()
    {
        return LeaveType::Active()->get();
    }
}
