<?php

namespace Modules\Attendance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Attendance\Entities\Attendance;
use Modules\RolePermission\Entities\Role;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Attendance\Http\Requests\AttendanceReportFormRequest;
use Modules\Attendance\Repositories\AttendanceRepositoryInterface;
use App\User;
use PDF;

class AttendanceReportController extends Controller
{
    protected $attaendanceRepository;

    public function __construct(AttendanceRepositoryInterface $attaendanceRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->attaendanceRepository = $attaendanceRepository;
    }
    public function index()
    {
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return view('attendance::attendance_reports.index', compact('months'));
    }

    public function reports(Request $request)
    {
        try {
            $data['reports'] = $this->attaendanceRepository->report($request->all());
            $users = $this->attaendanceRepository->user($request->all());
            $data['report_dates'] = $this->attaendanceRepository->date($request->all());
            $data['r'] = $request->role_id;
            $data['m'] = $request->month;
            $data['y'] = $request->year;
            $data['months'] = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'desc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            $data['items'] = $this->attaendanceRepository->withAttendancePaginate($row_count,$quick_search,$name,$sort,$column,$request->all());

            if ($request->ajax()) {
                return view('attendance::attendance_reports.paginates.list', $data);
            }

            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->attaendanceRepository->withAttendancePaginate("all",$quick_search,$name,$sort,$column,$request->all());
                if ($request->import_as == "print") {
                    return view('attendance::attendance_reports.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->attaendanceRepository->csvAttendanceDownload($data);
                    $filePath = public_path("uploads/csv/attendance.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-attendance.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('attendance::attendance_reports.index', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Attendance Report');
            return redirect()->back();
        }
    }

    public function attendance_report_print($role_id, $month, $year)
    {
        try{
            $users = User::where('role_id', $role_id)->get();
            $report_dates = Attendance::where('month', $month)->where('year', $year)->distinct()->get(['date']);;
            $role = Role::find($role_id);
            $r = $role_id;
            $m = $month;
            $y = $year;
            $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

            $customPaper = array(0, 0, 700.00, 1000.80);
            $pdf = PDF::loadView(
                'attendance::attendance_reports.staff_attendance_print',
                [
                    'report_dates' => $report_dates,
                    'users' => $users,
                    'r' => $r,
                    'm' => $m,
                    'y' => $y,
                    'role' => $role,
                    'months' => $months
                ]
            )->setPaper('A4', 'landscape');
            return $pdf->stream('staff_attendance.pdf');

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return redirect()->back();
        }
    }
}
