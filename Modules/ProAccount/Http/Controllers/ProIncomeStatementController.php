<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Entities\ShowRoom;
use update\Modules\ProAccount\Repositories\IncomeStatementRepository;
use update\Modules\ProAccount\Repositories\FinancialYearRepository;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class ProIncomeStatementController extends Controller
{
    protected $incomeStatementRepository, $financialYearsRepository;

    public function __construct(IncomeStatementRepository  $incomeStatementRepository, FinancialYearRepository $financialYearsRepository)
    {
        $this->incomeStatementRepository = $incomeStatementRepository;
        $this->financialYearsRepository = $financialYearsRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        try {
            if (Settings('direct_income_leadger') != 0 && Settings('in_direct_income_leadger') != 0 && Settings('direct_expense_leadger') != 0 && Settings('in_direct_expense_leadger') != 0) {

                $data['showroom_id'] = ($request->showroom_id != null) ? $request->showroom_id : session()->get('showroom_id');
                if ($request->showroom_id != null) {
                    $data['selected_showroom'] = ShowRoom::find($request->showroom_id);
                }

                $data['direct_income_data'] = $this->incomeStatementRepository->directIncomeAccountsReports();
                $data['direct_income'] = $data['direct_income_data']['leadger'];
                $data['direct_income_ids'] = $data['direct_income_data']['direct_income_leadgers'];

                $data['indirect_income_data'] = $this->incomeStatementRepository->indirectIncomeAccountsReports();
                $data['indirect_income'] = $data['indirect_income_data']['leadger'];
                $data['indirect_income_ids'] = array_diff($data['indirect_income_data']['direct_income_leadgers'],[Settings('income_summary_debit_leadger')]);

                $data['direct_expense_data'] = $this->incomeStatementRepository->directExpenseAccountsReports();
                $data['direct_expense'] = $data['direct_expense_data']['leadger'];
                $data['direct_expense_ids'] = $data['direct_expense_data']['direct_income_leadgers'];

                $data['indirect_expense_data'] = $this->incomeStatementRepository->indirectExpenseAccountsReports();
                $data['indirect_expense'] = $data['indirect_expense_data']['leadger'];
                $data['indirect_expense_ids'] = $data['indirect_expense_data']['direct_income_leadgers'];

                if (($key = array_search(Settings('default_purchase_account'), $data['direct_expense_ids'])) !== false) {
                    unset($data['direct_expense_ids'][$key]);
                }
                if (($key = array_search(Settings('company_income_tax'), $data['direct_expense_ids'])) !== false) {
                    unset($data['direct_expense_ids'][$key]);
                }
                if (($key = array_search(Settings('company_income_tax'), $data['indirect_expense_ids'])) !== false) {
                    unset($data['indirect_expense_ids'][$key]);
                }
                if ($request->report_type == null || $request->report_type == "fiscal_year") {
                    $data['dateFrom'] = null;
                    $data['dateTo'] = null;
                } else {
                    $data['dateFrom'] = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
                    $data['dateTo'] = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
                }

                $data['all_financial_years'] = $this->financialYearsRepository->getAll();
                if ($request->has('start_year')) {
                    $data['financial_years'] = $this->financialYearsRepository->getSpecificYears($request->start_year, $request->end_year);
                }else {
                    $data['financial_years'] = $data['all_financial_years'];
                }
                if ($request->has('print')) {
                    \LogActivity::successLog(trans('common.Print has been Done').' - Income Statement', route('income_statement_report'), "Print");
                    return view('proaccount::reports.income_statement_report.print_view', $data);
                }
                if ($request->has('excel')) {
                    try {
                        if ($request->report_type == null || $request->report_type == "fiscal_year") {
                            $this->incomeStatementRepository->csvDownloadIncomeStatement($data);
                            \LogActivity::successLog(trans('common.CSV Download has been done').' - Income Statement', route('income_statement_report'), "CSV Download");
                            $filePath = public_path("/export_csv/income_statement_report.xlsx");
                            $headers = ['Content-Type: text/xlsx'];
                            $fileName = time().'-income_statement_report.xlsx';
                            return response()->download($filePath, $fileName, $headers);
                        } else {
                            $this->incomeStatementRepository->csvDownloadIncomeStatementDateRange($data);
                        }
                        \LogActivity::successLog(trans('common.CSV Download has been done').' - Income Statement', route('income_statement_report'), "CSV Download");
                        $filePath = public_path("/export_csv/income_statement_report.xlsx");
                        $headers = ['Content-Type: text/xlsx'];
                        $fileName = time().'-income_statement_report.xlsx';
                        return response()->download($filePath, $fileName, $headers);
                    } catch (\Exception $e) {
                        return response()->json($e);
                    }
                }
                return view('proaccount::reports.income_statement_report.index', $data);
            }else {
                Toastr::error(trans('account.configure_this_first!'));
                return redirect()->route('account.report.configuration');
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return back();
        }
    }

    public function export_excel()
    {
        try {
            $this->incomeStatementRepository->csvDownloadIncomeStatement(null, null);
            \LogActivity::successLog(trans('common.CSV Download has been done').' - Income Statement', route('income_statement_report'), "CSV Download");
            $filePath = public_path("/export_csv/income_statement_report.xlsx");
            $headers = ['Content-Type: text/xlsx'];
            $fileName = time().'-income_statement_report.xlsx';
            return response()->download($filePath, $fileName, $headers);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function profit_loss_single_entry_report(Request $request)
    {
        try {
            $data['showroom_id'] = ($request->showroom_id != null) ? $request->showroom_id : session()->get('showroom_id');
            if ($request->showroom_id != null) {
                $data['selected_showroom'] = ShowRoom::find($request->showroom_id);
            }
            $data['dateFrom'] = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $data['dateTo'] = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

            $data['cash_incomes'] = $this->incomeStatementRepository->directIncomeCashAccounts();

            $data['bank_incomes'] = $this->incomeStatementRepository->directIncomeBankAccounts();
            $data['transactions'] = $this->incomeStatementRepository->getTransactions($data['cash_incomes'],$data['bank_incomes'],$data['showroom_id'],$data['dateFrom'],$data['dateTo']);
            $data['cash_data'] = $data['transactions']['cash'];
            $data['bank_data'] = $data['transactions']['bank'];
            if ($request->has('print')) {
                return view('proaccount::reports.profit_loss_single_entry_report.print_view',$data);
            }
            return view('proaccount::reports.profit_loss_single_entry_report.index',$data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return back();
        }
    }
}
