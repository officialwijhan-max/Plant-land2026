<?php

namespace Modules\RolePermission\Repositories;

use Modules\RolePermission\Entities\Role;
use Modules\RolePermission\Entities\Permission;
use Auth;
use Modules\RolePermission\Repositories\RoleRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use Modules\RolePermission\Exports\RoleExport;

class RoleRepository implements RoleRepositoryInterface
{
    public function all()
    {
        return Role::orderBy('id', 'desc')->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/role-list.xlsx"))) {
            unlink(public_path("uploads/csv/role-list.xlsx"));
        }
        return Excel::store(new RoleExport($data), 'uploads/csv/role-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count, $quick_search, $name, $sort, $column, $relational_data=[], $selected_data=['*'])
    {
        $items = Role::query();
        $items = $items->with($relational_data)->where('type', 'regular_user');

        if ($quick_search != null) {
            $items = $items->whereLike(['name'], $quick_search)->where('type', 'regular_user');
        }
        if ($row_count == "all") {
            $total_number = Role::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->paginate($row_count, $selected_data);
            }
        }
    }

    public function create(array $data)
    {
        $role = new Role();
        $role->name = $data['name'];
        $role->type = $data['type'];
        $role->save();
    }

    public function update(array $data, $id)
    {
        return Role::findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        $role = Role::with('users')->findOrFail($id);
        if ($role->users->count()){
            return false;
        }
        return $role->delete();
    }

    public function normalRoles()
    {
        return Role::where('type','!=','system_user')->get();
    }

    public function regularRoles()
    {
        return Role::where('type', 'regular_user')->get();
    }

    public function findRole($id){
        return Role::findOrFail($id);
    }
}
