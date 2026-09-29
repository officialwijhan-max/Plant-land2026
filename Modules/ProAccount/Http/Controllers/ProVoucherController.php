<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\ProAccount\Http\Requests\CreatePaymentVoucherRequest;
use Modules\ProAccount\Repositories\VoucherRepository;
use Modules\ProAccount\Repositories\SubLeadgerRepository;
use Modules\ProAccount\Repositories\LeadgerRepository;
use Modules\ProAccount\Repositories\JournalRepository;
use Modules\Purchases\Repositories\PurchaseOrderRepository;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;

class ProVoucherController extends Controller
{
    protected $voucherRepository;

    public function __construct(VoucherRepository  $voucherRepository)
    {
        $this->voucherRepository = $voucherRepository;
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
            $data['items'] = $this->voucherRepository->withPaginatePayVoucher("all",$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
            if ($request->import_as == "print") {
                \LogActivity::successLog(trans('common.Print has been Done').' - Voucher Payment', route('vouchers.index'), "Print");
                return view('proaccount::voucher_payments.paginates.print', $data);
            }
            if ($request->import_as == "csv") {
                $this->voucherRepository->csvDownloadPayVoucher();
                \LogActivity::successLog(trans('common.CSV Download has been done').' - Voucher Payment', route('vouchers.index'), "CSV Download");
                $filePath = public_path("uploads/csv/voucher_payments.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-voucher_payments.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        }

        $data['items'] = $this->voucherRepository->withPaginatePayVoucher($row_count,$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
        if ($request->ajax()) {
            return view('proaccount::voucher_payments.paginates.list', $data);
        }
        return view('proaccount::voucher_payments.index', $data);
    }

    public function approval_index(Request $request)
    {
        $row_count = ($request->has('row')) ? $request->row : 10 ;
        $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
        $column = ($request->has('col')) ? $request->col : null ;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $this->voucherRepository->withPaginatePendingVoucher("all",$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
            if ($request->import_as == "print") {
                \LogActivity::successLog(trans('common.Print has been Done').' - Pending Voucher', route('vouchers.index'), "Print");
                return view('proaccount::voucher_approvals.paginates.print', $data);
            }
            if ($request->import_as == "csv") {
                $this->voucherRepository->csvDownPendingVoucher();
                \LogActivity::successLog(trans('common.CSV Download has been done').' - Pending Voucher', route('vouchers.index'), "CSV Download");
                $filePath = public_path("uploads/csv/pending-vouchers.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-pending-vouchers.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        }

        $data['items'] = $this->voucherRepository->withPaginatePendingVoucher($row_count,$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
        if ($request->ajax()) {
            return view('proaccount::voucher_approvals.paginates.list', $data);
        }
        return view('proaccount::voucher_approvals.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('proaccount::voucher_payments.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CreatePaymentVoucherRequest $request)
    {
        DB::beginTransaction();
        try {
            $type_check_for_cash_flow = [];
            $total_debit_amount = 0;
            $total_credit_amount = 0;
            $approval = 0;
            $type = $request->journal_type;
            $type_for_check = null;

            if ($request->due_invoice_list != 0) {
                $saleRepo = new PurchaseOrderRepository();
                $sale = $saleRepo->find($request->due_invoice_list);
                $referable_id = $sale->id;
                $referable_type = get_class($sale);
                $is_invoiced = 1;
                $is_advanced = 0;
            }
            else {
                $referable_id = null;
                $referable_type = null;
                $is_invoiced = 0;
                $is_advanced = 0;
            }

            $subLeadgerRepo = new SubLeadgerRepository();
            $leadger_id = $subLeadgerRepo->find($request->credit_sub_account_id)->leadger_id;

            $LeadgerRepo = new LeadgerRepository();
            $leadger = $LeadgerRepo->find($request->debit_account_id);

            $total_amount_voucher = ($is_invoiced == 1) ? $request->sub_amount[0] + $request->discount_amount : $request->sub_amount[0];

            $debit_amounts[] = $total_amount_voucher;
            $debit_account_id[] = $leadger_id;
            $debit_partner_id[] = $request->credit_sub_account_id;
            $debit_cash_flow_account_id[] = 0;
            $debit_narration[] = $request->narration;

            $credit_amounts[] = $request->sub_amount[0];
            $credit_account_id[] = $request->debit_account_id;
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = ($request->cash_flow_account) ? $request->cash_flow_account[0] : 0;
            $credit_narration[] = $request->narration;

            if ($is_invoiced == 1) {
                $credit_amounts[] = $request->discount_amount;
                $credit_account_id[] = Settings('purchase_discount_recieve_at_payment_time');
                $credit_partner_id[] = 0;
                $credit_cash_flow_account_id[] = 0;
                $credit_narration[] = "Payment Discount on Purchase Invoice";
            }

            $approval = 1;

            $journalRecieveRepository = new JournalRepository();
            $voucher = $journalRecieveRepository->create([
                'type' => $leadger->acc_type == "bank" ? "pay_bank" : "pay_cash",
                'is_cash_flow_journal' => ($request->cash_flow_account && $request->cash_flow_account[0] != 0) ? 1 : 0,
                'amount'=> $total_amount_voucher,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> $request->narration,
                'referable_type'=> $referable_type,
                'referable_id'=> $referable_id,
                'is_invoiced'=> $is_invoiced,
                'is_manual_entry'=> 1,

                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => $approval,
                'discount_percentage' => $request->discount_percentage,
                'discount_amount' => $request->discount_amount,
            ]);
            DB::commit();
            \LogActivity::successLog(trans("common.Successfully Added").' - '.$voucher->GetTypeName().'-'.$voucher->txn_id,route('journal.audit_history',$voucher->id),'Voucher Payment entry');
            Toastr::success(trans('account.voucher_has_been_added'));
            if ($request->save_and_close_button == "only_save") {
                return back();
            }
            if ($request->save_and_close_button == "save_and_close_button") {
                return redirect()->route('vouchers.index');
            }
            if ($request->save_and_close_button == "save_and_edit_button") {
                return redirect()->route('journal.edit',$voucher->id);
            }
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        try {
            $voucher = $this->voucherRepository->voucherDetails($id);
            if (Settings('accounting_entry_system') == "single_entry") {
                return view('proaccount::voucher_approvals.single_voucher_details', [
                    "voucher" => $voucher
                ]);
            }else {
                return view('proaccount::voucher_approvals.voucher_details', [
                    "voucher" => $voucher
                ]);
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data['payment'] = $this->voucherRepository->find($id);
        if ($data['payment']->is_approve == 1) {
            return back();
        }
        return view('proaccount::voucher_payments.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(CreatePaymentVoucherRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $type_check_for_cash_flow = [];
            $total_debit_amount = 0;
            $total_credit_amount = 0;
            $approval = 0;
            $type = $request->journal_type;
            $type_for_check = null;

            if ($request->due_invoice_list != 0) {
                $saleRepo = new PurchaseOrderRepository();
                $sale = $saleRepo->find($request->due_invoice_list);
                $referable_id = $sale->id;
                $referable_type = get_class($sale);
                $is_invoiced = 1;
                $is_advanced = 0;
            }
            else {
                $referable_id = null;
                $referable_type = null;
                $is_invoiced = 0;
                $is_advanced = 0;
            }

            $subLeadgerRepo = new SubLeadgerRepository();
            $leadger_id = $subLeadgerRepo->find($request->credit_sub_account_id)->leadger_id;

            $total_amount_voucher = ($is_invoiced == 1) ? $request->sub_amount[0] + $request->discount_amount : $request->sub_amount[0];

            $LeadgerRepo = new LeadgerRepository();
            $leadger = $LeadgerRepo->find($request->debit_account_id);

            $debit_amounts[] = $total_amount_voucher;
            $debit_account_id[] = $leadger_id;
            $debit_partner_id[] = $request->credit_sub_account_id;
            $debit_cash_flow_account_id[] = 0;
            $debit_narration[] = $request->narration;

            $credit_amounts[] = $request->sub_amount[0];
            $credit_account_id[] = $request->debit_account_id;
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = ($request->cash_flow_account) ? $request->cash_flow_account[0] : 0;
            $credit_narration[] = $request->narration;

            if ($is_invoiced == 1) {
                $credit_amounts[] = $request->discount_amount;
                $credit_account_id[] = Settings('purchase_discount_recieve_at_payment_time');
                $credit_partner_id[] = 0;
                $credit_cash_flow_account_id[] = 0;
                $credit_narration[] = "Payment Discount on Purchase Invoice";
            }

            $approval = 1;

            $journalRecieveRepository = new JournalRepository();
            $voucher = $journalRecieveRepository->update([
                'type' => $leadger->acc_type == "bank" ? "pay_bank" : "pay_cash",
                'is_cash_flow_journal' => ($request->cash_flow_account && $request->cash_flow_account[0] != 0) ? 1 : 0,
                'amount'=> $total_amount_voucher,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> $request->narration,
                'referable_type'=> $referable_type,
                'referable_id'=> $referable_id,
                'is_invoiced'=> $is_invoiced,

                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => $approval,
                'discount_percentage' => ($request->discount_percentage) ? $request->discount_percentage : 0,
                'discount_amount' => ($request->discount_amount) ? $request->discount_amount : 0,
            ], $id);
            DB::commit();
            \LogActivity::successLog(trans("common.Successfully Updated").' - '.$voucher->GetTypeName().'-'.$voucher->txn_id,route('journal.audit_history',$voucher->id),'Voucher Payment Updated');
            Toastr::success(trans('account.voucher_has_been_updated'));
            if ($request->save_and_close_button == "only_save") {
                return back();
            }
            if ($request->save_and_close_button == "save_and_close_button") {
                return redirect()->route('vouchers.index');
            }
            if ($request->save_and_close_button == "save_and_new_button") {
                return redirect()->route('vouchers.create');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function print($id)
    {
        $voucher = $this->voucherRepository->voucherDetails($id);
        \LogActivity::successLog('Print - '.$voucher->GetTypeName().'-'.$voucher->txn_id,route('journal.audit_history',$voucher->id),'Print');
        return view('proaccount::journal_vouchers.print', [
            "voucher" => $voucher
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->voucherRepository->delete($request->id);
            DB::commit();
            \LogActivity::successLog(trans('account.voucher_has_been_destroyed'));
            return response()->json(["message" => trans('account.voucher_has_been_destroyed')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function destroy_approved(Request $request)
    {
        try {
            if (Hash::check($request->password, auth()->user()->password)) {
                DB::beginTransaction();
                $response = $this->voucherRepository->deleteApproved($request->id, $request->password);
                if ($response == "success") {
                    DB::commit();
                    \LogActivity::successLog(trans('account.voucher_has_been_destroyed'));
                    return response()->json(["message" => trans('account.voucher_has_been_destroyed')], 200);
                }else {
                    DB::rollBack();
                    \LogActivity::successLog(trans('account.password_did_not_match'));
                    return response()->json(["message" => trans('account.password_did_not_match')], 200);
                }
            }else {
                \LogActivity::successLog(trans('account.password_did_not_match'));
                return response()->json(["message" => trans('account.password_did_not_match')], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong')], 503);
        }
    }

    public function get_accounts($type)
    {
        return $this->voucherRepository->getAccountByCashBankOthers($type);
    }

    public function get_accounts_with_type(Request $request)
    {
        $account = $this->voucherRepository->getAccountByCashBankOthers($request->id);
        if ($request->transfer == 1) {
            return view('proaccount::transfers.accounts', [
                "account_list" => $account
            ]);
        }
        else {
            return view('proaccount::voucher_payments.accounts', [
                "account_list" => $account
            ]);
        }
    }

    public function approval_status(Request $request)
    {
        try {
            DB::beginTransaction();
            $voucher = $this->voucherRepository->status_approval($request->except("_token"));
            DB::commit();
            \LogActivity::successLog(trans('account.voucher_approval_has_been_updated'));
            return response()->json(["message" => trans('account.voucher_approval_has_been_updated')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function allApproval(Request $request)
    {
        DB::beginTransaction();
        try {
            if($request->voucher_ids == null){
                Toastr::error(trans('account.select_unapproved_voucher_please'));
                return back();
            }
            $this->voucherRepository->allApproved($request->all());
            DB::commit();
            \LogActivity::successLog(trans('account.vouchers_has_been_approved_successfully'));
            Toastr::success(trans('account.vouchers_has_been_approved_successfully'));
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function approved_voucher(Request $request)
    {
        $row_count = ($request->has('row')) ? $request->row : 10 ;
        $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
        $column = ($request->has('col')) ? $request->col : null ;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;

        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $this->voucherRepository->withPaginateApprovedVoucher("all",$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
            if ($request->import_as == "print") {
                return view('proaccount::voucher_approvals.paginates.approved_print', $data);
            }
            if ($request->import_as == "csv") {
                $this->voucherRepository->csvDownApprovedVoucher();
                $filePath = public_path("uploads/csv/approved-vouchers.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-approved-vouchers.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        }
        $data['items'] = $this->voucherRepository->withPaginateApprovedVoucher($row_count,$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
        if ($request->ajax()) {
            return view('proaccount::voucher_approvals.paginates.approved_list', $data);
        }
        return view('proaccount::voucher_approvals.approved_voucher', $data);
    }

    public function get_accounts_for_select_option(Request $request)
    {
        try{
            $data = $this->voucherRepository->getAccountByAjax($request->type,$request->search);
            return response()->json($data);
        }catch(Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error($e->getMessage(), 'Error!!');
            return response()->json([
                'error' => $e->getMessage()
            ],503);
        }
    }

    public function money_reciept($id)
    {
        try {
            $data['voucher'] = $this->voucherRepository->find($id);
            return view('proaccount::voucher_payments.reciept', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return back();
        }
    }
}
