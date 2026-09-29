<?php

namespace Modules\RolePermission\Repositories;

use Modules\RolePermission\Entities\UserColumnPermission;
use App\User;
use Auth;

class UserColumnPermissionRepository
{
    public function find($id, $table_name)
    {
        return UserColumnPermission::where('user_id', $id)->where('table_name', $table_name)->first();
    }

    public function findForMe($table_name)
    {
        return UserColumnPermission::where('user_id', auth()->user()->id)->where('table_name', $table_name)->first();
    }
    public function storeSelf(array $data)
    {
        $permission_show_ids = [];
        $permission_hide_ids = [];
        $export_cols_names = [];
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                if (explode('-',$value)[0] == "show") {
                    array_push($permission_show_ids, $key);
                    array_push($permission_show_ids, ',');
                    array_push($export_cols_names, explode('-',$value)[1]);
                    array_push($export_cols_names, ',');
                }else {
                    array_push($permission_hide_ids, $key);
                    array_push($permission_hide_ids, ',');
                }
            }
        }
        array_pop($permission_show_ids);
        array_pop($export_cols_names);
        array_pop($permission_hide_ids);
        UserColumnPermission::create([
            'role_id' => User::where('id', $data['employee_id'])->first()->role_id,
            'user_id' => $data['employee_id'],
            'table_name' => $data['table_name'],
            'hide_column_no_by_self' => '['.implode($permission_hide_ids).']',
            'export_column' => '['.implode($export_cols_names).']',
            'show_column_no_by_self' => '['.implode($permission_show_ids).']'
        ]);
    }

    public function updateSelf(array $data, $id)
    {
        $permission = UserColumnPermission::findOrFail($id);
        $permission_show_ids = [];
        $export_cols_names = [];
        $permission_hide_ids = [];
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                if (explode('-',$value)[0] == "show") {
                    array_push($permission_show_ids, $key);
                    array_push($permission_show_ids, ',');
                    array_push($export_cols_names, explode('-',$value)[1]);
                    array_push($export_cols_names, ',');
                }else {
                    array_push($permission_hide_ids, $key);
                    array_push($permission_hide_ids, ',');
                }
            }
        }
        if ($permission->hide_column_no_by_admin) {
            $str_real = str_replace(["[", "]"], '', $permission->hide_column_no_by_admin);
            foreach (explode(',',$str_real) as $k => $val) {
                if ($val != "") {
                    array_push($permission_hide_ids, $val);
                    array_push($permission_hide_ids, ',');
                }
            }
        }
        array_pop($permission_show_ids);
        array_pop($export_cols_names);
        array_pop($permission_hide_ids);
        $permission->update([
            'show_column_no_by_self' => '['.implode($permission_show_ids).']',
            'export_column' => '['.implode($export_cols_names).']',
            'hide_column_no_by_self' => '['.implode($permission_hide_ids).']'
        ]);
    }
}
