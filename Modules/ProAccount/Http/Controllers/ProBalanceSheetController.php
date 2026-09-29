<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Entities\ShowRoom;
use update\Modules\ProAccount\Repositories\BalanceSheetRepository;
use update\Modules\ProAccount\Repositories\FinancialYearRepository;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class ProBalanceSheetController extends Controller
{
    protected $balanceSheetRepository, $financialYearsRepository;

    public function __construct(BalanceSheetRepository  $balanceSheetRepository, FinancialYearRepository $financialYearsRepository)
    {
        $this->balanceSheetRepository = $balanceSheetRepository;
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

                $data['assets_data'] = $this->balanceSheetRepository->assetAccountsReports(['childrenCategories:id,parent_id,name,code','childrenCategories.categories:id,parent_id,name,code','childrenCategories.transactions:id,leadger_id,date,type,amount,showroom_id,accounting_period_id','childrenCategories.categories.transactions:id,leadger_id,date,type,amount,showroom_id,accounting_period_id','childrenCategories.categories.financial_year_leadger_amounts:id,showroom_id,leadger_id,balance','childrenCategories.financial_year_leadger_amounts:id,showroom_id,leadger_id,balance'],['id','name','code','parent_id','is_cost_center']);
                $data['assets'] = $data['assets_data']['leadger'];
                $data['assets_ids'] = $data['assets_data']['asset_leadgers'];

                $data['liabilities_data'] = $this->balanceSheetRepository->liabilityAccountsReports(['childrenCategories:id,parent_id,name,code','childrenCategories.categories:id,parent_id,name,code','childrenCategories.transactions:id,leadger_id,date,type,amount,showroom_id,accounting_period_id','childrenCategories.categories.transactions:id,leadger_id,date,type,amount,showroom_id,accounting_period_id','childrenCategories.categories.financial_year_leadger_amounts:id,showroom_id,leadger_id,balance','childrenCategories.financial_year_leadger_amounts:id,showroom_id,leadger_id,balance'],['id','name','code','parent_id','is_cost_center']);
                $data['liabilities'] = $data['liabilities_data']['leadger'];
                $data['liabilities_ids'] = $data['liabilities_data']['liability_leadgers'];

                if (($key = array_search(Settings('retail_earning_leadger'), $data['liabilities_ids'])) !== false) {
                    unset($data['liabilities_ids'][$key]);
                }

                if ($request->report_type == null || $request->report_type == "fiscal_year") {
                    $data['dateFrom'] = null;
                    $data['dateTo'] = null;
                } else {
                    $data['dateFrom'] = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
                    $data['dateTo'] = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
                }

                $data['all_financial_years'] = $this->financialYearsRepository->getAll(['financial_year_leadger_amounts:id,showroom_id,leadger_id,balance']);
                if ($request->has('start_year')) {
                    $data['financial_years'] = $this->financialYearsRepository->getSpecificYears($request->start_year, $request->end_year, ['financial_year_leadger_amounts:id,showroom_id,leadger_id,balance']);
                }else {
                    $data['financial_years'] = $data['all_financial_years'];
                }
                if ($request->has('print')) {
                    \LogActivity::successLog(trans('common.Print has been Done').' - Balance Sheet', route('balance_sheet_report'), "Print");
                    return view('proaccount::reports.balance_sheet.print_view', $data);
                }
                if ($request->has('excel')) {
                    if ($request->report_type == null || $request->report_type == "fiscal_year") {
                        $this->balanceSheetRepository->csvDownloadIncomeStatement($data);
                        $filePath = public_path("/export_csv/balance_sheet.xlsx");
                        $headers = ['Content-Type: text/xlsx'];
                        $fileName = time().'-balance_sheet.xlsx';
                        \LogActivity::successLog(trans('common.CSV Download has been done').' - Balance Sheet', route('balance_sheet_report'), "CSV Download");
                        return response()->download($filePath, $fileName, $headers);
                    } else {
                        \LogActivity::successLog(trans('common.CSV Download has been done').' - Balance Sheet', route('balance_sheet_report'), "CSV Download");
                        $this->balanceSheetRepository->csvDownloadIncomeStatementDateRange($data);
                        $filePath = public_path("/export_csv/balance_sheet.xlsx");
                        $headers = ['Content-Type: text/xlsx'];
                        $fileName = time().'-balance_sheet.xlsx';
                        return response()->download($filePath, $fileName, $headers);
                    }
                }
                return view('proaccount::reports.balance_sheet.index', $data);
            }else {
                Toastr::error(trans('account.configure_this_first!'));
                return redirect()->route('account.report.configuration');
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans("common.Something Went Wrong"));
            return back();
        }
    }

    public function export_excel()
    {
        try {
            $this->balanceSheetRepository->csvDownloadIncomeStatement(null, null);
            $filePath = public_path("/export_csv/balance_sheet.xlsx");
            $headers = ['Content-Type: text/xlsx'];
            $fileName = time().'-balance_sheet.xlsx';
            \LogActivity::successLog(trans('common.CSV Download has been done').' - Balance Sheet', route('balance_sheet_report'), "CSV Download");
            return response()->download($filePath, $fileName, $headers);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }
}
