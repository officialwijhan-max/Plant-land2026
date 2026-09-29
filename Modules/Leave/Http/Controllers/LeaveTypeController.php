<?php

namespace Modules\Leave\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Leave\Repositories\LeaveTypeRepository;
use Modules\UserActivityLog\Traits\LogActivity;

class LeaveTypeController extends Controller
{
    private $leaveTypeRepository;

    public function __construct(LeaveTypeRepository $leaveTypeRepository)
    {
        $this->leaveTypeRepository = $leaveTypeRepository;
    }

    public function index(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'desc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            $data['items'] = $this->leaveTypeRepository->withPaginate($row_count,$quick_search,$name,$sort,$column);
            if ($request->ajax()) {
                return view('leave::leave_types.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->leaveTypeRepository->withPaginate("all",$quick_search,$name,$sort,$column);
                if ($request->import_as == "print") {
                    return view('leave::leave_types.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->leaveTypeRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/leave_type.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-leave_type.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('leave::leave_types.index', $data);

        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation failed');
            return back();
        }
    }

    public function store(Request $request)
    {
        $validate_rules = [
            'name' => ['required', 'string', 'unique:leave_types', 'max:255'],
            'status' => 'required'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $this->leaveTypeRepository->create($request->all());

            LogActivity::successLog(trans('leave.Leave Type Added Successfully'));

            return response()->json([
                'success' => trans('leave.Leave Type Added Successfully'),
                'TableData' => $this->loadTableData(),
            ]);

        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return  response()->json([
                'error' => trans('common.Something Went Wrong'),
            ]);
        }
    }

    public function update(Request $request)
    {
        $validate_rules = [
            'name' => ['required', 'string', 'max:255', 'unique:leave_types,name,' . $request->id],
            'status' => 'required'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        try {
            $this->leaveTypeRepository->update($request->all(), $request->id);

            LogActivity::successLog(trans('leave.Leave Type updated Successfully'));

            return response()->json([
                'success' => trans('leave.Leave Type updated Successfully'),
                'TableData' => $this->loadTableData(),
            ]);

        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return  response()->json([
                'error' => trans('common.Something Went Wrong'),
            ]);
        }
    }

    public function delete(Request $request)
    {
        $validate_rules = [
            'id' => 'required',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $this->leaveTypeRepository->delete($request['id']);

            return response()->json([
                'success' => trans('leave.Leave Type Deleted Successfully'),
                'TableData' => $this->loadTableData(),
            ]);

        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return  response()->json([
                'error' => trans('common.Something Went Wrong'),
            ]);
        }
    }

    private function loadTableData()
    {
        try {
            $LeaveTypeList = $this->leaveTypeRepository->all();
            return  (string)view('leave::leave_types.components.list', compact('LeaveTypeList'));

        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return  response()->json([
                'error' => trans('common.Something Went Wrong'),
            ]);
        }
    }
}
