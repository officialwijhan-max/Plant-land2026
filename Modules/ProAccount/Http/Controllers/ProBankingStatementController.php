<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ProAccount\Repositories\BankingStatementRepository;
use Modules\ProAccount\Repositories\JournalRepository;
use Modules\ProAccount\Entities\Leadger;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class ProBankingStatementController extends Controller
{
    protected $bankingStatementRepository;

    public function __construct(BankingStatementRepository  $bankingStatementRepository)
    {
        $this->bankingStatementRepository = $bankingStatementRepository;
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

        $data['items'] = $this->bankingStatementRepository->withPaginate($row_count,$quick_search,$sort,$column);
        if ($request->ajax()) {
            return view('proaccount::banking_statement.paginates.list', $data);
        }
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $this->bankingStatementRepository->withPaginate("all",$quick_search,$sort,$column);
            if ($request->import_as == "print") {
                \LogActivity::successLog(trans('common.Print has been Done').' - BankingStatement List', route('journal.index'), "Print");
                return view('proaccount::banking_statement.paginates.print', $data);
            }
            if ($request->import_as == "csv") {
                $this->bankingStatementRepository->csvDownload($data);
                \LogActivity::successLog(trans('common.CSV Download has been done').' - BankingStatement List', route('journal.index'), "CSV Download");
                $filePath = public_path("uploads/csv/banking_statement.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-banking_statement.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        }

        return view('proaccount::banking_statement.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('proaccount::banking_statement.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validate_rules = [
            'file' => 'required|mimes:csv,xls,xlsx|max:2048',
            'credit_account_id' => 'required',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        $leadger = Leadger::find($request->credit_account_id);
        if ($leadger->acc_type == "cash" || $leadger->acc_type == "bank") {
            try {
                ini_set('max_execution_time', 0);
                DB::beginTransaction();
                $this->bankingStatementRepository->create($request->except("_token"));
                DB::commit();
                \LogActivity::successLog('Bank Transaction Statement Uploaded',route('banking_statement.index'), 'CSV Upload');
                Toastr::success(trans('account.Uploaded successfully'));
                return back();
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                DB::rollBack();
                return $e->failures();
            }
        } else {
            Toastr::warning("Choose Bank Account Please !");
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
            $data['statement'] = $this->bankingStatementRepository->find($id);

            return view('proaccount::banking_statement.show', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return back();
        }
    }

    public function reconciled($id)
    {
        try {
            $data['statement'] = $this->bankingStatementRepository->find($id);

            return view('proaccount::banking_statement.reconciled', $data);
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
        return view('proaccount::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $response = $this->bankingStatementRepository->findToReconcile($request->except('_token'));
            DB::commit();
            return response()->json(["message" => trans("common.Successfully Added")], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function transaction_entry_modal(Request $request)
    {
        try {
            $data['response'] = $this->bankingStatementRepository->findStatementDetail($request->id);
            if ($data['response']->banking_statement->leadger->type == 1 || $data['response']->banking_statement->leadger->type == 3) {
                if ($data['response']->sign == 1) {
                    return view('proaccount::banking_statement.modal.income', $data);
                }else {
                    return view('proaccount::banking_statement.modal.expense', $data);
                }
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function transaction_entry_income(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'credit_account_id'=>'required|integer|min:1',
                "debit_amount.*"  => "required|regex:/^\d+(\.\d{1,2})?$/|min:0",
            ]);

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
            $debit_narration[] = "Reconcie Entry By ". auth()->user()->name;

            $approval = 1;

            $journalRecieveRepository = new JournalRepository();
            $item = $journalRecieveRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount'=> ($request->amount) ? $request->amount : $total_amount,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> ($request->purpose) ? $request->purpose : "Reconcile Amount Entry By ". auth()->user()->name,
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
                'sale_or_purchase' => null,
                'ref_no' => null,
                'is_sale_purchase' => 0,
                'direct_manupulate' => 0,
            ]);
            DB::commit();
            \LogActivity::successLog(trans("common.Successfully Added").' - '.$item->GetTypeName().'-'.$item->txn_id,route('journal.audit_history',$item->id),'Reconcile Amount entry');
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return back();
        }
    }

    public function transaction_entry_expense(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'credit_account_id'=>'required|integer|min:1',
                "debit_amount.*"  => "required|regex:/^\d+(\.\d{1,2})?$/|min:0",
            ]);

            $total_amount = 0;

            foreach ($request->account_id as $key => $account) {
                $debit_amounts[] = $request->debit_amount[$key];
                $debit_account_id[] = $request->account_id[$key];
                $debit_partner_id[] = $request->sub_account_id[$key];
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] = $request->narration[$key];
                $total_amount += $request->debit_amount[$key];
            }

            $credit_amounts[] = $total_amount;
            $credit_account_id[] = $request->credit_account_id;
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
            $credit_narration[] = "Reconsile Entry By ". auth()->user()->name;

            $approval = 1;

            $journalRecieveRepository = new JournalRepository();
            $item = $journalRecieveRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount'=> ($request->amount) ? $request->amount : $total_amount,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> ($request->purpose) ? $request->purpose : "Reconcile Amount Entry By ". auth()->user()->name,
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
                'sale_or_purchase' => null,
                'ref_no' => null,
                'is_sale_purchase' => 0,
                'direct_manupulate' => 0,
            ]);
            DB::commit();
            \LogActivity::successLog(trans("common.Successfully Added").' - '.$item->GetTypeName().'-'.$item->txn_id,route('journal.audit_history',$item->id),'Reconcile Amount entry');
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return back();
        }
    }

    public function undo_reconcile(Request $request)
    {
        try {
            DB::beginTransaction();
            $response = $this->bankingStatementRepository->undoReconcile($request->except('_token'));
            DB::commit();
            return response()->json(["message" => trans("common.Successfully Added")], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function approve_reconcile(Request $request)
    {
        try {
            DB::beginTransaction();
            $response = $this->bankingStatementRepository->approveReconcile($request->except('_token'));
            DB::commit();
            return response()->json(["message" => trans("common.Successfully Added")], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function reconcile_done(Request $request)
    {
        try {
            DB::beginTransaction();
            $response = $this->bankingStatementRepository->doneReconcile($request->except('_token'));
            DB::commit();
            return response()->json(["message" => trans("common.Successfully Added")], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => trans('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->bankingStatementRepository->destroy($id);
            Toastr::success(trans('common.Successfully Deleted'));
            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }
}
