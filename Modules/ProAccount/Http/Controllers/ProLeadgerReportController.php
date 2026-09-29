<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Entities\ShowRoom;
use Modules\ProAccount\Repositories\SubLeadgerRepository;
use Modules\ProAccount\Repositories\LeadgerRepository;
use update\Modules\ProAccount\Repositories\LeadgerReportRepository;
use Maatwebsite\Excel\Facades\Excel;
use update\Modules\ProAccount\Exports\LeadgerReportExport;
use App\Traits\PdfGenerate;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;

class ProLeadgerReportController extends Controller
{
    use PdfGenerate;
    protected $leadgerRepository;

    public function __construct(LeadgerReportRepository $leadgerRepository)
    {
        $this->leadgerRepository = $leadgerRepository;
    }

    public function sub_leadger_report_view(Request $request)
    {
        $data['leadgerAccount'] = null;
        $data['account_type'] = null;
        $data['dateFrom'] = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : null;
        $data['dateTo'] = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : null;
        $data['account_id'] = ($request->subleagerId != null) ? $request->subleagerId : null;

        set_time_limit(-1);
        if ($data['dateFrom'] != null && $data['dateTo'] != null && $data['account_id'] == null) {
            Toastr::warning("Select Account First");
            return view('proaccount::reports.sub_leadger_report.subleadger_report', $data);
        }
        if ($data['dateTo'] != null && $data['dateFrom'] == null) {
            Toastr::warning('You need to set date-from when you select date-to.');
            return view('proaccount::reports.sub_leadger_report.subleadger_report', $data);
        }
        if ($data['dateTo'] == null && $data['dateFrom'] != null) {
            Toastr::warning('You need to set date-to when you select date-from.');
            return view('proaccount::reports.sub_leadger_report.subleadger_report', $data);
        }
        if ($data['account_id'] != null) {
            $subLeadgerRepo = new SubLeadgerRepository();
            $data['leadgerAccount'] = $subLeadgerRepo->find($request->subleagerId,['leadger:id,name,code,type'],['id','leadger_id','name','code']);
            $data['balance'] = 0;
            $data['account_type'] = $data['leadgerAccount']->leadger->type;
            $data['transactions'] = $this->leadgerRepository->searchSubleadger($data['dateFrom'], $data['dateTo'], $data['account_id'], session()->get('showroom_id'),['leadger:id,name,code,type','voucher:id,type,amount,date,txn_id'],['id','type','is_opening','voucher_id','leadger_id','sub_leadger_id','amount','narration']);
            $data['real_transactions'] = $data['transactions']['real_transactions'];
            $data['opening_transactions'] = $data['transactions']['opening_transactions'];
            if ($request->has('pdf')) {
                return $this->getPDF('proaccount::reports.sub_leadger_report.pdf',$data, 'sub_leadger_report');
            }
            if ($request->has('print')) {
                return view('proaccount::reports.sub_leadger_report.print_view', $data);
            }
            return view('proaccount::reports.sub_leadger_report.subleadger_report', $data);
        }
        else {
            return view('proaccount::reports.sub_leadger_report.subleadger_report');
        }
    }

    public function leadger_report_view(Request $request)
    {
        $data['leadgerAccount'] = null;
        $data['account_type'] = null;
        $data['dateFrom'] = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : null;
        $data['dateTo'] = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : null;
        $data['account_id'] = ($request->leadgerId != null) ? $request->leadgerId : null;
        $data['showroom_id'] = ($request->showroom_id != null) ? $request->showroom_id : session()->get('showroom_id');
        if ($request->showroom_id != null) {
            $data['selected_showroom'] = ShowRoom::find($request->showroom_id);
        }

        set_time_limit(-1);
        if ($data['dateFrom'] != null && $data['dateTo'] != null && $data['account_id'] == null) {
            Toastr::warning("Select Account First");
            return view('proaccount::reports.leadger_report.leadger_report', $data);
        }
        if ($data['dateTo'] != null && $data['dateFrom'] == null) {
            Toastr::warning('You need to set date-from when you select date-to.');
            return view('proaccount::reports.leadger_report.leadger_report', $data);
        }
        if ($data['dateTo'] == null && $data['dateFrom'] != null) {
            Toastr::warning('You need to set date-to when you select date-from.');
            return view('proaccount::reports.leadger_report.leadger_report', $data);
        }
        if ($data['account_id'] != null) {
            $leadgerRepo = new LeadgerRepository();
            $data['leadgerAccount'] = $leadgerRepo->find($request->leadgerId);
            $data['balance'] = ($data['dateFrom'] != null) ? $this->leadgerRepository->balanceBeforeDate($data['dateFrom'], $data['leadgerAccount'], $data['showroom_id']) : 0;

            $data['account_type'] = $data['leadgerAccount']->type;
            $data['transactions'] = $this->leadgerRepository->search($data['dateFrom'], $data['dateTo'], $data['account_id'], $data['showroom_id']);


            if ($request->has('pdf')) {
                return $this->getPDF('proaccount::reports.leadger_report.pdf',$data, 'leadger_report');
            }
            if ($request->has('print')) {
                return view('proaccount::reports.leadger_report.print_view', $data);
            }
            if ($request->has('excel')) {
                if (file_exists(public_path("uploads/csv/leadger_report.xlsx"))) {
                    unlink(public_path("uploads/csv/leadger_report.xlsx"));
                  }
                Excel::store(new LeadgerReportExport($data), 'uploads/csv/leadger_report.xlsx', 'public_folder');
                $filePath = public_path("uploads/csv/leadger_report.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-leadger_report.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }

            return view('proaccount::reports.leadger_report.leadger_report', $data);
        }
        else {
            return view('proaccount::reports.leadger_report.leadger_report');
        }
    }

    public function partner_summary_reports(Request $request)
    {
        try {
            $data['leadgerAccount'] = null;
            $data['account_type'] = null;
            $data['dateFrom'] = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $data['dateTo'] = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $data['account_id'] = ($request->subleagerId != null) ? $request->subleagerId : null;
            $data['type'] = ($request->type == null || $request->type == "all") ? null : $request->type;

            set_time_limit(-1);
            if ($data['dateFrom'] != null && $data['dateTo'] != null && $data['account_id'] == null) {
                Toastr::warning("Select Account First");
                return view('proaccount::reports.partner_summary_reports.subleadger_report', $data);
            }
            if ($data['dateTo'] != null && $data['dateFrom'] == null) {
                Toastr::warning('You need to set date-from when you select date-to.');
                return view('proaccount::reports.partner_summary_reports.subleadger_report', $data);
            }
            if ($data['dateTo'] == null && $data['dateFrom'] != null) {
                Toastr::warning('You need to set date-to when you select date-from.');
                return view('proaccount::reports.partner_summary_reports.subleadger_report', $data);
            }
            if ($data['account_id'] != null) {
                $subLeadgerRepo = new SubLeadgerRepository();
                $data['leadgerAccount'] = $subLeadgerRepo->find($request->subleagerId);
                $data['balance'] = ($data['dateFrom'] != null) ? $this->leadgerRepository->balanceBeforeDateSubleadger($data['dateFrom'], $data['leadgerAccount']) : 0;

                $data['account_type'] = $data['leadgerAccount']->leadger->type;
                $data['transactions'] = $this->leadgerRepository->searchSubleadgerSummary($data['dateFrom'], $data['dateTo'], $data['account_id'], $data['type']);
                if ($request->has('excel')) {
                    return redirect()->route('leadger_report.partner_summary_export_csv',[
                        'dateFrom' => $data['dateFrom'],
                        'dateTo' => $data['dateTo'],
                        'account_id' => $data['account_id'],
                        'type' => ($data['type']) ? $data['type'] : "all",
                    ]);
                }
                if ($request->has('print')) {
                    return view('proaccount::reports.partner_summary_reports.print_view', $data);
                }
                return view('proaccount::reports.partner_summary_reports.subleadger_report', $data);
            }
            else {
                return view('proaccount::reports.partner_summary_reports.subleadger_report');
            }
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function partner_summary_export_csv($dateFrom, $dateTo, $account_id, $type)
    {
        try {
            set_time_limit(-1);
            $this->leadgerRepository->csvDownload($dateFrom, $dateTo, $account_id, $type);
            $filePath = public_path("uploads/csv/partner_accounts_summary.xlsx");
        	$headers = ['Content-Type: text/csv'];
        	$fileName = time().'-partner_accounts_summary.xlsx';

        	return response()->download($filePath, $fileName, $headers);
            return back();
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function self_account_balance(Request $request)
    {
        try {
            $data['leadgerAccount'] = null;
            $data['account_type'] = null;
            $data['dateFrom'] = null;
            $data['dateTo'] = null;
            $data['account_id'] = (auth()->user()->role_id == 7) ? auth()->user()->agent->morph->id : auth()->user()->contact->morph->id;

            set_time_limit(-1);
            if ($data['account_id'] != null) {
                $subLeadgerRepo = new SubLeadgerRepository();
                $data['leadgerAccount'] = $subLeadgerRepo->find($data['account_id'],['leadger:id,name,code,type'],['id','leadger_id','name','code']);
                $data['balance'] = 0;
                $data['account_type'] = $data['leadgerAccount']->leadger->type;
                $data['transactions'] = $this->leadgerRepository->searchSubleadger($data['dateFrom'], $data['dateTo'], $data['account_id'], session()->get('showroom_id'),['leadger:id,name,code,type','voucher:id,type,amount,date,txn_id'],['id','type','is_opening','voucher_id','leadger_id','sub_leadger_id','amount','narration']);
                $data['real_transactions'] = $data['transactions']['real_transactions'];
                $data['opening_transactions'] = $data['transactions']['opening_transactions'];
                if ($request->has('print')) {
                    return view('proaccount::reports.sub_leadger_report.print_view', $data);
                }
                return view('proaccount::reports.self_account_balance.subleadger_report', $data);
            }
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }
}
