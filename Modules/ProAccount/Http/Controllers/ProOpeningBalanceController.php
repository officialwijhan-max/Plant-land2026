<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ProAccount\Repositories\OpeningBalanceRepository;
use Modules\ProAccount\Repositories\VoucherRepository;
use Modules\ProAccount\Repositories\JournalRepository;
use Modules\ProAccount\Repositories\CashFLowAccountRepository;
use update\Modules\ProAccount\Repositories\FinancialYearRepository;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;

class ProOpeningBalanceController extends Controller
{
    protected $openingRepository, $journalRepository;

    public function __construct(OpeningBalanceRepository  $openingRepository, JournalRepository $journalRepository)
    {
        $this->openingRepository = $openingRepository;
        $this->journalRepository = $journalRepository;
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
                $this->openingRepository->csvDownloadExpense();
                $filePath = public_path("uploads/csv/opening-balance.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-opening-balance.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
            $data['items'] = $this->openingRepository->withPaginateExpense("all",$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
            if ($request->import_as == "print") {
                \LogActivity::successLog(trans('common.Print has been Done').' - Income List', route('income.index'), "Print");
                return view('proaccount::opening_balance.paginates.print', $data);
            }
        }

        $data['items'] = $this->openingRepository->withPaginateExpense($row_count,$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
        if ($request->ajax()) {
            return view('proaccount::opening_balance.paginates.list', $data);
        }
        return view('proaccount::opening_balance.index', $data);
    }

    public function create()
    {
        $financialRepo = new FinancialYearRepository();
        $data['financial_years'] = $financialRepo->getAll();
        return view('proaccount::opening_balance.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $type_check_for_cash_flow = [];
        $total_debit_amount = 0;
        $total_credit_amount = 0;
        $type = $request->journal_type;
        $type_for_check = null;
        $cashFlowRepo = new CashFLowAccountRepository();

        $credit_amounts[] = 0;
        $credit_account_id[] = 0;
        $credit_partner_id[] = 0;
        $credit_cash_flow_account_id[] = 0;
        $credit_narration[] = null;

        $debit_amounts[] = 0;
        $debit_account_id[] = 0;
        $debit_partner_id[] = 0;
        $debit_cash_flow_account_id[] = 0;
        $debit_narration[] = null;


        foreach ($request->debit_amount as $key => $debit_amount) {
            if ($debit_amount > 0) {
                $total_debit_amount += $debit_amount;
                $debit_amounts[] = $debit_amount;
                $debit_account_id[] = $request->account_id[$key];
                $debit_partner_id[] = $request->sub_account_id[$key];
                $debit_cash_flow_account_id[] = ($request->cash_flow_account) ? $request->cash_flow_account[$key] : 0;
                $debit_narration[] = $request->narration[$key];
            }
        }
        foreach ($request->credit_amount as $m => $credit_amount) {
            if ($credit_amount > 0) {
                $total_credit_amount += $credit_amount;
                $credit_amounts[] = $credit_amount;
                $credit_account_id[] = $request->account_id[$m];
                $credit_partner_id[] = $request->sub_account_id[$m];
                $credit_cash_flow_account_id[] = ($request->cash_flow_account) ? $request->cash_flow_account[$m] : 0;
                $credit_narration[] = $request->narration[$m];
                if (in_array($request->account_id[$m],$debit_account_id)) {
                    return response()->json(["message_warning" => trans('account.Same Account entered for debit and credit side')]);
                }
            }
        }
        $referable_id = null;
        $referable_type = null;
        $is_invoiced = 0;
        DB::beginTransaction();
        try {
            $item = $this->journalRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => ($request->is_cashflow_journal == "yes") ? 1 : 0,
                'amount'=> $total_debit_amount,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'account_type'=>$request->account_type,
                'payment_type' => 'journal_voucher',
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> ($request->narration_voucher) ? $request->narration_voucher : "Opening Balance Entry",
                'referable_type'=> $referable_type,
                'referable_id'=> $referable_id,
                'is_invoiced'=> $is_invoiced,
                'is_manual_entry'=> 1,
                'sale_or_purchase'=> 'opening',
                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => 1,
            ]);
            DB::commit();
            \LogActivity::successLog(trans("common.Successfully Added").' - '.$item->GetTypeName().'-'.$item->txn_id,route('journal.audit_history',$item->id),'Journal entry');
            if ($request->save_and_close_button == "only_save") {
                return response()->json([
                                        "message" => trans("common.Successfully Added"),
                                        "url" => route('pro-opening-balance.create')
                                    ], 200);
            }
            if ($request->save_and_close_button == "save_and_close_button") {
                return response()->json([
                    "message" => trans("common.Successfully Added"),
                    "url" => route('pro-opening-balance.index')
                ], 200);
            }
            if ($request->save_and_close_button == "save_and_edit_button") {
                return response()->json([
                    "message" => trans("common.Successfully Added"),
                    "url" => route('pro-opening-balance.edit', $item->id)
                ], 200);
            }
            return response()->json(["message" => trans('account.journal_has_been_added_successfully')]);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Journal creation');
            return response()->json(["message_error" => $e->getMessage().trans('common.Something Went Wrong')]);
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
            $data['journal'] = $this->openingRepository->find($id);
            if ($data['journal']->sale_or_purchase != "opening") {
                return back();
            }
            $financialRepo = new FinancialYearRepository();
            $data['financial_years'] = $financialRepo->getAll();
            return view('proaccount::opening_balance.edit', $data);
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
        $type_check_for_cash_flow = [];
        $total_debit_amount = 0;
        $total_credit_amount = 0;
        $type = $request->journal_type;
        $type_for_check = null;
        $cashFlowRepo = new CashFLowAccountRepository();

        $credit_amounts[] = 0;
        $credit_account_id[] = 0;
        $credit_partner_id[] = 0;
        $credit_cash_flow_account_id[] = 0;
        $credit_narration[] = null;

        $debit_amounts[] = 0;
        $debit_account_id[] = 0;
        $debit_partner_id[] = 0;
        $debit_cash_flow_account_id[] = 0;
        $debit_narration[] = null;


        foreach ($request->debit_amount as $key => $debit_amount) {
            if ($debit_amount > 0) {
                $total_debit_amount += $debit_amount;
                $debit_amounts[] = $debit_amount;
                $debit_account_id[] = $request->account_id[$key];
                $debit_partner_id[] = $request->sub_account_id[$key];
                $debit_cash_flow_account_id[] = ($request->cash_flow_account) ? $request->cash_flow_account[$key] : 0;
                $debit_narration[] = $request->narration[$key];
            }
        }
        foreach ($request->credit_amount as $m => $credit_amount) {
            if ($credit_amount > 0) {
                $total_credit_amount += $credit_amount;
                $credit_amounts[] = $credit_amount;
                $credit_account_id[] = $request->account_id[$m];
                $credit_partner_id[] = $request->sub_account_id[$m];
                $credit_cash_flow_account_id[] = ($request->cash_flow_account) ? $request->cash_flow_account[$m] : 0;
                $credit_narration[] = $request->narration[$m];
                if (in_array($request->account_id[$m],$debit_account_id)) {
                    return response()->json(["message_warning" => trans('account.Same Account entered for debit and credit side')]);
                }
            }
        }
        $referable_id = null;
        $referable_type = null;
        $is_invoiced = 0;
        DB::beginTransaction();
        $voucherRepo = new VoucherRepository();
        $voucherRepo->deleteApproved($id, "pass");
        try {
            $item = $this->journalRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount'=> $total_debit_amount,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'account_type'=>$request->account_type,
                'payment_type' => 'journal_voucher',
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> ($request->narration_voucher) ? $request->narration_voucher : "Opening Balance Entry",
                'referable_type'=> $referable_type,
                'referable_id'=> $referable_id,
                'is_invoiced'=> $is_invoiced,
                'is_manual_entry'=> 1,
                'sale_or_purchase'=> 'opening',
                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => 1,
            ]);
            DB::commit();
            \LogActivity::successLog(trans('account.Journal has been updated Successfully').' - '.$item->GetTypeName().'-'.$item->txn_id,route('journal.audit_history',$item->id),'Journal entry');
            if ($request->save_and_close_button == "only_save") {
                return response()->json([
                                        "message" => trans("common.Successfully Added"),
                                        "url" => route('pro-opening-balance.edit',$item->id)
                                    ], 200);
            }
            if ($request->save_and_close_button == "save_and_close_button") {
                return response()->json([
                    "message" => trans("common.Successfully Added"),
                    "url" => route('pro-opening-balance.index')
                ], 200);
            }
            if ($request->save_and_close_button == "save_and_new_button") {
                return response()->json([
                    "message" => trans("common.Successfully Added"),
                    "url" => route('pro-opening-balance.create')
                ], 200);
            }
            return response()->json(["message" => trans('account.journal_has_been_updated_successfully')]);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Journal creation');
            return response()->json(["message_error" => trans('common.Something Went Wrong')]);
        }
    }

    public function csv_upload_view()
    {
        abort(404);
        return view('proaccount::opening_balance.import_view');
    }

    public function bulk_expense_store(Request $request)
    {
        abort(404);
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
            $this->openingRepository->csvUpload($request->except("_token"));
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

        $row_width_leadger = "col-lg-2";
        $row_width_cash_flow = "col-lg-1";

        if (Settings('use_cash_flow_in_accounting') == 0) {
            $row_width_leadger = "col-lg-5";
        }
        if (Settings('use_cash_flow_in_accounting') == 1){
            $row_cash_flow = '<div class="'.$row_width_cash_flow.'">
                                <div class="primary_input mb-15 ">
                                    <label class="primary_input_label" for="">'.trans('account.cash_flow').'</label>
                                    <div class="cash_flow_account_upper_div">
                                        <select class="select2 mb-15 cash_flow_account" name="cash_flow_account[]">
                                            <option value="0">'.trans('account.Select one').'</option>
                                        </select>
                                    </div>
                                </div>
                            </div>';
        }
        $output = '<div class="row new_added_row">
                        <div class="'.$row_width_leadger.' upper_account_div">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">'.trans('account.select_account').' *</label>
                                <select class="select2 mb-15 account_id" name="account_id[]" data-row="'.$row_count.'" required>
                                    <option value="0">'.trans('account.Select one').'</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="primary_input mb-15 ">
                                <label class="primary_input_label" for="">'.trans('account.partner').'</label>
                                <div class="sub_account_upper_div">
                                    <select class="select2 mb-15 sub_account_id" name="sub_account_id[]" data-rows="'.$row_count.'" >
                                        <option value="0">'.trans('account.Select one').'</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        '.$row_cash_flow.'

                        <div class="col-lg-2">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for=""> '.trans('account.narration').'</label>
                                <input class="primary_input_field" name="narration[]" id="narration" type="text">
                            </div>
                        </div>
                        <div class="col-lg-1 debit_amount_div">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for=""> '.trans('account.debit').' *</label>
                                <input class="primary_input_field debit_amount" name="debit_amount[]" id="debit_amount" value="0" type="number" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-lg-1 credit_amount_div">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for=""> '.trans('account.credit').' *</label>
                                <input class="primary_input_field credit_amount" name="credit_amount[]" id="credit_amount" value="0" type="number" min="0" step="0.01">
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
}
