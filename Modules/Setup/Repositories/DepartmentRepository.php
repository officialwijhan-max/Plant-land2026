<?php

namespace Modules\Setup\Repositories;

use Modules\Setup\Entities\Department;
use Modules\Setup\Repositories\DepartmentRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Setup\Exports\DepartmentExport;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function all()
    {
        return Department::latest()->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/department_list.xlsx"))) {
          unlink(public_path("uploads/csv/department_list.xlsx"));
        }
        return Excel::store(new DepartmentExport($data), 'uploads/csv/department_list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$name,$sort,$column)
    {
        $items = Department::query();
        if ($quick_search != null) {
            $items = $items->whereLike(['name'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = Department::count();

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
        return Department::create($data);
    }

    public function find($id)
    {
        return Department::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        return Department::findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        return Department::findOrFail($id)->delete();
    }
}
