<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ProAccount\Repositories\IncomeRepository;
use Modules\ProAccount\Repositories\LeadgerRepository;
use Modules\ProAccount\Repositories\JournalRepository;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;

class ProIncomeController extends Controller
{
    protected $incomeRepository;

    public function __construct(IncomeRepository  $incomeRepository)
    {
        $this->incomeRepository = $incomeRepository;
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
        if ($request->has("import_as")) {
            set_time_limit(-1);
            if ($request->import_as == "csv") {
                \LogActivity::successLog(trans('common.CSV Download has been done').' - Income List', route('income.index'), "CSV Download");
                $this->incomeRepository->csvDownloadExpense();
                $filePath = public_path("uploads/csv/income.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-income.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
            $data['items'] = $this->incomeRepository->withPaginateExpense("all",$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
            if ($request->import_as == "print") {
                \LogActivity::successLog(trans('common.Print has been Done').' - Income List', route('income.index'), "Print");
                return view('proaccount::income.paginates.print', $data);
            }
        }

        $data['items'] = $this->incomeRepository->withPaginateExpense($row_count,$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
        if ($request->ajax()) {
            return view('proaccount::income.paginates.list', $data);
        }
        $leadgerRepo = new LeadgerRepository();
        $data['accounts'] = $leadgerRepo->cashBankAccounts();
        return view('proaccount::income.index', $data);
    }

    public function create()
    {
        if (Settings('accounting_entry_system') != "single_entry") {
            return view('proaccount::income.create');
        }
        return back();
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            if (Settings('accounting_entry_system') == "single_entry") {
                $request->validate([
                    'purpose'=>'required',
                    'amount'=>'required'
                ]);
                $approval = 0;
                $debit_amounts[] = $request->amount;
                $debit_account_id[] = $request->credit_account_id;
                $debit_partner_id[] = 0;
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] = $request->purpose;

                $credit_amounts[] = $request->amount;
                $credit_account_id[] = Settings('default_income_account');
                $credit_partner_id[] = 0;
                $credit_cash_flow_account_id[] = 0;
                $credit_narration[] = $request->purpose;
            }

            if (Settings('accounting_entry_system') == "double_entry") {
                $request->validate([
                    'credit_account_id'=>'required|integer|min:1',
                    "debit_amount.*"  => "required|regex:/^\d+(\.\d{1,2})?$/|min:0",
                ]);
                $approval = 0;

                $total_amount = 0;

                foreach ($request->account_id as $key => $account) {
                    $credit_amounts[] = $request->debit_amount[$key];
                    $credit_account_id[] = $request->account_id[$key];
                    $credit_partner_id[] = $request->sub_account_id[$key];
                    $credit_cash_flow_account_id[] = 0;
                    $credit_narration[] = $request->narration[$key];
                    $total_amount += $request->debit_amount[$key];
                }

                $debit_amounts[] = $total_amount;
                $debit_account_id[] = $request->credit_account_id;
                $debit_partner_id[] = 0;
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] = "Income Entry By ". auth()->user()->name;
            }

            $approval = 1;

            $journalRecieveRepository = new JournalRepository();
            $item = $journalRecieveRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount'=> ($request->amount) ? $request->amount : $total_amount,
                'date'=> (Settings('accounting_entry_system') == "double_entry") ? Carbon::parse($request->date)->format('Y-m-d') :Carbon::now(),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> ($request->purpose) ? $request->purpose : "Income Entry By ". auth()->user()->name,
                'referable_type'=> null,
                'referable_id'=> null,
                'is_invoiced'=> 0,
                'is_advanced'=> 0,
                'is_manual_entry'=> 1,

                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => (Settings('accounting_entry_system') == "single_entry") ? 1 : $approval,
                'sale_or_purchase' => "inc",
                'ref_no' => null,
                'is_sale_purchase' => 0,
                'direct_manupulate' => 0,
            ]);
            DB::commit();
            \LogActivity::successLog(trans("common.Successfully Added").' - '.$item->GetTypeName().'-'.$item->txn_id,route('journal.audit_history',$item->id),'Income entry');
            if ($request->save_and_close_button == "only_save") {
                return response()->json([
                                        "message" => trans("common.Successfully Added"),
                                        "url" => route('income.create')
                                    ], 200);
            }
            if ($request->save_and_close_button == "save_and_close_button") {
                return response()->json([
                    "message" => trans("common.Successfully Added"),
                    "url" => route('income.index')
                ], 200);
            }
            if ($request->save_and_close_button == "save_and_edit_button") {
                return response()->json([
                    "message" => trans("common.Successfully Added"),
                    "url" => route('income.edit', $item->id)
                ], 200);
            }
            return response()->json(["message" => trans("common.Successfully Added")], 200);
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

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        try{
            $data['row'] = $this->incomeRepository->find($id);
            if ($data['row']->is_approve == 1) {
                return back();
            }
            $leadgerRepo = new LeadgerRepository();
            $data['accounts'] = $leadgerRepo->cashBankAccounts();
            if (Settings('accounting_entry_system') != "single_entry") {
                return view('proaccount::income.edit', $data);
            }else {
                return view('proaccount::income.edit_single_entry', $data);
            }
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
    public function update(Request $request, $id)
    {
        try {
            if (Settings('accounting_entry_system') == "single_entry") {
                DB::beginTransaction();
                $request->validate([
                    'purpose'=>'required',
                    'amount'=>'required'
                ]);
                $approval = 0;
                $debit_amounts[] = $request->amount;
                $debit_account_id[] = $request->credit_account_id;
                $debit_partner_id[] = 0;
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] = $request->purpose;

                $credit_amounts[] = $request->amount;
                $credit_account_id[] = Settings('default_income_account');
                $credit_partner_id[] = 0;
                $credit_cash_flow_account_id[] = 0;
                $credit_narration[] = $request->purpose;
            }

            if (Settings('accounting_entry_system') == "double_entry") {
                $request->validate([
                    'credit_account_id'=>'required|integer|min:1',
                    "debit_amount.*"  => "required|regex:/^\d+(\.\d{1,2})?$/|min:0",
                ]);
                $approval = 0;

                $total_amount = 0;

                foreach ($request->account_id as $key => $account) {
                    $credit_amounts[] = $request->debit_amount[$key];
                    $credit_account_id[] = $request->account_id[$key];
                    $credit_partner_id[] = $request->sub_account_id[$key];
                    $credit_cash_flow_account_id[] = 0;
                    $credit_narration[] = $request->narration[$key];
                    $total_amount += $request->debit_amount[$key];
                }

                $debit_amounts[] = $total_amount;
                $debit_account_id[] = $request->credit_account_id;
                $debit_partner_id[] = 0;
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] = "Income Entry Updated By ". auth()->user()->name;
            }


            $approval = 1;

            $journalRecieveRepository = new JournalRepository();
            $item = $journalRecieveRepository->update([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount'=> ($request->amount) ? $request->amount : $total_amount,
                'date'=> (Settings('accounting_entry_system') == "double_entry") ? Carbon::parse($request->date)->format('Y-m-d') :Carbon::now(),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> ($request->purpose) ? $request->purpose : "Income Entry By ". auth()->user()->name,
                'referable_type'=> null,
                'referable_id'=> null,
                'is_invoiced'=> 0,
                'is_advanced'=> 0,
                'is_manual_entry'=> 1,

                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => (Settings('accounting_entry_system') == "single_entry") ? 1 : $approval,
                'sale_or_purchase' => "inc",
                'ref_no' => null,
                'is_sale_purchase' => 0,
                'direct_manupulate' => 0,
            ], $id);
            DB::commit();
            \LogActivity::successLog(trans("common.Successfully Updated").' - '.$item->GetTypeName().'-'.$item->txn_id,route('journal.audit_history',$item->id),'Income Update');
            if ($request->save_and_close_button == "only_save") {
                return response()->json([
                                        "message" => trans("common.Successfully Added"),
                                        "url" => route('income.edit', $item->id)
                                    ], 200);
            }
            if ($request->save_and_close_button == "save_and_close_button") {
                return response()->json([
                    "message" => trans("common.Successfully Added"),
                    "url" => route('income.index')
                ], 200);
            }
            if ($request->save_and_close_button == "save_and_new_button") {
                return response()->json([
                    "message" => trans("common.Successfully Added"),
                    "url" => route('income.create')
                ], 200);
            }
            return response()->json(["message" => trans("common.Successfully Updated")], 200);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function csv_upload_view()
    {
        return view('proaccount::income.import_view');
    }

    public function bulk_expense_store(Request $request)
    {
        $validate_rules = [
            'file' => 'required|mimes:csv,xls,xlsx|max:2048'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        if (Settings('default_income_account') == 0 || Settings('default_income_account') == Null) {
            Toastr::warning(trans('account.set_default_income_account_first_from_account_configuration'));
            return back();
        }
        ini_set('max_execution_time', 0);
        try {
            DB::beginTransaction();
            $this->incomeRepository->csvUpload($request->except("_token"));
            DB::commit();
            \LogActivity::successLog('Bulk Income Uploaded', route('income.index'), "Bulk Income");
            Toastr::success(trans('account.Uploaded successfully'));
            return back();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return $e->failures();
        }
    }

    public function add_new_line()
    {
        $row_cash_flow = '';
        $output = '';

        $row_count = request()->get('row', 2);

        $output = '<div class="row new_added_row">
                        <div class="col-lg-3 upper_account_div">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">'.trans('account.select_account').' *</label>
                                <select class="select2 mb-15 account_id" data-row="'.$row_count.'" name="account_id[]" required>
                                    <option value="0">'.trans('account.Select one').'</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for=""> '.trans('account.narration').'</label>
                                <input class="primary_input_field" name="narration[]" id="narration" type="text">
                            </div>
                        </div>
                        <div class="col-lg-2 debit_amount_div">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for=""> '.trans('account.amount').' *</label>
                                <input class="primary_input_field debit_amount" name="debit_amount[]" id="debit_amount" value="0" type="number" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-lg-3 upper_account_div">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">'.trans('account.select_account').' *</label>
                                <select class="select2 mb-15 sub_account_id" data-rows="'.$row_count.'" name="sub_account_id[]" required>
                                    <option value="0">'.trans('account.Select one').'</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="primary_input mb-15 action_div">
                                <label class="primary_input_label" for=""> '.trans('common.Action').' </label>
                                <a class="primary-btn btn-sm delete_new_row"><i class="fas fa-trash-alt required_mark2 f_s_13"></i></a>
                            </div>
                        </div>
                    </div>';
        return response()->json($output);
    }

    public function bulk_income_store(Request $request)
    {
        $validate_rules = [
            'file' => 'required|mimes:csv,xls,xlsx|max:2048'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        if (Settings('default_income_account') == 0 || Settings('default_income_account') == Null) {
            return response()->json(['message' => 'Please set default income account first'], 500);
        }
        ini_set('max_execution_time', 0);
        try {
            DB::beginTransaction();
            $this->incomeRepository->csvUpload($request->except("_token"));
            DB::commit();
            return back();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            return response()->json(['message' => $e->failures()], 500);
        }
    }
}
