<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Entities\ChartAccount;
use Modules\Account\Entities\OpeningBalanceHistory;
use Modules\Account\Entities\Voucher;
use Modules\Account\Entities\Transaction;
use Modules\Account\Repositories\LedgerReportRepositoryInterface;
use Modules\Account\Repositories\VoucherRepository;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Account\Repositories\ChartAccountRepositoryInterface;
use Modules\Account\Repositories\TransactionRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Account\Exports\TransactionReportExport;

class TransactionController extends Controller
{
    protected $transactionRepository, $charAccountRepository, $leadgerRepository;

    public function __construct(ChartAccountRepositoryInterface $charAccountRepository, LedgerReportRepositoryInterface $leadgerRepository, TransactionRepositoryInterface $transactionRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->charAccountRepository = $charAccountRepository;
        $this->leadgerRepository = $leadgerRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function index(Request $request)
    {
        try{
            if ($request->dateTo != null && $request->dateFrom == null) {
                Toastr::warning(__('report.You need to set date-from when you select date-to.'));
                return back();
            }
            if ($request->dateTo == null && $request->dateFrom != null) {
                Toastr::warning(__('report.You need to set date-to when you select date-from.'));
                return back();
            }
            $beforedateAccount = null;
            $accont_type = null;
            $dateFrom = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : null;
            $dateTo = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : null;
            $account_id = ($request->account_id != null) ? $request->account_id : null;
            $filter_account = ($request->account_id != null) ? ChartAccount::findOrFail($request['account_id']) : null;
            if ( $request->account_id != null) {
                $beforedateAccount = $filter_account;
                $balance = $this->leadgerRepository->balanceBeforeDate($dateFrom, $beforedateAccount);
                $accont_type = $beforedateAccount->type;
                $opening_balance = OpeningBalanceHistory::where('account_id', $request['account_id'])->where('is_default', 0)->sum('amount');
            }
            else {
                $balance = 0;
                $opening_balance = 0;
            }
            if (!$account_id) {
                $transactions = null;
            } else{
                $transactions = $this->leadgerRepository->search($dateFrom, $dateTo, $account_id);
            }
            if ($request->has('import_as') && $request->import_as == "print") {
                return view('account::leadger_report.print', compact('filter_account', 'transactions', 'accont_type', 'dateFrom', 'dateTo', 'account_id', 'balance', 'beforedateAccount', 'opening_balance'));
            }
            if ($request->has('import_as') && $request->import_as == "csv") {
                if (file_exists(public_path("uploads/csv/transaction-report.xlsx"))) {
                    unlink(public_path("uploads/csv/transaction-report.xlsx"));
                }
                Excel::store(new TransactionReportExport($filter_account, $transactions, $accont_type, $dateFrom, $dateTo, $account_id, $balance, $beforedateAccount, $opening_balance), 'uploads/csv/transaction-report.xlsx', 'public_folder');
                $filePath = public_path("uploads/csv/transaction-report.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time() . '-transaction-report.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
            return view('account::leadger_report.index', compact('filter_account', 'transactions', 'accont_type', 'dateFrom', 'dateTo', 'account_id', 'balance', 'beforedateAccount', 'opening_balance'));

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }
    public function indexCost(Request $request)
    {
        try{
            if ($request->dateTo != null && $request->dateFrom == null) {
                Toastr::warning(__('report.You need to set date-from when you select date-to.'));
                return back();
            }
            if ($request->dateTo == null && $request->dateFrom != null) {
                Toastr::warning(__('report.You need to set date-to when you select date-from.'));
                return back();
            }
            $beforedateAccount = null;
            $accont_type = null;
            $dateFrom = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : null;
            $dateTo = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : null;
            $account_id = ($request->account_id != null) ? $request->account_id : null;
            $filter_account = ($request->account_id != null) ? ChartAccount::findOrFail($request['account_id']) : null;
            if ( $request->account_id != null) {
                $beforedateAccount = $filter_account;
                $balance = $this->leadgerRepository->balanceBeforeDate($dateFrom, $beforedateAccount);
                $accont_type = $beforedateAccount->type;
                $opening_balance = OpeningBalanceHistory::where('account_id', $request['account_id'])->where('is_default', 0)->sum('amount');
            }
            else {
                $balance = 0;
                $opening_balance = 0;
            }
            if (!$account_id) {
                $transactions = null;
            } else{
                $transactions = $this->leadgerRepository->search($dateFrom, $dateTo, $account_id);
            }
            if ($request->has('import_as') && $request->import_as == "print") {
                return view('account::leadger_report.print_cost', compact('filter_account', 'transactions', 'accont_type', 'dateFrom', 'dateTo', 'account_id', 'balance', 'beforedateAccount', 'opening_balance'));
            }
            if ($request->has('import_as') && $request->import_as == "csv") {
                if (file_exists(public_path("uploads/csv/transaction-report.xlsx"))) {
                    unlink(public_path("uploads/csv/transaction-report.xlsx"));
                }
                Excel::store(new TransactionReportExport($filter_account, $transactions, $accont_type, $dateFrom, $dateTo, $account_id, $balance, $beforedateAccount, $opening_balance), 'uploads/csv/transaction-report.xlsx', 'public_folder');
                $filePath = public_path("uploads/csv/transaction-report.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time() . '-transaction-report.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
            return view('account::leadger_report.index_cost', compact('filter_account', 'transactions', 'accont_type', 'dateFrom', 'dateTo', 'account_id', 'balance', 'beforedateAccount', 'opening_balance'));

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function delete($id)
    {
        try {
            $this->transactionRepository->delete($id);
            \LogActivity::successLog('Transaction deleted.');
            Toastr::success(__('account.Transaction deleted'));
            return redirect()->back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been Transaction deleted');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function search(Request $request)
    {

        $rangeArr = $request->date_range ? explode('-', $request->date_range) : "".date('m/d/Y')." - ".date('m/d/Y')."";

            if($request->date_range){
                $dateFrom = new \DateTime(trim($rangeArr[0]), new \DateTimeZone('Asia/Dhaka'));
                $dateTo =  new \DateTime(trim($rangeArr[1]), new \DateTimeZone('Asia/Dhaka'));
            }

        $dateFrom = ($request->date_range != null) ? Carbon::parse($dateFrom)->format('Y-m-d') : null;
        $dateTo = ($request->date_range != null) ? Carbon::parse($dateTo)->format('Y-m-d') : null;
        $voucher_type = ($request->voucher_type != null) ? $request->voucher_type : null;
        $payment_type = ($request->payment_type != null) ? $request->payment_type : null;
        $is_approve = ($request->is_approve != null) ? $request->is_approve : null;
        $account_type = ($request->account_type != null) ? $request->account_type : null;
        $data['transactionLists'] = $this->transactionRepository->search($dateFrom, $dateTo, $voucher_type, $payment_type, $is_approve, $account_type);
        $data['dateFrom'] = $request->dateFrom;
        $data['dateTo'] = $request->dateTo;
        $data['voucher_type'] = $request->voucher_type;
        $data['payment_type'] = $request->payment_type;
        $data['is_approve'] = $request->is_approve;
        return view('account::transactions.index',$data);
    }
    public function searchCost(Request $request)
    {

        $rangeArr = $request->date_range ? explode('-', $request->date_range) : "".date('m/d/Y')." - ".date('m/d/Y')."";

            if($request->date_range){
                $dateFrom = new \DateTime(trim($rangeArr[0]), new \DateTimeZone('Asia/Dhaka'));
                $dateTo =  new \DateTime(trim($rangeArr[1]), new \DateTimeZone('Asia/Dhaka'));
            }

        $dateFrom = ($request->date_range != null) ? Carbon::parse($dateFrom)->format('Y-m-d') : null;
        $dateTo = ($request->date_range != null) ? Carbon::parse($dateTo)->format('Y-m-d') : null;
        $voucher_type = ($request->voucher_type != null) ? $request->voucher_type : null;
        $payment_type = ($request->payment_type != null) ? $request->payment_type : null;
        $is_approve = ($request->is_approve != null) ? $request->is_approve : null;
        $account_type = ($request->account_type != null) ? $request->account_type : null;
        $data['transactionLists'] = $this->transactionRepository->search($dateFrom, $dateTo, $voucher_type, $payment_type, $is_approve, $account_type);
        $data['dateFrom'] = $request->dateFrom;
        $data['dateTo'] = $request->dateTo;
        $data['voucher_type'] = $request->voucher_type;
        $data['payment_type'] = $request->payment_type;
        $data['is_approve'] = $request->is_approve;
        return view('account::transactions.index_cost',$data);
    }
}
