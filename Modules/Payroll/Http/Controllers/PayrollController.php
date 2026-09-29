<?php

namespace Modules\Payroll\Http\Controllers;

use PDF;
use App\User;
use App\Traits\PdfGenerate;
use App\Traits\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Setup\Entities\ApplyLoan;
use Illuminate\Contracts\Support\Renderable;
use App\Repositories\UserRepositoryInterface;
use App\Traits\ChecksAccountBalance;
use Modules\Account\Entities\ChartAccount;
use Modules\Payroll\Entities\Payroll;
use Modules\ProAccount\Repositories\LeadgerRepository;
use Modules\Payroll\Http\Requests\PayrollFilterFormRequest;
use Modules\Payroll\Http\Requests\PayrollReportFormRequest;
use Modules\Payroll\Repositories\PayrollRepositoryInterface;

class PayrollController extends Controller
{
    use Notification, PdfGenerate, ChecksAccountBalance;
    protected $payrollRepository,$userRepository;

    public function __construct(PayrollRepositoryInterface $payrollRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->payrollRepository = $payrollRepository;
    }

    public function index()
    {
    	try{
    		$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        	return view('payroll::payrolls.index', compact('months'));
    	}
    	catch(\Exception $e)
    	{
		   Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
    		\LogActivity::errorLog($e->getMessage().' - Error has been detected for payroll');
            return redirect()->back();
    	}

    }

    public function search_for_payroll(Request $request)
    {
        try {
            $data['r'] = $request->role_id;
            $data['m'] = $request->month;
            $data['y'] = $request->year;
            $data['months'] = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];


            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'desc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;

            $data['items'] = $this->payrollRepository->userPaginate($row_count, $quick_search, $name, $sort, $column, $request->all(), ['staff:id,user_id,department_id,employee_id,basic_salary,phone', 'staff.department:id,name', 'role:id,name', 'loans:id,user_id,amount,paid_loan_amount', 'staff.payrolls'], ['id', 'name', 'role_id']);
            if ($request->ajax()) {
                return view('payroll::payrolls.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->payrollRepository->userPaginate("all", $quick_search, $name, $sort, $column, $request->all(), ['staff:id,user_id,department_id,employee_id,basic_salary,phone', 'staff.department:id,name', 'role:id,name', 'loans:id,user_id,amount,paid_loan_amount', 'staff.payrolls'], ['id', 'name', 'role_id']);
                if ($request->import_as == "print") {
					return view('payroll::payrolls.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->payrollRepository->csvUserPaginateDownload($data);
					$filePath = public_path("uploads/csv/payrolls.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-payrolls.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }

            return view('payroll::payrolls.index', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for payroll');
            return redirect()->back();
        }
    }

    public function generatePayroll(Request $request, $id, $payroll_month, $payroll_year)
	{
		try{
			$staffDetails = $this->payrollRepository->userFind($id);
			$month = date('m', strtotime($payroll_month));
			$attendances = $this->payrollRepository->attendance($id,$payroll_month,$payroll_year);

			$p = 0;
			$l = 0;
			$a = 0;
			$f = 0;
			$h = 0;
			foreach ($attendances as $value) {
				if ($value->attendance == 'P') {
					$p++;
				} elseif ($value->attendance == 'L') {
					$l++;
				} elseif ($value->attendance == 'A') {
					$a++;
				} elseif ($value->attendance == 'F') {
					$f++;
				} elseif ($value->attendance == 'H') {
					$h++;
				}
			}
            $loans = ApplyLoan::Nonpaid()->where('user_id', $id)->get();
			$approve_leaves = $this->payrollRepository->leaveApprove($id);

			return view('payroll::payrolls.generatePayroll', compact('staffDetails', 'payroll_month', 'payroll_year', 'p', 'l', 'a', 'f', 'h', 'loans'));
		}catch (\Exception $e) {
			\LogActivity::errorLog($e->getMessage());
		   Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
		   return redirect()->back();
		}
	}

    public function savePayrollData(Request $request,UserRepositoryInterface $userRepository)
	{
	    $validate_rules = [
            'net_salary' => "required"
        ];
		$request->validate($validate_rules, validationMessage($validate_rules));
		DB::beginTransaction();
		try{
            $this->payrollRepository->create($request->except("_token"));
            $staff = $userRepository->find($request->staff_id);
            \LogActivity::successLog('Payroll Generated for - '. $staff->employee_id);
            DB::commit();
			Toastr::success(__('payroll.Payroll Generated Successfully'), __('common.Success'));
			return redirect()->route('payroll.index');
		}catch (\Exception $e) {
		    DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
		   Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
		   return redirect()->back();
		}
	}

    public function paymentPayroll(Request $request)
    {
        try {
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $ledgerRepo = new LeadgerRepository();
                $data['payAccounts'] = $ledgerRepo->cashBankAccounts();
            }
            $data['payrollDetails'] = $this->payrollRepository->find($request->id);
            $data['role_id'] = $request->role_id;
			return view('payroll::payrolls.paymentPayroll', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return redirect()->back();
        }
    }

    public function savePayrollPaymentData(Request $request)
	{
	    $request->validate([
	        'payment_mode' => 'required',
	    ], [
	        'payment_mode.required' => __('purchase.Please Select method'),
	    ]);

	    // PayrollRepository::savePayrollPaymentData() always posts payroll
	    // payouts against the fixed "Cash account" (code 01-01-02), not a
	    // per-branch account, regardless of any payment_mode field - so
	    // that's the account this check has to look at.
	    if (! (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro")) {
	        $payroll = Payroll::find($request->payroll_generate_id);
	        $cashAccount = ChartAccount::where('code', '01-01-02')->first();
	        $insufficientFundsMessage = $payroll && $cashAccount
	            ? $this->insufficientFundsForAccount($cashAccount->id, (float) $payroll->net_salary)
	            : null;
	        if ($insufficientFundsMessage) {
	            Toastr::error($insufficientFundsMessage, __('common.Error'));
	            return redirect()->back();
	        }
	    }

	    DB::beginTransaction();
		try{
            $payroll = $this->payrollRepository->savePayrollPaymentData($request->except("_token"));
            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
                        ->where('is_active','1')
                        ->get(['id','role_id']);
            $created_by = Auth::user()->name;
            $content = __('notification.Salary Has been generated by').$created_by. __('notification. .Your Net Salary was:').$payroll->net_salary.'';
            $number = $payroll->staff->phone;
			$subject=__('notification.Salary Generate Reminder');
            $message = __('notification.Salary Has been generated by').$created_by. __('notification. .Your Net Salary was:').$payroll->net_salary.'';
            $this->sendNotification($payroll,$payroll->staff->user->email, $subject, $content,$number,$message,$users,null,null);
            \LogActivity::successLog('Payroll Payment paid for - '. $payroll->staff->employee_id);
            DB::commit();
            Toastr::success(__('payroll.Payment Has been done Successfully'), __('common.Success'));
			return back();
		}catch (\Exception $e) {
		    DB::rollBack();
			\LogActivity::errorLog($e->getMessage());
		   Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
		   return redirect()->back();
		}
	}

	public function viewPayslip(Request $request)
	{
		try{
			$payrollDetails = $this->payrollRepository->find($request->id);
			$payrollEarnDetails = $this->payrollRepository->payrollEarnDetails($request->id);
			$payrollDedcDetails = $this->payrollRepository->payrollDedcDetails($request->id);

			return view('payroll::payrolls.viewPayslip', compact('payrollDetails', 'payrollEarnDetails', 'payrollDedcDetails'));
		}catch (\Exception $e) {
		   Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
		   return redirect()->back();
		}
	}


    public function report_index()
    {
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return view('payroll::payroll_reports.payroll', compact('months'));
    }

    public function searchPayrollReport(Request $request)
    {
		try{
            $data['r'] = $request->role_id;
            $data['m'] = $request->month;
            $data['y'] = $request->year;
            $data['months'] = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $payrolls = $this->payrollRepository->payrollReports($request->role_id, $request->month, $request->year);

            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'desc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;

            $data['items'] = $this->payrollRepository->withPayrollReportsPaginate($row_count, $quick_search, $name, $sort, $column, $request->all(), ['staff:id,user_id,employee_id', 'staff.user:id,name', 'role:id,name'], ['id', 'staff_id', 'role_id', 'payroll_month', 'payroll_year', 'basic_salary', 'gross_salary', 'net_salary', 'total_earning', 'total_deduction', 'tax', 'payment_date']);

            if ($request->ajax()) {
                return view('payroll::payroll_reports.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                $data['items'] = $this->payrollRepository->withPayrollReportsPaginate("all", $quick_search, $name, $sort, $column, $request->all(), ['staff:id,user_id,employee_id', 'staff.user:id,name', 'role:id,name'], ['id', 'staff_id', 'role_id', 'payroll_month', 'payroll_year', 'basic_salary', 'gross_salary', 'net_salary', 'total_earning', 'total_deduction', 'tax', 'payment_date']);

                set_time_limit(-1);
                if ($request->import_as == "csv") {
                    $this->payrollRepository->csvPayrollReportsDownload($data);
					$filePath = public_path("uploads/csv/payroll_reports.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-payroll_reports.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
                if ($request->import_as == "print") {
					return view('payroll::payroll_reports.paginates.print', $data);
                }
            }

			return view('payroll::payroll_reports.payroll', $data);
		}catch (\Exception $e) {
			\LogActivity::errorLog($e->getMessage());
		   Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
		   return redirect()->back();
		}
    }

    public function getPdf($id)
    {
        try {
            $payrollDetails = $this->payrollRepository->find($id);
			return $this->getPayroll('payroll::payrolls.viewPayslip', $payrollDetails);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }
}
