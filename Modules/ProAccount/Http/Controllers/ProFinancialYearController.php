<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use update\Modules\ProAccount\Repositories\FinancialYearRepository;
use Brian2694\Toastr\Facades\Toastr;

class ProFinancialYearController extends Controller
{
    protected $financialYearRepository;

    public function __construct(FinancialYearRepository  $financialYearRepository)
    {
        $this->financialYearRepository = $financialYearRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (Settings('income_summary_debit_leadger') != 0 && Settings('company_tax_leadger') != 0 && Settings('retail_earning_leadger') != 0) {
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;

            $data['items'] = $this->financialYearRepository->withPaginateFinancialYear($row_count,$quick_search,$sort,$column);
            if ($request->ajax()) {
                return view('proaccount::financial_years.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->financialYearRepository->withPaginateFinancialYear("all",$quick_search,$sort,$column);
                if ($request->import_as == "print") {
                    \LogActivity::successLog(trans('common.Print has been Done').' - Financial Year List', route('financial_years.index'), "Print");
                    return view('proaccount::financial_years.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    \LogActivity::successLog(trans('common.CSV Download has been done').' - Financial Year List', route('financial_years.index'), "CSV Download");
                    $this->financialYearRepository->csvDownloadFinancialYear();
                    $filePath = public_path("uploads/csv/financial-years.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-financial-years.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('proaccount::financial_years.index', $data);
        }else {
            Toastr::error(trans('account.configure_this_first!'));
            return redirect()->route('account.report.configuration');
        }
    }

    public function closing_by_id($id)
    {
        try {
            $data['row'] = $this->financialYearRepository->findByID($id);
            return view('proaccount::financial_years.closing_modal', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function closing(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data['row'] = $this->financialYearRepository->closing($request->except('_token'), $id);
            if ($data['row'] == "success") {
                DB::commit();
                \LogActivity::successLog(__('account.financial_year_has_been_closed').' - Financial Year', route('financial_years.index'), "Financial Year Closed");
                Toastr::success(trans('account.financial_year_has_been_closed.'));
            }else {
                DB::rollBack();
                \LogActivity::successLog(__('account.closing_date_must_be_greater_than_start_date_and_not greater_than_today.').' - Financial Year', route('financial_years.index'), "Financial Year Close");
                Toastr::error(trans('account.closing_date_must_be_greater_than_start_date_and_not greater_than_today.'));
            }
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
