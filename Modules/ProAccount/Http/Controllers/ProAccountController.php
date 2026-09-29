<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProAccount\Repositories\LeadgerRepository;
use Modules\ProAccount\Repositories\SubLeadgerRepository;
use Modules\ProAccount\Repositories\CashFLowAccountRepository;
use Modules\ProAccount\Entities\AccountConfiguration;
use Brian2694\Toastr\Facades\Toastr;
use Artisan;
use \Cache;

class ProAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $leadgerRepo = new LeadgerRepository();
        $data['accounts'] = $leadgerRepo->transactional_accounts([],['id','name','code','type']);

        \LogActivity::successLog('Visited Account Configuration',route('account.configuration'), 'Account Configuration');
        return view('proaccount::configurations.index', $data);
    }

    public function entry_system_config()
    {
        abort(404);
        return view('proaccount::configurations.entry_system_config');
    }

    public function configuration_report_index()
    {
        $leadgerRepo = new LeadgerRepository();
        $data['accounts'] = $leadgerRepo->cost_center();
        $data['transaction_accounts'] = $leadgerRepo->transactional_accounts([],['id','name','code','type']);
        \LogActivity::successLog('Visited Account Report Configuration',route('account.report.configuration'), 'Account Report Configuration');
        return view('proaccount::configurations.configuration_report_index', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function approval_config_update(Request $request)
    {
        try {
            foreach ($request->except('_token','cash_type_account','bank_type_account') as $index_name => $value) {
                $setting = AccountConfiguration::where('name',$index_name)->first();
                if ($setting) {
                    $setting->update(['value' => $value]);
                }else {
                    AccountConfiguration::create([
                        'name' => $index_name,
                        'value' => $value,
                    ]);
                }
            }

            Artisan::call('optimize:clear');

            $datas = [];
            foreach (AccountConfiguration::get() as $key => $setting) {
                $datas[$setting->name] = $setting->value;
            }
            Cache::rememberForever('account_configurations', function () use($datas) {
                return $datas;
            });

            \LogActivity::successLog('Account Configuration Updated',route('account.report.configuration'), 'Account Configuration Updated');
            Toastr::success(trans('account.updated_successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error($e->getMessage());
            return back();
        }
    }

    public function recievable_account(Request $request)
    {
        $row_count = ($request->has('row')) ? $request->row : 10 ;
        $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
        $column = ($request->has('col')) ? $request->col : null ;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
        $name = ($request->has('name')) ? $request->name : null ;
        $subLeadgerRepo = new SubLeadgerRepository();
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $subLeadgerRepo->withPaginateRecievableAcc("all",$quick_search,$sort,$column,[],['code','name','id','is_active']);
            if ($request->import_as == "print") {
                \LogActivity::successLog(trans('common.Print has been Done').' - Recievable List', route('quotation.index'), "Print");
                return view('proaccount::recievable_account.paginates.print', $data);
            }
            if ($request->import_as == "csv") {
                \LogActivity::successLog(trans('common.CSV Download has been done').' - Recievable List', route('quotation.index'), "CSV Download");
                $subLeadgerRepo->csvDownloadRecievableAcc(Settings('account_recievable'));
                $filePath = public_path("uploads/csv/recievable_account.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-recievable_account.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        }
        $data['items'] = $subLeadgerRepo->withPaginateRecievableAcc($row_count,$quick_search,$sort,$column,[],['code','name','id','is_active']);
        if ($request->ajax()) {
            return view('proaccount::recievable_account.paginates.list', $data);
        }
        return view('proaccount::recievable_account.index', $data);
    }

    public function payable_account(Request $request)
    {
        $row_count = ($request->has('row')) ? $request->row : 10 ;
        $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
        $column = ($request->has('col')) ? $request->col : null ;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
        $name = ($request->has('name')) ? $request->name : null ;
        $subLeadgerRepo = new SubLeadgerRepository();
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $subLeadgerRepo->withPaginatePayableAcc("all",$quick_search,$sort,$column,[],['code','name','id','is_active']);
            if ($request->import_as == "print") {
                \LogActivity::successLog(trans('common.Print has been Done').' - Payable List', route('quotation.index'), "Print");
                return view('proaccount::payable_account.paginates.print', $data);
            }
            if ($request->import_as == "csv") {
                \LogActivity::successLog(trans('common.CSV Download has been done').' - Payable List', route('quotation.index'), "CSV Download");
                $subLeadgerRepo->csvDownloadPayableAcc(Settings('account_payable'));
                $filePath = public_path("uploads/csv/payable_account.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-payable_account.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        }
        $data['items'] = $subLeadgerRepo->withPaginatePayableAcc($row_count,$quick_search,$sort,$column,[],['code','name','id','is_active']);
        if ($request->ajax()) {
            return view('proaccount::payable_account.paginates.list', $data);
        }
        return view('proaccount::payable_account.index', $data);
    }
}
