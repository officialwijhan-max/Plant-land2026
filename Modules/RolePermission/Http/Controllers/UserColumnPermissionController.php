<?php

namespace Modules\RolePermission\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\RolePermission\Repositories\UserColumnPermissionRepository;

class UserColumnPermissionController extends Controller
{
    protected $userColPermission;

    public function __construct(UserColumnPermissionRepository $userColPermission)
    {
        $this->middleware(['auth']);
        $this->userColPermission = $userColPermission;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */

    public function show_with_self($table_name)
    {
        $data['permission'] = $this->userColPermission->findForMe($table_name);
        $data['employee_id'] = auth()->user()->id;
        $data['employee_role_id'] = auth()->user()->role_id;
        $data['table_name'] = $table_name;
        if ($data['permission'] != null) {
            return view('rolepermission::user_col_permission.self_edit', $data);
        }else {
            return view('rolepermission::user_col_permission.self_create', $data);
        }
    }

    public function store_self(Request $request)
    {
        try {
            $this->userColPermission->storeSelf($request->except('_token'));
            Toastr::success(trans('common.Successfully Updated'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function update_self(Request $request, $id)
    {
        try {
            $this->userColPermission->updateSelf($request->except('_token'), $id);
            Toastr::success(trans('common.Successfully Updated'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }
}
