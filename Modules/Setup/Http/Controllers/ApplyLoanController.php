<?php

namespace Modules\Setup\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Setup\Http\Requests\ApplyLoanFormRequest;
use Modules\Setup\Repositories\ApplyLoanRepositoryInterface;
use App\Repositories\UserRepositoryInterface;

class ApplyLoanController extends Controller
{
    protected $applyLoanRepository;

    public function __construct(ApplyLoanRepositoryInterface $applyLoanRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->applyLoanRepository = $applyLoanRepository;
    }

    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'desc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;
            $data['items'] = $this->applyLoanRepository->withPaginate($row_count, $quick_search, $name, $sort, $column, ['user:id,name', 'department:id,name'], ['id', 'department_id', 'user_id', 'loan_type', 'loan_date', 'amount', 'paid_loan_amount', 'approval']);
            if ($request->ajax()) {
                return view('setup::staff_loans.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->applyLoanRepository->withPaginate("all", $quick_search, $name, $sort, $column, ['user:id,name', 'department:id,name'], ['id', 'department_id', 'user_id', 'loan_type', 'loan_date', 'amount', 'paid_loan_amount', 'approval']);
                if ($request->import_as == "print") {
                    return view('setup::staff_loans.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->applyLoanRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/loan_list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-loan_list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }

            return view('setup::staff_loans.index', $data);
        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function create()
    {
        return view('setup::staff_loans.create');
    }

    public function store(ApplyLoanFormRequest $request)
    {
        try {
            $this->applyLoanRepository->create($request->except("_token"));
            \LogActivity::successLog('New Loan - ('.$request->name.') has been Applied.');
            return response()->json(['message' => __('common.Loan has been applied Successfully')]);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Loan Apply');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function edit(Request $request)
    {
        try {
            $loan = $this->applyLoanRepository->find($request->id);
            return view('setup::staff_loans.edit', [
                "loan" => $loan
            ]);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Loan Apply');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function show(Request $request)
    {
        try {
            $loan = $this->applyLoanRepository->find($request->id);
            return view('setup::staff_loans.view', [
                "loan" => $loan
            ]);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Loan Apply');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function update(ApplyLoanFormRequest $request, $id)
    {
        try {
            $this->applyLoanRepository->update($request->except("_token"), $id);
            \LogActivity::successLog('Division - ('.$request->name.') has been updated.');
            Toastr::success(__('common.Loan has been updated Successfully'));
            return redirect()->route('apply_loans.index');
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Loan Apply update');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $this->applyLoanRepository->delete($id);
            \LogActivity::successLog('Applyied Loan has been destroyed.');
            Toastr::success(__('common.Applyied Loan has been deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Division Destroy');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function loan_approval_index(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'desc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;
            $data['items'] = $this->applyLoanRepository->withPaginateApplied($row_count, $quick_search, $name, $sort, $column, null, ['user:id,name', 'department:id,name'], ['id', 'department_id', 'user_id', 'loan_type', 'loan_date', 'amount', 'paid_loan_amount', 'approval']);
            if ($request->ajax()) {
                return view('setup::approval_loans.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->applyLoanRepository->withPaginateApplied("all", $quick_search, $name, $sort, $column, null, ['user:id,name', 'department:id,name'], ['id', 'department_id', 'user_id', 'loan_type', 'loan_date', 'amount', 'paid_loan_amount', 'approval']);
                if ($request->import_as == "print") {
                    return view('setup::approval_loans.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->applyLoanRepository->csvDownloadApplied($data);
                    $filePath = public_path("uploads/csv/applied_loan_list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-applied_loan_list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('setup::approval_loans.index', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Loan Apply');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function applied_show(Request $request)
    {
        try {
            $loan = $this->applyLoanRepository->find($request->id);
            return view('setup::approval_loans.view', [
                "loan" => $loan
            ]);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Loan Apply');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function change_approval(Request $request)
    {
        try {
            $this->applyLoanRepository->change_approval($request->except("_token"));
            return 1;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function history(Request $request)
    {
        $row_count = ($request->has('row')) ? $request->row : 10;
        $sort = ($request->has('sort')) ? $request->sort : 'desc';
        $column = ($request->has('col')) ? $request->col : null;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
        $name = ($request->has('name')) ? $request->name : null;
        $data['items'] = $this->applyLoanRepository->withPaginateHistoryLoan($row_count, $quick_search, $name, $sort, $column, ['role:id,name,type', 'staff:id,phone,date_of_joining,user_id'], ['id', 'role_id', 'name', 'email', 'created_at']);
        if ($request->ajax()) {
            return view('setup::approval_loans.paginates.history_list', $data);
        }
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $this->applyLoanRepository->withPaginateHistoryLoan("all", $quick_search, $name, $sort, $column, ['role:id,name,type', 'staff:id,phone,date_of_joining,user_id'], ['id', 'role_id', 'name', 'email', 'created_at']);
            if ($request->import_as == "print") {
                return view('setup::approval_loans.paginates.history_print', $data);
            }
            if ($request->import_as == "csv") {
                $this->applyLoanRepository->csvDownloadHistoryLoan($data);
                $filePath = public_path("uploads/csv/loan_history_list.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time() . '-loan_history_list.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        }
        return view('setup::approval_loans.history',$data);
    }

    public function loanDetails(Request $request, UserRepositoryInterface $userRepository)
    {

        $user = $userRepository->findUser($request->id);

        return view('setup::approval_loans.get_details',compact('user'));
    }
}
