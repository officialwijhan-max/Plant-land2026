<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Entities\Income;
use Modules\Account\Entities\ChartAccount;
use Session;
use Modules\Account\Entities\Voucher;
use Modules\Account\Repositories\ChartAccountRepositoryInterface;
use Modules\Report\Repositories\BalanceStatementReportRepository;
use Modules\Account\Repositories\AccountBalanceRepositoryInterface;
use Carbon\Carbon;

class AccountBalanceController extends Controller
{


    protected $balanceStatementRepositories, $openingBalanceHistoryRepository, $charAccountRepository, $accountBalanceRepository;

    public function __construct(BalanceStatementReportRepository $balanceStatementRepositories, ChartAccountRepositoryInterface $charAccountRepository, AccountBalanceRepositoryInterface $accountBalanceRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->charAccountRepository = $charAccountRepository;
        $this->balanceStatementRepositories = $balanceStatementRepositories;
        $this->accountBalanceRepository = $accountBalanceRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $dateFrom = Null;
        $dateTo = Null;
        if ($request->date_range) {

             $rangeArr = $request->date_range ? explode('-', $request->date_range) : "".date('m/d/Y')." - ".date('m/d/Y')."";

            if($request->date_range){
                $dateFrom = new \DateTime(trim($rangeArr[0]), new \DateTimeZone('Asia/Dhaka'));
                $dateTo =  new \DateTime(trim($rangeArr[1]), new \DateTimeZone('Asia/Dhaka'));

                 $dateFrom = ($request->date_range != null) ? Carbon::parse($dateFrom)->format('Y-m-d') : null;
                 $dateTo = ($request->date_range != null) ? Carbon::parse($dateTo)->format('Y-m-d') : null;
            }
        }

       
        $income = $this->accountBalanceRepository->getIncome($dateFrom??null, $dateTo??null);
        $expense = $this->accountBalanceRepository->getExpense($dateFrom??null, $dateTo??null);
        $asset = $this->accountBalanceRepository->getAsset($dateFrom??null, $dateTo??null);
        $liabilities = $this->accountBalanceRepository->getLiabilities($dateFrom??null, $dateTo??null);
        $equity = $this->accountBalanceRepository->getEquity($dateFrom??null, $dateTo??null);


        $data['asset'] = $asset;
        $data['liabilities'] = $liabilities;
        $data['expense'] = $expense;
        $data['income'] = $income;
        $data['equity'] = $equity;
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;

        return view('account::account-balance.index', $data);
    }



    public function income_by_customer(Request $request)
    {
        $data['dateFrom'] = Null;
        $data['dateTo'] = Null;
        if ($request->date_range) {

             $rangeArr = $request->date_range ? explode('-', $request->date_range) : "".date('m/d/Y')." - ".date('m/d/Y')."";

            if($request->date_range){
                $data['dateFrom'] = new \DateTime(trim($rangeArr[0]), new \DateTimeZone('Asia/Dhaka'));
                $data['dateTo'] =  new \DateTime(trim($rangeArr[1]), new \DateTimeZone('Asia/Dhaka'));


                 $data['dateFrom'] = ($request->date_range != null) ? Carbon::parse($data['dateFrom'])->format('Y-m-d') : null;
                 $data['dateTo'] = ($request->date_range != null) ? Carbon::parse($data['dateTo'])->format('Y-m-d') : null;

            }
        }

        $row_count = ($request->has('row')) ? $request->row : 10;
        $sort = ($request->has('sort')) ? $request->sort : 'asc';
        $column = ($request->has('col')) ? $request->col : null;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
        $data['items'] = $this->accountBalanceRepository->getIncomeByCustomer($row_count, $quick_search, $sort, $column, $data['dateFrom'], $data['dateTo']);
        if ($request->ajax()) {
            return view('account::account-balance.paginates.income_by_customer', $data);
        }
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $this->accountBalanceRepository->getIncomeByCustomer("all", $quick_search, $sort, $column, $data['dateFrom'], $data['dateTo']);
            if ($request->import_as == "print") {
                return view('account::account-balance.paginates.income_by_customer_print', $data);
            }
            if ($request->import_as == "csv") {
                $this->accountBalanceRepository->csvDownloadIncomeByCustomer($data);
                $filePath = public_path("uploads/csv/income-by-customer.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time() . '-income-by-customer.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        };

        return view('account::account-balance.income_by_customer', $data);
    }



    public function expense_by_supplier(Request $request)
    {
        $data['dateFrom'] = Null;
        $data['dateTo'] = Null;
        if ($request->date_range) {

             $rangeArr = $request->date_range ? explode('-', $request->date_range) : "".date('m/d/Y')." - ".date('m/d/Y')."";

            if($request->date_range){
                $data['dateFrom'] = new \DateTime(trim($rangeArr[0]), new \DateTimeZone('Asia/Dhaka'));
                $data['dateTo'] =  new \DateTime(trim($rangeArr[1]), new \DateTimeZone('Asia/Dhaka'));


                 $data['dateFrom'] = ($request->date_range != null) ? Carbon::parse($data['dateFrom'])->format('Y-m-d') : null;
                 $data['dateTo'] = ($request->date_range != null) ? Carbon::parse($data['dateTo'])->format('Y-m-d') : null;

            }
        }

        $row_count = ($request->has('row')) ? $request->row : 10;
        $sort = ($request->has('sort')) ? $request->sort : 'asc';
        $column = ($request->has('col')) ? $request->col : null;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
        $data['items'] = $this->accountBalanceRepository->getExpenseBySupplier($row_count, $quick_search, $sort, $column, $data['dateFrom'], $data['dateTo']);
        if ($request->ajax()) {
            return view('account::account-balance.paginates.expense_by_supplier', $data);
        }
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $this->accountBalanceRepository->getExpenseBySupplier("all", $quick_search, $sort, $column, $data['dateFrom'], $data['dateTo']);
            if ($request->import_as == "print") {
                return view('account::account-balance.paginates.expense_by_supplier_print', $data);
            }
            if ($request->import_as == "csv") {
                $this->accountBalanceRepository->csvDownloadExpenseBySupplier($data);
                $filePath = public_path("uploads/csv/expense-by-supplier.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time() . '-expense-by-supplier.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        };

        return view('account::account-balance.expense_by_supplier', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function sale_tax(Request $request)
    {
        if ($request->date_range) {

             $rangeArr = $request->date_range ? explode('-', $request->date_range) : "".date('m/d/Y')." - ".date('m/d/Y')."";

            if($request->date_range){
                $dateFrom = new \DateTime(trim($rangeArr[0]), new \DateTimeZone('Asia/Dhaka'));
                $dateTo =  new \DateTime(trim($rangeArr[1]), new \DateTimeZone('Asia/Dhaka'));


                 $dateFrom = ($request->date_range != null) ? Carbon::parse($dateFrom)->format('Y-m-d') : null;
                 $dateTo = ($request->date_range != null) ? Carbon::parse($dateTo)->format('Y-m-d') : null;

            }
        }

        $tax = $this->accountBalanceRepository->saleTax($dateFrom??null, $dateTo??null);

        return view('account::account-balance.sale_tax', compact('tax'));
    }

    public function income (Request $request){
        $formDate = $toDate = Null;
        if ($request->date_from and $request->date_to){
            $formDate = Carbon::parse($request->date_from)->format('Y-m-d');
            $toDate = Carbon::parse($request->date_to)->format('Y-m-d');
        }

        $income = $this->accountBalanceRepository->income($formDate, $toDate);
        $expense = $this->accountBalanceRepository->expense($formDate, $toDate);

        return view('account::account-balance.income', compact('income', 'expense', 'formDate', 'toDate'));

    }
}
