<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Entities\ShowRoom;
use Modules\ProAccount\Repositories\TrialBalanceRepository;
use App\Traits\PdfGenerate;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;

class ProTrialBalanceController extends Controller
{
    use PdfGenerate;
    protected $trialBalanceRepository;

    public function __construct(TrialBalanceRepository  $trialBalanceRepository)
    {
        $this->trialBalanceRepository = $trialBalanceRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        try {
            $data['start_date'] = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $data['end_date'] = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $data['showroom_id'] = ($request->showroom_id != null) ? $request->showroom_id : null;
            if ($request->showroom_id != null) {
                $data['selected_showroom'] = ShowRoom::find($request->showroom_id);
            }
            if ($request->has('excel')) {
                $this->trialBalanceRepository->csvDownload($data['start_date'], $data['end_date'], $data['showroom_id']);
                $filePath = public_path("uploads/csv/trial_balance_list.xlsx");
                $headers = ['Content-Type: text/xlsx'];
                $fileName = time().'-trial-balance.xlsx';
                return response()->download($filePath, $fileName, $headers);
            }
            $data['leadgers'] = $this->trialBalanceRepository->getData($data['start_date'], $data['end_date'], $data['showroom_id']);
            if ($request->has('print')) {
                \LogActivity::successLog(trans('common.Print has been Done').' - Trial Balance', route('vouchers.trial_balance'), "Print");
                return view('proaccount::trial_balance.print', $data);
            }
            return view('proaccount::trial_balance.index', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function import_view()
    {
        return view('proaccount::trial_balance.import_page');
    }

    public function import_store(Request $request)
    {
        $validate_rules = [
            'file' => 'required|mimes:csv,xls,xlsx|max:2048'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        ini_set('max_execution_time', 0);
        DB::beginTransaction();
        try {
            $this->trialBalanceRepository->csvUploadLeadger($request->except("_token"));
            \LogActivity::successLog('Trial Balance uploaded.', route('vouchers.trial_balance'), "Print");
            DB::commit();
            Toastr::success(trans('account.Uploaded successfully'));
            return back();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return $e->failures();
        }
    }
}
