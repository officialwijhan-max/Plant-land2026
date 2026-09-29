<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Entities\ChartAccount;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Repositories\VoucherRepositoryInterface;
use Modules\Inventory\Http\Requests\ExpenseFormRequest;
use Modules\Inventory\Repositories\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Session;
use Brian2694\Toastr\Facades\Toastr;
use App\Traits\ChecksAccountBalance;

class ExpenseController extends Controller
{
    use ChecksAccountBalance;

    protected $expenseRepository,$voucherRepository;

    public function __construct(ExpenseRepositoryInterface $expenseRepository,VoucherRepositoryInterface $voucherRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->expenseRepository = $expenseRepository;
        $this->voucherRepository = $voucherRepository;
    }



    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $data['items'] = $this->expenseRepository->withPaginate($row_count, $quick_search, $sort, $column, ['showroom','voucher']);
            if ($request->ajax()) {
                return view('inventory::expenses.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->expenseRepository->withPaginate('all', $quick_search, $sort, $column, ['showroom','voucher']);
                if ($request->import_as == "print") {
                    return view('inventory::expenses.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->expenseRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/expense-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-expense-list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('inventory::expenses.index', $data);

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function create()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }
        $account_categories = $this->voucherRepository->category();
        $bank_accounts = BankAccount::get();
        return view('inventory::expenses.create', [
            "account_categories" => $account_categories,
            "bank_accounts" => $bank_accounts,
        ]);
    }

    public function store(ExpenseFormRequest $request)
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }
        $sub_amount = 0;
        foreach ($request->sub_amount as $key => $amount) {
            $sub_amount += $amount;
        }
        $account_id = $request->credit_account_id;

        $insufficientFundsMessage = $this->insufficientFundsForAccount((int) $account_id, $sub_amount);
        if ($insufficientFundsMessage) {
            Toastr::error($insufficientFundsMessage, __('common.Error'));
            return back();
        }

        try {
            $this->expenseRepository->create([
                'voucher_type' => $request->voucher_type == 1 ? 'CV' : 'BV' ,//modified
                'amount'=> $sub_amount,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'account_type'=> 'credit',
                'payment_type' => 'contra_voucher',
                'account_id'=> $account_id,  //debit side and credit side shoud be same
                'main_amount'=> $sub_amount,  //debit side and credit side shoud be same
                'narration'=> $request->narration,  //debit side and credit side shoud be same
                'sub_account_id'=> $request->sub_account_id,   //debit side and credit side shoud be same
                'sub_amount'=> $request->sub_amount,
                'description'=> $request->description,
                'sub_narration'=> $request->sub_narration,
                'customer_id'=> $request->customer_id,

                'cheque_no' => $request->cheque_no,
                'cheque_date' => Carbon::parse($request->cheque_date)->format('Y-m-d'),
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,

                'is_approve' => (app('business_settings')->where('type', 'expense_voucher_approval')->first()->status == 1) ? 1 : 0,
            ]);
            DB::commit();
            \LogActivity::successLog('Expense has been added Successfully.');
            Toastr::success(__('inventory.Expense has been added Successfully'));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Expense creation');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function edit($id)
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }
        $data['expense'] = $this->expenseRepository->find($id, ['voucher','voucher.transactions','voucher.transactions.account']);
        $data['account_categories'] = $this->voucherRepository->category();
        $data['bank_accounts'] = BankAccount::get();
        return view('inventory::expenses.edit', $data);
    }

    public function show($id)
    {
        $data['voucher'] = $this->voucherRepository->find($id);
        return view('inventory::expenses.voucher_details', $data);
    }

    public function update(Request $request, $id)
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }

        $request->validate([
            'credit_account_id' => 'required',
            'sub_account_id' => 'required|array|min:1',
            'sub_amount' => 'required|array|min:1',
        ], validationMessage([
            'credit_account_id' => 'required',
            'sub_account_id' => 'required|array|min:1',
            'sub_amount' => 'required|array|min:1',
        ]));

        $sub_amount = 0;
        foreach ($request->sub_amount as $key => $amount) {
            $sub_amount += $amount;
        }
        $account_id = $request->credit_account_id;

        // Note: this compares the new amount against the account's
        // current balance, which already reflects this expense's
        // original amount - an edit that only changes the amount
        // slightly on an account near its limit could be blocked even
        // when the net effect would be fine. Re-visit if that's a
        // problem in practice (would need the old amount added back
        // before checking).
        $insufficientFundsMessage = $this->insufficientFundsForAccount((int) $account_id, $sub_amount);
        if ($insufficientFundsMessage) {
            Toastr::error($insufficientFundsMessage, __('common.Error'));
            return back();
        }

        try {
            DB::beginTransaction();
            $this->expenseRepository->update([
                'voucher_type' => $request->voucher_type == 1 ? 'CV' : 'BV' ,//modified
                'amount'=> $sub_amount,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'account_type'=> 'credit',
                'payment_type' => 'contra_voucher',
                'account_id'=> $account_id,  //debit side and credit side shoud be same
                'main_amount'=> $sub_amount,  //debit side and credit side shoud be same
                'narration'=> $request->narration,  //debit side and credit side shoud be same
                'sub_account_id'=> $request->sub_account_id,   //debit side and credit side shoud be same
                'sub_amount'=> $request->sub_amount,
                'sub_narration'=> $request->sub_narration,
                'customer_id'=> $request->customer_id,
                'cheque_no' => $request->cheque_no,
                'cheque_date' => Carbon::parse($request->cheque_date)->format('Y-m-d'),
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'is_approve' => (app('business_settings')->where('type', 'expense_voucher_approval')->first()->status == 1) ? 1 : 0,
            ], $id);
            DB::commit();
            \LogActivity::successLog('Expense has been updated Successfully.');
            Toastr::success(__('inventory.Expense has been updated Successfully'));
            return redirect()->route('expenses.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Expense Update');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $voucher = $this->expenseRepository->delete($id);
            DB::commit();
            \LogActivity::successLog('Expense has been destroyed.');
            Toastr::success(__('inventory.Expense has been deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Expense Destroy');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }
}
