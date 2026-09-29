<?php

namespace Modules\Leave\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Leave\Repositories\LeaveDefineRepository;
use Modules\Leave\Repositories\LeaveDefineRepositoryInterface;
use Modules\Leave\Repositories\LeaveTypeRepository;
use Modules\Leave\Repositories\LeaveTypeRepositoryInterface;
use Modules\RolePermission\Repositories\RoleRepository;
use Modules\RolePermission\Repositories\RoleRepositoryInterface;
use Modules\UserActivityLog\Traits\LogActivity;

class LeaveDefineController extends Controller
{
    private $leaveDefineRepository,$roleRepo,$leaveTypeRpo;

    public function __construct(LeaveDefineRepository $leaveDefineRepository,RoleRepository $roleRepo,LeaveTypeRepository $leaveTypeRpo)
    {
        $this->leaveDefineRepository = $leaveDefineRepository;
        $this->roleRepo = $roleRepo;
        $this->leaveTypeRpo = $leaveTypeRpo;
    }

    public function index(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'desc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            $data['items'] = $this->leaveDefineRepository->withPaginate($row_count,$quick_search,$name,$sort,$column);
            if ($request->ajax()) {
                return view('leave::leave_defines.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->leaveDefineRepository->withPaginate("all",$quick_search,$name,$sort,$column);
                if ($request->import_as == "print") {
                    return view('leave::leave_defines.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->leaveDefineRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/leave_define.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-leave_define.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            $data['RoleList'] = $this->roleRepo->regularRoles();
            $data['LeaveTypeList'] = $this->leaveTypeRpo->all();

            return view('leave::leave_defines.index', $data);

        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation failed');
            return back();
        }
    }

    public function store(Request $request)
    {
        $validate_rules = [
            'role_id' => 'required',
            'leave_type_id' => 'required',
            'total_days' => 'required',
            'max_forward' => 'required_if:balance_forward,==,1',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            DB::beginTransaction();
            $defined = $this->leaveDefineRepository->roleWiseLeave($request->leave_type_id,$request->role_id);
            $request['adjust_days'] = $defined ? $defined->total_days : 0;

            if ($defined && empty($request->users))
            {
                return response()->json(trans('leave.Leave Type For this role already defined'));
            }

            $this->leaveDefineRepository->create($request->all());
            DB::commit();
            LogActivity::successLog("Leave Define added Successfully");
            return response()->json([
                'success' => trans('leave.Leave Defined Successfully'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage());
            return response()->json(trans('common.Something Went Wrong'));
        }
    }

    public function update(Request $request)
    {
        $validate_rules = [
            'role_id' => 'required',
            'leave_type_id' => 'required',
            'total_days' => 'required',
            'max_forward' => 'required_if:balance_forward,==,1',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules) );
        DB::beginTransaction();
        try {

            $this->leaveDefineRepository->update($request->all(), $request->id);
            DB::commit();
            LogActivity::successLog("Leave Define updated Successfully");
            return response()->json([
                'success' => trans('leave.Leave Define Updated Successfully'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage());
            return response()->json(trans('common.Something Went Wrong'));
        }
    }

    public function delete(Request $request)
    {
        $validate_rules = [
            'id' => 'required',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $this->leaveDefineRepository->delete($request->id);
            return response()->json([
                'success' => trans('leave.Leave Define Deleted Successfully'),
            ]);

        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return response()->json(trans('common.Something Went Wrong'));
        }
    }
}
