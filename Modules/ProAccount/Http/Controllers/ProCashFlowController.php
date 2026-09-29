<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProAccount\Repositories\CashFlowRepository;
use App\Traits\PdfGenerate;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;

class ProCashFlowController extends Controller
{
    use PdfGenerate;
    protected $cashFlowRepository;

    public function __construct(CashFlowRepository  $cashFlowRepository)
    {
        $this->cashFlowRepository = $cashFlowRepository;
    }

    public function index(Request $request)
    {
        $data['dateFrom'] = null;
        $data['dateTo'] = null;
        $cash_in = $request->has('cash_in') ? "cash-in" : "no-cash-in";
        $cash_out = $request->has('cash_out') ? "cash-out" : "no-cash-out";
        if ($request->has('dateFrom')) {
            $data['dateFrom'] = ($request->dateFrom) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $data['dateTo'] = ($request->dateTo) ? Carbon::parse($request->dateTo)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            if ($request->has('cash_in')) {
                $data['incomes'] = $this->cashFlowRepository->cashRecieveListIncome($data['dateFrom'], $data['dateTo']);
            }
            if ($request->has('cash_out')) {
                $data['expenses'] = $this->cashFlowRepository->cashPaymentListExpense($data['dateFrom'], $data['dateTo']);
            }
        }else {
            if ($request->has('cash_in')) {
                $data['incomes'] = $this->cashFlowRepository->cashRecieveListIncome(null, null);
            }
            if ($request->has('cash_out')) {
                $data['expenses'] = $this->cashFlowRepository->cashPaymentListExpense(null, null);
            }
        }

        if ($request->has('excel')) {
            return redirect()->route('vouchers.cash_flow_export_excel', [$cash_in, $cash_out, $data['dateFrom'], $data['dateTo']]);
        }

        if ($request->has('print')) {
            $data['datas'] = $this->cashFlowRepository->pdfDownloadCashflow($cash_in, $cash_out, $data['dateFrom'], $data['dateTo']);
            return view('proaccount::cash_flow.print', $data);
        }

        return view('proaccount::cash_flow.index', $data);
    }

    public function export_excel($cash_in, $cash_out, $start_date, $end_date)
    {
        try {
            if ($cash_in == "cash-in" || $cash_out == "cash-out") {
                \LogActivity::successLog(trans('common.CSV Download has been done').' - CashBook', route('cashbook.index'), "CSV Download");
                $this->cashFlowRepository->csvDownloadCashflow($cash_in, $cash_out, $start_date, $end_date);
                $filePath = public_path("uploads/csv/cash_flow_list.xlsx");
            	$headers = ['Content-Type: text/xlsx'];
            	$fileName = time().'-cash-flow.xlsx';
                return response()->download($filePath, $fileName, $headers);
            }
            Toastr::warning(trans('account.please_select_cash_flow_first'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }
}
