<?php

namespace Modules\Account\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use LogActivity;
use Modules\Account\Entities\ChartAccount;
use Illuminate\Routing\Controller;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Repositories\IncomeRepositoryInterface;
use Modules\Account\Repositories\VoucherRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Session;
use Brian2694\Toastr\Facades\Toastr;

class IncomeController extends Controller
{

    public $incomeRepository,$voucherRepository;

    public function __construct(IncomeRepositoryInterface $incomeRepository,VoucherRepositoryInterface $voucherRepository)
    {
        $this->incomeRepository = $incomeRepository;
        $this->voucherRepository = $voucherRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $data['items'] = $this->incomeRepository->withPaginate($row_count, $quick_search, $sort, $column, ['showroom','voucher']);
            if ($request->ajax()) {
                return view('account::income.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->incomeRepository->withPaginate('all', $quick_search, $sort, $column, ['showroom','voucher']);
                if ($request->import_as == "print") {
                    return view('account::income.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->incomeRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/income-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-income-list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('account::income.index', $data);

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }
        $account_categories = $this->voucherRepository->category();
        $bank_accounts = BankAccount::get();
        return view('account::income.create', [
            "account_categories" => $account_categories,
            "bank_accounts" => $bank_accounts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
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
        if (!empty($request->bank_id)) {
            $bank_account = BankAccount::find($request->bank_id);
            if ($bank_account->balance_amount < $request->sub_amounts) {
                Toastr::error("Your bank balance is low, please reduce the amount or recharge your account.");
                return back();
            }
        }
        try {
            DB::beginTransaction();
            $this->incomeRepository->create([
                'voucher_type' => $request->voucher_type == 1 ? 'CV' : 'BV' ,//modified
                'amount'=> $sub_amount,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'account_type'=> 'debit',
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

                'is_approve' => 1,
            ]);
            DB::commit();
            LogActivity::successLog('Income has been added Successfully.');
            Toastr::success(__('inventory.Income has been added Successfully'));
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage() . ' - Error has been detected for Income creation');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
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
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }
        $income = $this->incomeRepository->find($id);
        $account_categories = $this->voucherRepository->category();
        $bank_accounts = BankAccount::get();
        return view('account::income.edit', [
            "income" => $income,
            "account_categories" => $account_categories,
            "bank_accounts" => $bank_accounts,
        ]);
    }

    public function show($id)
    {
        $voucher = $this->voucherRepository->find($id);
        return view('account::income.voucher_details', [
            "voucher" => $voucher,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
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
        if (!empty($request->bank_id)) {
            $bank_account = BankAccount::find($request->bank_id);
            if ($bank_account->balance_amount < $request->sub_amounts) {
                Toastr::error("Your bank balance is low, please reduce the amount or recharge your account.");
                return back();
            }
        }

        try {
            DB::beginTransaction();
            $this->incomeRepository->update([
                'voucher_type' => $request->voucher_type == 1 ? 'CV' : 'BV' ,//modified
                'amount'=> $sub_amount,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'account_type'=> 'debit',
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
                'is_approve' => 1,
            ], $id);

            DB::commit();
            LogActivity::successLog('Income has been updated Successfully.');
            Toastr::success(__('inventory.Income has been updated Successfully'));
            return redirect()->route('income.index');
        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage() . ' - Error has been detected for Income Update');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
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
            $voucher = $this->incomeRepository->delete($id);
            DB::commit();
            LogActivity::successLog('Income has been destroyed.');
            Toastr::success(__('inventory.Income has been deleted Successfully'));
            return redirect()->route('income.index');
        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage() . ' - Error has been detected for Income Destroy');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return redirect()->route('income.index');
        }
    }
}
