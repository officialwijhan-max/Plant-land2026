<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProAccount\Repositories\CashFLowAccountRepository;
use Modules\ProAccount\Repositories\CashFlowReportRepository;
use App\Traits\PdfGenerate;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;

class ProCashFlowReportController extends Controller
{
    use PdfGenerate;
    protected $cashFlowReportRepository;

    public function __construct(CashFlowReportRepository $cashFlowReportRepository)
    {
        $this->cashFlowReportRepository = $cashFlowReportRepository;
    }

    public function cash_flow_report_view(Request $request)
    {
        try {
            $data['leadgerAccount'] = null;
            $data['account_type'] = null;
            $data['dateFrom'] = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : null;
            $data['dateTo'] = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : null;
            $data['account_id'] = ($request->cashflowId != null) ? $request->cashflowId : null;

            if ($data['dateFrom'] != null && $data['dateTo'] != null && $data['account_id'] == null) {
                Toastr::warning("proaccount::account.select_account_first");
                return view('proaccount::reports.cash_flow_report.cash_flow_report', $data);
            }
            if ($data['dateTo'] != null && $data['dateFrom'] == null) {
                Toastr::warning('proaccount::account.you_need_to_select_date_from_when_you_selected_date_to');
                return view('proaccount::reports.cash_flow_report.cash_flow_report', $data);
            }
            if ($data['dateTo'] == null && $data['dateFrom'] != null) {
                Toastr::warning('proaccount::account.you_need_to_select_date_to_when_you_selected_date_from');
                return view('proaccount::reports.cash_flow_report.cash_flow_report', $data);
            }
            if ($data['account_id'] != null) {
                $cashFlowRepo = new CashFLowAccountRepository();
                $data['cashFlowAccount'] = $cashFlowRepo->find($request->cashflowId);
                $data['balance'] = $this->cashFlowReportRepository->balanceBeforeDate($data['dateFrom'], $data['cashFlowAccount']);
                $data['account_type'] = $data['cashFlowAccount']->type;
                $data['transactions'] = $this->cashFlowReportRepository->search($data['dateFrom'], $data['dateTo'], $data['account_id']);
                if ($request->ajax()) {
                    if ($data['account_type'] == 1 || $data['account_type'] == 3) {
                        return view('proaccount::reports.cash_flow_report.component.components.debit_transaction_list_table', $data);
                    }else {
                        return view('proaccount::reports.cash_flow_report.component.components.credit_transaction_list_table', $data);
                    }
                }
                if ($request->has('pdf')) {
                    $data['transactions'] = $this->cashFlowReportRepository->searchPrint($data['dateFrom'], $data['dateTo'], $data['account_id']);
                    \LogActivity::successLog('PDF download - CashFlow Report', route('vouchers.cash_flow'), "PDF");
                    return $this->getPDF('proaccount::reports.cash_flow_report.pdf',$data, 'cash_flow_report');
                }
                if ($request->has('print')) {
                    \LogActivity::successLog(trans('common.Print has been Done').' - CashFlow Report', route('vouchers.cash_flow'), "Print");
                    $data['transactions'] = $this->cashFlowReportRepository->searchPrint($data['dateFrom'], $data['dateTo'], $data['account_id']);
                    return view('proaccount::reports.cash_flow_report.print_view', $data);
                }
                return view('proaccount::reports.cash_flow_report.cash_flow_report', $data);
            }
            else {
                return view('proaccount::reports.cash_flow_report.cash_flow_report');
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }
}
