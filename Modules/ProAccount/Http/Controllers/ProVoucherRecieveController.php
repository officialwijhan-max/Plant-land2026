<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ProAccount\Http\Requests\CreateRecieveVoucherRequest;
use Modules\ProAccount\Repositories\JournalRepository;
use Modules\ProAccount\Repositories\VoucherRecieveRepository;
use Modules\ProAccount\Repositories\SubLeadgerRepository;
use Modules\ProAccount\Repositories\LeadgerRepository;
use Modules\Sales\Repositories\SaleRepository;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;

class ProVoucherRecieveController extends Controller
{
    protected $voucherRecieveRepository;

    public function __construct(JournalRepository $voucherRecieveRepository)
    {
        $this->voucherRecieveRepository = $voucherRecieveRepository;
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

        $voucherRecieveRepo = new VoucherRecieveRepository();
        if ($request->has("import_as")) {
            set_time_limit(-1);
            if ($request->import_as == "print") {
                $data['items'] = $voucherRecieveRepo->withPaginateRcvVoucher("all",$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
                \LogActivity::successLog(trans('common.Print has been Done').' - Voucher Payment', route('recieve_index.index'), "Print");
                return view('proaccount::voucher_recieves.paginates.print', $data);
            }
            if ($request->import_as == "csv") {
                $voucherRecieveRepo->csvDownloadRcvVoucher();
                \LogActivity::successLog(trans('common.CSV Download has been done').' - Voucher Payment', route('recieve_index.index'), "CSV Download");
                $filePath = public_path("uploads/csv/voucher_recieves.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-voucher_recieves.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        }

        $data['items'] = $voucherRecieveRepo->withPaginateRcvVoucher($row_count,$quick_search,$sort,$column,['transactions:id,voucher_id,accounting_period_id'],['id','amount', 'date','narration','txn_id','type','is_approve','is_manual_entry']);
        if ($request->ajax()) {
            return view('proaccount::voucher_recieves.paginates.list', $data);
        }
        return view('proaccount::voucher_recieves.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('proaccount::voucher_recieves.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CreateRecieveVoucherRequest $request)
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
                $saleRepo = new SaleRepository();
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

            $credit_amounts[] = ($is_invoiced == 1) ? ($request->sub_amount[0] + $request->discount_amount) : $request->sub_amount[0];
            $credit_account_id[] = $leadger_id;
            $credit_partner_id[] = $request->credit_sub_account_id;
            $credit_cash_flow_account_id[] = 0;
            $credit_narration[] = $request->narration;

            $debit_amounts[] = $request->sub_amount[0];
            $debit_account_id[] = $request->debit_account_id;
            $debit_partner_id[] = 0;
            $debit_cash_flow_account_id[] = ($request->cash_flow_account) ? $request->cash_flow_account[0] : 0;
            $debit_narration[] = $request->narration;

            $approval = 1;

            $total_amount_voucher = ($is_invoiced == 1) ? ($request->sub_amount[0] + $request->discount_amount) : $request->sub_amount[0];

            if ($is_invoiced == 1) {
                $debit_amounts[] = $request->discount_amount;
                $debit_account_id[] = Settings('sale_discount_at_recieve_time');
                $debit_partner_id[] = 0;
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] = "Payment Discount on Sales Invoice";
            }

            $voucher = $this->voucherRecieveRepository->create([
                'type' => $leadger->acc_type == "bank" ? "rec_bank" : "rec_cash",
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
                'is_advanced'=> $is_advanced,
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
            \LogActivity::successLog(trans("common.Successfully Added").' - '.$voucher->GetTypeName().'-'.$voucher->txn_id,route('journal.audit_history',$voucher->id),'Recieve Voucher entry');
            Toastr::success(trans('account.recieve_voucher_has_been_added'));
            if ($request->save_and_close_button == "only_save") {
                return back();
            }
            if ($request->save_and_close_button == "save_and_close_button") {
                return redirect()->route('vouchers.recieve_index');
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
    public function show(Request $request)
    {
        try {
            $voucher = $this->voucherRecieveRepository->voucherDetails($request->except("_token"));
            return view('proaccount::voucher_approvals.voucher_details', [
                "voucher" => $voucher
            ]);
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
        $data['payment'] = $this->voucherRecieveRepository->find($id);
        if ($data['payment']->is_approve == 1) {
            return back();
        }
        return view('proaccount::voucher_recieves.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(CreateRecieveVoucherRequest $request, $id)
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
                $saleRepo = new SaleRepository();
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

            $total_amount_voucher = ($is_invoiced == 1) ? $request->sub_amount[0] + $request->discount_amount : $request->sub_amount[0];

            $subLeadgerRepo = new SubLeadgerRepository();
            $leadger_id = $subLeadgerRepo->find($request->credit_sub_account_id)->leadger_id;

            $LeadgerRepo = new LeadgerRepository();
            $leadger = $LeadgerRepo->find($request->debit_account_id);

            $credit_amounts[] = $total_amount_voucher;
            $credit_account_id[] = $leadger_id;
            $credit_partner_id[] = $request->credit_sub_account_id;
            $credit_cash_flow_account_id[] = 0;
            $credit_narration[] = $request->narration;

            $debit_amounts[] = $request->sub_amount[0];
            $debit_account_id[] = $request->debit_account_id;
            $debit_partner_id[] = 0;
            $debit_cash_flow_account_id[] = ($request->cash_flow_account) ? $request->cash_flow_account[0] : 0;
            $debit_narration[] = $request->narration;

            if ($is_invoiced == 1) {
                $debit_amounts[] = $request->discount_amount;
                $debit_account_id[] = Settings('sale_discount_at_recieve_time');
                $debit_partner_id[] = 0;
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] = "Payment Discount on Sales Invoice";
            }

            $approval = 1;

            $voucher = $this->voucherRecieveRepository->update([
                'type' => $leadger->acc_type == "bank" ? "rec_bank" : "rec_cash",
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
                'is_advanced'=> $is_advanced,

                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => $approval,
                'discount_percentage' => $request->discount_percentage,
                'discount_amount' => $request->discount_amount,
            ], $id);
            DB::commit();
            \LogActivity::successLog(trans("common.Successfully Added").' - '.$voucher->GetTypeName().'-'.$voucher->txn_id,route('journal.audit_history',$voucher->id),'Recieve Voucher Update');
            Toastr::success(trans('account.recieve_voucher_has_been_updated'));
            if ($request->save_and_close_button == "only_save") {
                return back();
            }
            if ($request->save_and_close_button == "save_and_close_button") {
                return redirect()->route('vouchers.recieve_index');
            }
            if ($request->save_and_close_button == "save_and_new_button") {
                return redirect()->route('vouchers.recieve_create');
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
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request)
    {
        try {
            $this->voucherRecieveRepository->delete($request->id);
            \LogActivity::successLog(trans('account.voucher_has_been_destroyed'));
            Toastr::success(trans('account.voucher_has_been_destroyed'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function get_accounts($type)
    {
        return $this->voucherRecieveRepository->getAccountByCashBankOthers($type);
    }

    public function money_reciept($id)
    {
        try {
            $data['voucher'] = $this->voucherRecieveRepository->find($id);
            \LogActivity::successLog(trans("common.Successfully Added").' - '.$data['voucher']->GetTypeName().'-'.$data['voucher']->txn_id,route('journal.audit_history',$data['voucher']->id),'Recieve Voucher Update');
            return view('proaccount::voucher_recieves.reciept', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return back();
        }
    }
}
