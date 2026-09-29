<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ProAccount\Repositories\CashFLowAccountRepository;
use Modules\ProAccount\Http\Requests\CashFLowRequest;
use Brian2694\Toastr\Facades\Toastr;

class ProCashFLowAccountController extends Controller
{
    protected $cashFlowRepository;

    public function __construct(CashFLowAccountRepository  $cashFlowRepository)
    {
        $this->cashFlowRepository = $cashFlowRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $row_count = ($request->has('row')) ? $request->row : 10 ;
        $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
        $column = ($request->has('col')) ? $request->col : null ;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
        $name = ($request->has('name')) ? $request->name : null ;
        $data['items'] = $this->cashFlowRepository->withPaginate($row_count,$quick_search,$name,$sort,$column);
        if ($request->ajax()) {
            return view('proaccount::cash_flow_account.paginates.list', $data);
        }
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $this->cashFlowRepository->withPaginate("all",$quick_search,$name,$sort,$column);
            if ($request->import_as == "print") {
                \LogActivity::successLog(trans('common.Print has been Done').' - CashFlow Account List', route('cash_flow_account.index'), "Print");
                return view('proaccount::cash_flow_account.paginates.print', $data);
            }
            if ($request->import_as == "csv") {
                \LogActivity::successLog(trans('common.CSV Download has been done').' - CashFlow Account List', route('cash_flow_account.index'), "CSV Download");
                $this->cashFlowRepository->csvDownload();
                $filePath = public_path("uploads/csv/cashflow_accountlist.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-cashflow_account_list.xlsx';
                return response()->download($filePath, $fileName, $headers);
            }
        }
        return view('proaccount::cash_flow_account.index', $data);
    }

    public function list_for_select(Request $request)
    {
        $data = $this->cashFlowRepository->cashFlowAccountForSelect($request->search);
        return response()->json($data);
    }

    public function out_list_for_select(Request $request)
    {
        $data = $this->cashFlowRepository->cashFlowAccountExpenseForSelect($request->search);
        return response()->json($data);
    }

    public function import_page()
    {
        return view('proaccount::cash_flow_account.import_page');
    }

    public function import(Request $request)
    {
        $validate_rules = [
            'file' => 'required|mimes:csv,xls,xlsx|max:2048'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        ini_set('max_execution_time', 0);
        DB::beginTransaction();
        try {
            $this->cashFlowRepository->csvUpload($request->except("_token"));
            DB::commit();
            Toastr::success(trans('account.Uploaded successfully'));
            return back();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            return $e->failures();
        }
    }

    public function export_csv()
    {
        try {
            $this->cashFlowRepository->csvDownload();
            $filePath = public_path("uploads/csv/cashflow_accountlist.xlsx");
        	$headers = ['Content-Type: text/csv'];
        	$fileName = time().'-cashflow_account_list.xlsx';

        	return response()->download($filePath, $fileName, $headers);
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CashFLowRequest $request)
    {
        try {
            $this->cashFlowRepository->create($request->except("_token"));
            \LogActivity::successLog(trans("account.cash_flow_account_added_successfully"));
            return response()->json(["message" => trans("account.cash_flow_account_added_successfully")], 200);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('proaccount::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        try{
            $row = $this->cashFlowRepository->find($id);
            return view('proaccount::cash_flow_account.edit',compact('row'));
        }catch(Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error($e->getMessage(), 'Error!!');
            return response()->json([
                'error' => $e->getMessage()
            ],503);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(CashFLowRequest $request, $id)
    {
        try {
            $this->cashFlowRepository->update($request->except("_token"), $id);
            \LogActivity::successLog(trans("account.cash_flow_account_updated_successfully"));
            return response()->json(["message" => trans("account.cash_flow_account_updated_successfully")], 200);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request)
    {
        try {
            $response = $this->cashFlowRepository->delete($request->id);
            if ($response == "done") {
                \LogActivity::successLog(trans('account.cash_flow_account_deleted_successfully'));
                return response()->json(["message" => trans('account.cash_flow_account_deleted_successfully')], 200);
            }
            if ($response == "failed") {
                return response()->json(["message" => trans('account.cash_flow_account_is_already_used')], 503);
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }
}
