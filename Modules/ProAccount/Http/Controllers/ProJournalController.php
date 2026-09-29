<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ProAccount\Http\Requests\CreateJournalVoucherRequest;
use Modules\ProAccount\Repositories\CashFLowAccountRepository;
use Modules\ProAccount\Repositories\JournalRepository;
use Carbon\Carbon;

class ProJournalController extends Controller
{
    protected $journalRepository;

    public function __construct(JournalRepository $journalRepository)
    {
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

        $data['items'] = $this->journalRepository->withPaginateJournalVoucher($row_count,$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
        if ($request->ajax()) {
            return view('proaccount::journal_vouchers.paginates.list', $data);
        }
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $this->journalRepository->withPaginateJournalVoucher("all",$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
            if ($request->import_as == "print") {
                \LogActivity::successLog(trans('common.Print has been Done').' - journal List', route('journal.index'), "Print");
                return view('proaccount::journal_vouchers.paginates.print', $data);
            }
            if ($request->import_as == "csv") {
                $this->journalRepository->csvDownloadJournalVoucher();
                \LogActivity::successLog(trans('common.CSV Download has been done').' - journal List', route('journal.index'), "CSV Download");
                $filePath = public_path("uploads/csv/journal_vouchers.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-journal_vouchers.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        }
        return view('proaccount::journal_vouchers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Settings('accounting_entry_system') != "single_entry") {
            return view('proaccount::journal_vouchers.create');
        }else {
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CreateJournalVoucherRequest $request)
    {
        if ($request->cash_flow_account && $request->is_cashflow_journal == "yes") {
            $accurate_account_cashflow = 1;
            foreach ($request->cash_flow_account as $cashflow) {
                if ($cashflow > 0) {
                    $accurate_account_cashflow = 0;
                }
            }
            if ($accurate_account_cashflow == 1) {
                return response()->json(["message_warning" => trans("account.cash_flow_account_was_not_for_every_line")]);
            }
        }
        $same_debit_credit = 0;

        $type_check_for_cash_flow = [];
        $total_debit_amount = 0;
        $total_credit_amount = 0;
        $type = $request->journal_type;
        $type_for_check = null;
        $cashFlowRepo = new CashFLowAccountRepository();
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
        if ($request->cash_flow_account) {
            foreach ($request->cash_flow_account as $k => $flow_account) {
                if ($flow_account != 0) {
                    $cashFlowAccount = $cashFlowRepo->find($flow_account);
                    $type_for_check = $cashFlowAccount->type;
                    if (!in_array($type_for_check, $type_check_for_cash_flow)) {
                        array_push($type_check_for_cash_flow, $type_for_check);
                    }
                }
            }
            if (count($type_check_for_cash_flow) > 1) {
                return response()->json(["message_warning" => trnas("proaccount::account.cash_flow_account_must_be_same_type_for_an_entry")]);
            }
        }

        if ($request->invoice_payment != null) {
            if ($request->invoice_payment == "contract") {
                $referable_id = $request->contract_invoice;
                $referable_type = "Modules\Customer\Entities\ContractInvoice";
            }
            if ($request->invoice_payment == "service") {
                $referable_id = $request->service_invoice;
                $referable_type = "Modules\Customer\Entities\Invoice";
            }
            $is_invoiced = 1;
        }else {
            $referable_id = null;
            $referable_type = null;
            $is_invoiced = 0;
        }
        if (number_format($total_debit_amount, 2) == number_format($total_credit_amount, 2)) {
            DB::beginTransaction();
            try {
                $item = $this->journalRepository->create([
                    'type' => $type,
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
                    'narration_voucher'=> $request->narration_voucher,
                    'referable_type'=> $referable_type,
                    'referable_id'=> $referable_id,
                    'is_invoiced'=> $is_invoiced,
                    'is_manual_entry'=> 1,

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
                                            "url" => route('journal.create')
                                        ], 200);
                }
                if ($request->save_and_close_button == "save_and_close_button") {
                    return response()->json([
                        "message" => trans("common.Successfully Added"),
                        "url" => route('journal.index')
                    ], 200);
                }
                if ($request->save_and_close_button == "save_and_edit_button") {
                    return response()->json([
                        "message" => trans("common.Successfully Added"),
                        "url" => route('journal.edit', $item->id)
                    ], 200);
                }
                return response()->json(["message" => trans('account.journal_has_been_added_successfully')]);
            } catch (\Exception $e) {
                DB::rollBack();
                \LogActivity::errorLog($e->getMessage().' - Error has been detected for Journal creation');
                return response()->json(["message_error" => $e->getMessage().trans('common.Something Went Wrong')]);
            }
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

    public function audit_history($id)
    {
        try {
            $data['journal'] = $this->journalRepository->find($id);
            return view('proaccount::journal_vouchers.audit_history', $data);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message_error" => $e->getMessage().trans('common.Something Went Wrong')]);
        }
    }

    public function audit_history_print($id)
    {
        try {
            $data['journal'] = $this->journalRepository->find($id);
            \LogActivity::successLog(trans('common.Print has been Done').' - Audit History', route('journal.audit_history_print',$data['journal']->id), "Print");
            return view('proaccount::journal_vouchers.audit_history_print', $data);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message_error" => $e->getMessage().trans('common.Something Went Wrong')]);
        }
    }

    public function transaction_detail($id)
    {
        try {
            $data['journal'] = $this->journalRepository->find($id);
            return view('proaccount::journal_vouchers.journal_transaction', $data);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message_error" => $e->getMessage().trans('common.Something Went Wrong')]);
        }
    }

    public function transaction_detail_print($id)
    {
        try {
            $data['journal'] = $this->journalRepository->find($id);
            \LogActivity::successLog(trans('common.Print has been Done').' - Transaction Journal', route('journal.transaction_detail_print',$data['journal']->id), "Print");
            return view('proaccount::journal_vouchers.journal_transaction_print', $data);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message_error" => $e->getMessage().trans('common.Something Went Wrong')]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data['journal'] = $this->journalRepository->find($id);
        if ($data['journal']->is_approve != 1) {
            if ($data['journal']->type == "rec_cash" || $data['journal']->type == "rec_bank") {
                return view('proaccount::voucher_recieves.edit', $data);
            }
            if ($data['journal']->type == "pay_cash" || $data['journal']->type == "pay_bank") {
                return view('proaccount::voucher_payments.edit', $data);
            }
            if ($data['journal']->sale_or_purchase == "exp") {
                return redirect()->route('expenses.index');
            }
            return view('proaccount::journal_vouchers.edit', $data);
        }
        return back();
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(CreateJournalVoucherRequest $request, $id)
    {
        if ($request->cash_flow_account && $request->is_cashflow_journal == "yes") {
            $accurate_account_cashflow = 1;
            foreach ($request->cash_flow_account as $cashflow) {
                if ($cashflow > 0) {
                    $accurate_account_cashflow = 0;
                }
            }
            if ($accurate_account_cashflow == 1) {
                return response()->json(["message_warning" => trans("account.cash_flow_account_was_not_for_every_line")]);
            }
        }
        $type_check_for_cash_flow = [];
        $total_debit_amount = 0;
        $total_credit_amount = 0;
        $type = $request->journal_type;
        $type_for_check = null;
        $cashFlowRepo = new CashFLowAccountRepository();
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
            }
        }

        if ($request->cash_flow_account) {
            foreach ($request->cash_flow_account as $k => $flow_account) {
                if ($flow_account != 0) {
                    $cashFlowAccount = $cashFlowRepo->find($flow_account);
                    $type_for_check = $cashFlowAccount->type;
                    if (!in_array($type_for_check, $type_check_for_cash_flow)) {
                        array_push($type_check_for_cash_flow, $type_for_check);
                    }
                }
            }
            if (count($type_check_for_cash_flow) > 1) {
                return response()->json(["message_warning" => trans("account.cash_flow_account_must_be_same_type_for_an_entry")]);
            }
        }

        if ($request->invoice_payment != null) {
            if ($request->invoice_payment == "contract") {
                $referable_id = $request->contract_invoice;
                $referable_type = "Modules\Customer\Entities\ContractInvoice";
            }
            if ($request->invoice_payment == "service") {
                $referable_id = $request->service_invoice;
                $referable_type = "Modules\Customer\Entities\Invoice";
            }
            $is_invoiced = 1;
        }else {
            $referable_id = null;
            $referable_type = null;
            $is_invoiced = 0;
        }
        if (number_format($total_debit_amount, 2) == number_format($total_credit_amount, 2)) {
            DB::beginTransaction();
            try {
                $item = $this->journalRepository->update([
                    'type' => $type,
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
                    'narration_voucher'=> $request->credit_narration,
                    'referable_type'=> $referable_type,
                    'referable_id'=> $referable_id,
                    'is_invoiced'=> $is_invoiced,

                    'debit_account_id'=> $debit_account_id,
                    'debit_sub_account_id'=> $debit_partner_id,
                    'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                    'debit_account_amount'=> $debit_amounts,
                    'debit_narration'=> $debit_narration,
                    'is_approve' => 1,
                ], $id);
                DB::commit();
                \LogActivity::successLog(trans('account.Journal has been updated Successfully').' - '.$item->GetTypeName().'-'.$item->txn_id,route('journal.audit_history',$item->id),'Journal entry');
                if ($request->save_and_close_button == "only_save") {
                    return response()->json([
                                            "message" => trans("common.Successfully Added"),
                                            "url" => route('journal.edit', $item->id)
                                        ], 200);
                }
                if ($request->save_and_close_button == "save_and_close_button") {
                    return response()->json([
                        "message" => trans("common.Successfully Added"),
                        "url" => route('journal.index')
                    ], 200);
                }
                if ($request->save_and_close_button == "save_and_new_button") {
                    return response()->json([
                        "message" => trans("common.Successfully Added"),
                        "url" => route('journal.create')
                    ], 200);
                }
                return response()->json(["message" => trans('account.journal_has_been_updated_successfully')]);
            } catch (\Exception $e) {
                DB::rollBack();
                \LogActivity::errorLog($e->getMessage().' - Error has been detected for Journal creation');
                return response()->json(["message_error" => trans('common.Something Went Wrong')]);
            }
        }
        else {
            \LogActivity::errorLog('Error has been detected for Journal creation for Mismatch Of Debit Credit');
            return response()->json(["message_error" => trans('common.Something Went Wrong')]);
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
