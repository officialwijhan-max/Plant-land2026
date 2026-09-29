<?php

namespace Modules\UserActivityLog\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\UserActivityLog\Traits\LogActivity;
use Modules\UserActivityLog\Entities\LogActivity as LogActivityModel;
use Maatwebsite\Excel\Facades\Excel;
use Modules\UserActivityLog\Exports\LogActivityModelExport;
use Modules\UserActivityLog\Exports\LoginActivityModelExport;
use Carbon\Carbon;
use App\User;

class UserActivityLogController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission']);
    }

    public function index(Request $request)
    {
        try{
            $data['from_date'] = ($request->from_date != null && $request->from_date != "undefined") ? Carbon::parse(str_replace('ff', '/', $request->from_date))->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $data['to_date'] = ($request->to_date != null && $request->to_date != "undefined") ? Carbon::parse(str_replace('ff', '/', $request->to_date))->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $data['user'] = ($request->user != null && $request->user != "undefined") ? $request->user : null;

            if ($request->to_date != null && $request->from_date == null) {
                Toastr::warning(__('report::report.you_need_to_set_date_from_when_you_select_date_to'));
                return view('base::activity.index', $data);
            }
            if ($request->to_date == null && $request->from_date != null) {
                Toastr::warning(__('report::report.you_need_to_set_date_to_when_you_select_date_from'));
                return view('base::activity.index', $data);
            }

            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;

            $items = LogActivityModel::query();

            if ($quick_search != null) {
                $items = $items->whereLike(['url','agent','display_name','ip'], $quick_search);
            }
            if ($data['to_date'] && $data['from_date']) {
                $items = $items->whereBetween('created_at', array($data['from_date']." 00:00:00",$data['to_date']." 23:59:59"));
            }
            if ($data['user']) {
                $data['user_info'] = User::find($data['user']);
                $items = $items->where('user_id', $data['user']);
            }
            if ($row_count == "all" || ($request->has("import_as") && $request->import_as == "print")) {
                $total_number = LogActivityModel::count();

                if ($column != null) {
                    $data['items'] = $items->orderBy($column, $sort)->paginate($total_number);
                }else {
                    $data['items'] = $items->latest()->paginate($total_number);
                }
            } else {
                if ($column != null) {
                    $data['items'] = $items->orderBy($column, $sort)->paginate($row_count);
                }else {
                    $data['items'] = $items->latest()->paginate($row_count);
                }
            }
            if ($request->ajax()) {
                return view('useractivitylog::paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                if ($request->import_as == "print") {
                    return view('useractivitylog::paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    if (file_exists(public_path("uploads/csv/activity_logs.xlsx"))) {
                        unlink(public_path("uploads/csv/activity_logs.xlsx"));
                      }
                    Excel::store(new LogActivityModelExport, 'uploads/csv/activity_logs.xlsx', 'public_folder');
                    $filePath = public_path("uploads/csv/activity_logs.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-activity_logs.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            $data['activities'] = LogActivity::logActivityLists();
            return view('useractivitylog::index', $data);
        }catch(\Exception $e){
            LogActivity::errorLog($e->getMessage());
            Toastr::error('Something happend Wrong!', 'Error!!');
            return redirect()->back();
        }
    }

    public function login_index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;

            $items = LogActivityModel::query();

            if ($quick_search != null) {
                $items = $items->whereLike(['agent','ip'], $quick_search);
            }
            if ($row_count == "all" || ($request->has("import_as") && $request->import_as == "print")) {
                $total_number = LogActivityModel::count();

                if ($column != null) {
                    $data['items'] = $items->orderBy($column, $sort)->paginate($total_number);
                }else {
                    $data['items'] = $items->latest()->paginate($total_number);
                }
            } else {
                if ($column != null) {
                    $data['items'] = $items->orderBy($column, $sort)->paginate($row_count);
                }else {
                    $data['items'] = $items->latest()->paginate($row_count);
                }
            }
            if ($request->ajax()) {
                return view('useractivitylog::paginates.login_list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                if ($request->import_as == "print") {
                    return view('useractivitylog::paginates.login_print', $data);
                }
                if ($request->import_as == "csv") {
                    if (file_exists(public_path("uploads/csv/login_activity_logs.xlsx"))) {
                        unlink(public_path("uploads/csv/login_activity_logs.xlsx"));
                      }
                    Excel::store(new LoginActivityModelExport, 'uploads/csv/login_activity_logs.xlsx', 'public_folder');
                    $filePath = public_path("uploads/csv/login_activity_logs.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-login_activity_logs.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('useractivitylog::login_index', $data);
        }catch(\Exception $e){
            LogActivity::errorLog($e->getMessage());
            Toastr::error('Something happend Wrong!', 'Error!!');
            return redirect()->back();
        }
    }
}
