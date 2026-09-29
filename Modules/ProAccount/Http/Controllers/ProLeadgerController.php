<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ProAccount\Repositories\LeadgerRepository;
use Modules\ProAccount\Repositories\SubLeadgerRepository;
use Modules\ProAccount\Http\Requests\CreateLeadgerRequest;
use Modules\ProAccount\Http\Requests\UpdateLeadgerRequest;
use Brian2694\Toastr\Facades\Toastr;
use Schema;

class ProLeadgerController extends Controller
{
    protected $leadgerRepository;

    public function __construct(LeadgerRepository  $leadgerRepository)
    {
        $this->leadgerRepository = $leadgerRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $data['ChartOfAccountList'] = $this->leadgerRepository->parentNullAccountList(['childrenCategories:id,name,code,is_cost_center,parent_id,is_active,type,is_blocked','transactions:id,leadger_id,is_approve,amount,type,accounting_period_id'],["id","name","code","is_cost_center","parent_id","is_active","type","is_blocked"]);
        if ($request->ajax()) {
            return view('proaccount::leadger_accounts.components.ledger_list', $data);
        }
        return view('proaccount::leadger_accounts.index', $data);
    }

    public function expense_leadger_list(Request $request)
    {
        try{
            $data = $this->leadgerRepository->getActiveExpenseLeadgerByAjax($request->search);;
            return response()->json($data);
        }catch(Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error($e->getMessage(), 'Error!!');
            return response()->json([
                'error' => $e->getMessage()
            ],503);
        }
    }

    public function income_leadger_list(Request $request)
    {
        try{
            $data = $this->leadgerRepository->getActiveIncomeLeadgerByAjax($request->search);;
            return response()->json($data);
        }catch(Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error($e->getMessage(), 'Error!!');
            return response()->json([
                'error' => $e->getMessage()
            ],503);
        }
    }

    public function list_for_select(Request $request)
    {
        $data = $this->leadgerRepository->leadgerForSelect($request->search);
        return response()->json($data);
    }

    public function cash_bank_account_select(Request $request)
    {
        $data = $this->leadgerRepository->listForSelectAccountCashBank($request);
        return response()->json($data);
    }

    public function import_page()
    {
        return view('proaccount::leadger_accounts.import_page');
    }

    public function import(Request $request)
    {
        $validate_rules = [
            'file' => 'required|mimes:csv,xls,xlsx|max:2048'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        ini_set('max_execution_time', 0);
        try {
            DB::beginTransaction();
            $this->leadgerRepository->csvUploadLeadger($request->except("_token"));
            DB::commit();
            Toastr::success(trans('account.Uploaded successfully'));
            return back();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return $e->failures();
        }
    }

    public function export_csv()
    {
        try {
            $this->leadgerRepository->csvDownloadLeadgers();
            $filePath = public_path("uploads/csv/leadger_accountlist.xlsx");
        	$headers = ['Content-Type: text/csv'];
        	$fileName = time().'-leadger_account_list.xlsx';

        	return response()->download($filePath, $fileName, $headers);
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function list()
    {
        $data['ChartOfAccountList'] = $this->leadgerRepository->parentNullAccountList();
        return view('proaccount::leadger_accounts.page_component.ledger_list', $data);
    }

    public function list_all()
    {
        return $this->leadgerRepository->getAll();
    }

    public function parent_category()
    {
        return $this->leadgerRepository->parent_category();
    }

    public function cost_center()
    {
        return $this->leadgerRepository->cost_center();
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CreateLeadgerRequest $request)
    {
        try {
            DB::beginTransaction();
            $item = $this->leadgerRepository->create($request->except("_token"));
            DB::commit();
            \LogActivity::successLog(trans('account.Ledger Added Successfully'), null, $item->name.'('.$item->code.')');
            return response()->json(["message" => trans('account.Ledger Added Successfully'), "leadger" => $item , "row_count" => $request->row_count, "class_name" => $request->class_name], 200);
        } catch (\Exception $e) {
            DB::rollBack();
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

    public function edit($id)
    {
        try {
            $data['leadger'] = $this->leadgerRepository->find($id);
            return view('proaccount::leadger_accounts.edit', $data);
        } catch (\Exception $e) {
            return $e->getMessage();
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
            $response = $this->leadgerRepository->delete($request->id);
            if ($response == "done") {
                \LogActivity::successLog("An Account deleted Successfully");
                return response()->json(["message" => trans('account.leadger_deleted_successfully')], 200);
            }
            if ($response == "failed") {
                return response()->json(["message" => trans('account.leadger_is_already_used')], 503);
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function rename_account(UpdateLeadgerRequest $request)
    {
        try {
            $response = $this->leadgerRepository->rename_account($request->except("_token"));
            \LogActivity::successLog( trans('account.leadger_updated_successfully'), null, $request->name);
            if ($response == "can not update") {
                Toastr::warning(trans('account.you_have_to_put_same_type_parent_account_as_transaction_exist_for_current_account'));
            }
            if ($request->ajax()) {
                return response()->json(["message" => trans('account.leadger_updated_successfully')], 200);
            } else {
                return back();
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function get_leadger_for_select(Request $request)
    {
        try{
            $data = $this->leadgerRepository->getLeadgerByAjax($request->search, $request->type);
            return response()->json($data);
        }catch(Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error($e->getMessage(), 'Error!!');
            return response()->json([
                'error' => $e->getMessage()
            ],503);
        }
    }

    public function get_table_row_data(Request $request)
    {
        $subleadger_name = "x";
        $subleadger_id = 0;
        $project_id = 0;
        $project_name = "x";

        if ($request->sub_leadger_id != 0) {
            $subleadgerRepository = new SubLeadgerRepository();
            $subleadger = $subleadgerRepository->find($request->sub_leadger_id);
            $subleadger_name = $subleadger->name;
            $subleadger_id = $subleadger->id;
        }


        $leadger = $this->leadgerRepository->find($request->leadger_id);
        $leadger_id = $leadger->id;
        $amount = $request->amount;
        $output = '';

        $output .= '<tr class="below_div_select">
                            <td class="text-center">'.$leadger->name.'<input class="primary_input_field debit_account_id" name="debit_account_id[]" id="debit_account_id" value="'.$leadger_id.'" placeholder="Amount" type="hidden"></td>
                            <td class="text-center">'.$subleadger_name.'<input class="primary_input_field debit_sub_account_id" name="debit_sub_account_id[]" id="debit_sub_account_id" value="'.$subleadger_id.'" placeholder="Amount" type="hidden"></td>
                            <td class="text-right">'.$amount.'<input class="primary_input_field sub_amount" name="sub_amount[]" id="sub_amount" value="'.$amount.'" placeholder="Amount" type="hidden"></td>
                            <td class="nowrap text-center"><a class="primary-btn btn-sm delete_item_row"><i class="fas fa-trash-alt required_mark2 f_s_13"></i></a></td>
                        </tr>';

        return response()->json($output);
    }
}
