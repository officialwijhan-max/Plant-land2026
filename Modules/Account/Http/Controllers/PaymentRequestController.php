<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Entities\ChartAccount;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Entities\PaymentRequest;
use Modules\Account\Entities\OpeningBalanceHistory;
use Modules\Account\Repositories\VoucherRepositoryInterface;
use Modules\Inventory\Http\Requests\ExpenseFormRequest;
use Modules\Inventory\Repositories\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\User;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Account\Entities\Transaction;
use Modules\Account\Entities\Voucher;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Account\Exports\PaymentRequestExport;
class PaymentRequestController extends Controller
{
    protected $expenseRepository,$voucherRepository;

    public function __construct(ExpenseRepositoryInterface $expenseRepository,VoucherRepositoryInterface $voucherRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->expenseRepository = $expenseRepository;
        $this->voucherRepository = $voucherRepository;
    }



    public function index(Request $request)
    {
        try {
            // Fetch parameters from the request
            $row_count = $request->get('row', 10);
            $sort = $request->get('sort', 'asc');
            $column = $request->get('col', 'id'); // Default sorting column
            $quick_search = $request->get('quick_search');

            // Build the query
            $query = PaymentRequest::where('staff_id', Auth::id())->orderBy('created_at', 'desc');
            // Apply quick search
            if ($quick_search) {
                $query->where(function ($q) use ($quick_search) {
                    $q->where('narration', 'LIKE', "%{$quick_search}%")
                    ->orWhere('region', 'LIKE', "%{$quick_search}%")
                    ->orWhere('amount', 'LIKE', "%{$quick_search}%");
                });
            }

            // Apply sorting
            if ($column) {
                $query->orderBy($column, $sort);
            }

            // Pagination
            $data['items'] = $query->paginate($row_count);

            // Handle AJAX requests
            if ($request->ajax()) {
                return view('account::payment_request.list', $data);
            }

            // Handle export requests
            if ($request->has('import_as')) {
                set_time_limit(-1); // Increase time limit for large exports
                $data['items'] = $query->get();

                if ($request->import_as == 'print') {
                    return view('account::payment_request.print', $data);
                }

                if ($request->import_as == 'csv') {
                    // Generate CSV file
                    if (file_exists(public_path("uploads/csv/payment-request-list.xlsx"))) {
                        unlink(public_path("uploads/csv/payment-request-list.xlsx"));
                    }

                    Excel::store(new PaymentRequestExport($data), 'uploads/csv/payment-request-list.xlsx', 'public_folder');

                    $filePath = public_path("uploads/csv/payment-request-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = 'payment-request-list.xlsx';
 
            
                    return response()->download($filePath, $fileName, $headers);
                }
            }

            // Render main view
            return view('account::payment_request.index', $data);

        } catch (\Exception $e) {
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
        $account_managers = User::whereHas('role', function ($query) {
            $query->where('name', 'Accountant Manager');
        })->get();
        $account_categories = $this->voucherRepository->category();
        $bank_accounts = BankAccount::get();
        return view('account::payment_request.create', [
            "account_categories" => $account_categories,
            "bank_accounts" => $bank_accounts,
            "account_managers" => $account_managers,
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            // 'bank_id' => 'required|exists:bank_accounts,id', // Must match an existing bank account ID, nullable if not provided
            'accountant_id' => 'required|exists:users,id',  // Must match an existing user ID
            'narration' => 'nullable|string|max:500',      // Required, max 500 characters
            'region' => 'nullable|string|max:255',         // Required, max 255 characters
            'amount' => 'required|numeric|min:0.01',       // Must be a positive numeric value
        ]);

        // if (!empty($validatedData['bank_id'])) {
        //     $bank_account = BankAccount::find($validatedData['bank_id']);
        //     if ($bank_account->balance_amount < $validatedData['amount']) {
        //         Toastr::error("Bank balance is low, please reduce the amount or recharge the account.");
        //         return back();
        //     }
        // }

        try {
            $PaymentRequest = new PaymentRequest();
            // $PaymentRequest->bank_id = $validatedData['bank_id'];
            $PaymentRequest->accountant_id = $validatedData['accountant_id'];
            $PaymentRequest->staff_id = Auth::id();
            $PaymentRequest->narration = $validatedData['narration'];
            $PaymentRequest->region = $validatedData['region'];
            $PaymentRequest->amount = $validatedData['amount'];
            $PaymentRequest->status = 'pending';
            $PaymentRequest->save();

            DB::commit();
            \LogActivity::successLog('Payment Request has been added Successfully.');
            Toastr::success(__('Payment Request has been added Successfully'));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage() . ' - Error has been detected for Payment Request creation');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }
    public function indexAccountant(Request $request)
    {
        try {
            // Fetch parameters from the request
            $row_count = $request->get('row', 10);
            $sort = $request->get('sort', 'asc');
            $column = $request->get('col', 'id'); // Default sorting column
            $quick_search = $request->get('quick_search');

            // Build the query
            $query = PaymentRequest::where('accountant_id', Auth::id())->orderBy('created_at', 'desc');
            $data['banks'] = BankAccount::get();
            // Apply quick search
            if ($quick_search) {
                $query->where(function ($q) use ($quick_search) {
                    $q->where('narration', 'LIKE', "%{$quick_search}%")
                    ->orWhere('region', 'LIKE', "%{$quick_search}%")
                    ->orWhere('amount', 'LIKE', "%{$quick_search}%");
                });
            }

            // Apply sorting
            if ($column) {
                $query->orderBy($column, $sort);
            }

            // Pagination
            $data['items'] = $query->paginate($row_count);

            // Handle AJAX requests
            if ($request->ajax()) {
                return view('account::payment_request.accountant_list', $data);
            }

            // Handle export requests
            if ($request->has('import_as')) {
                set_time_limit(-1); // Increase time limit for large exports
                $data['items'] = $query->get();

                if ($request->import_as == 'print') {
                    return view('account::payment_request.accountant_print', $data);
                }

                if ($request->import_as == 'csv') {
                    // Generate CSV file
                    if (file_exists(public_path("uploads/csv/payment-request-list.xlsx"))) {
                        unlink(public_path("uploads/csv/payment-request-list.xlsx"));
                    }

                    Excel::store(new PaymentRequestExport($data), 'uploads/csv/payment-request-list.xlsx', 'public_folder');

                    $filePath = public_path("uploads/csv/payment-request-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = 'payment-request-list.xlsx';
 
            
                    return response()->download($filePath, $fileName, $headers);
                }
            }

            // Render main view
            return view('account::payment_request.accountant_index', $data);

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }
    public function allRequests(Request $request)
    {
        try {
            // Fetch parameters from the request
            $row_count = $request->get('row', 10);
            $sort = $request->get('sort', 'asc');
            $column = $request->get('col', 'id'); // Default sorting column
            $quick_search = $request->get('quick_search');

            // Build the query
            $query = PaymentRequest::orderBy('created_at', 'desc');
            $data['banks'] = BankAccount::get();
            // Apply quick search
            if ($quick_search) {
                $query->where(function ($q) use ($quick_search) {
                    $q->where('narration', 'LIKE', "%{$quick_search}%")
                    ->orWhere('region', 'LIKE', "%{$quick_search}%")
                    ->orWhere('amount', 'LIKE', "%{$quick_search}%");
                });
            }

            // Apply sorting
            if ($column) {
                $query->orderBy($column, $sort);
            }

            // Pagination
            $data['items'] = $query->paginate($row_count);

            // Handle AJAX requests
            if ($request->ajax()) {
                return view('account::payment_request.accountant_list', $data);
            }

            // Handle export requests
            if ($request->has('import_as')) {
                set_time_limit(-1); // Increase time limit for large exports
                $data['items'] = $query->get();

                if ($request->import_as == 'print') {
                    return view('account::payment_request.accountant_print', $data);
                }

                if ($request->import_as == 'csv') {
                    // Generate CSV file

                    if (file_exists(public_path("uploads/csv/payment-request-list.xlsx"))) {
                        unlink(public_path("uploads/csv/payment-request-list.xlsx"));
                    }

                    Excel::store(new PaymentRequestExport($data), 'uploads/csv/payment-request-list.xlsx', 'public_folder');

                    $filePath = public_path("uploads/csv/payment-request-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = 'payment-request-list.xlsx';
 
            
                    return response()->download($filePath, $fileName, $headers);
                }
            }

            // Render main view
            return view('account::payment_request.accountant_index', $data);

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }


    public function createAccountant()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }
        $staff = User::whereHas('role', function ($query) {
            $query->where('name', 'Staff');
        })->get();
        $account_categories = $this->voucherRepository->category();
        $bank_accounts = BankAccount::get();
        return view('account::payment_request.accountant_create', [
            "account_categories" => $account_categories,
            "bank_accounts" => $bank_accounts,
            "staff" => $staff,
        ]);
    }

    public function storeAccountant(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'bank_id' => 'required|exists:bank_accounts,id', // Must match an existing bank account ID, nullable if not provided
            'staff_id' => 'required|exists:users,id',  // Must match an existing user ID
            'narration' => 'nullable|string|max:500',      // Required, max 500 characters
            'region' => 'nullable|string|max:255',         // Required, max 255 characters
            'amount' => 'required|numeric|min:0.01',       // Must be a positive numeric value
        ]);

        if (!empty($validatedData['bank_id'])) {
            $bank_account = BankAccount::find($validatedData['bank_id']);
            if ($bank_account->balance_amount < $validatedData['amount']) {
                Toastr::error("Bank balance is low, please reduce the amount or recharge the account.");
                return back();
            }
        }

        try {
            $PaymentRequest = new PaymentRequest();
            $PaymentRequest->bank_id = $validatedData['bank_id'];
            $PaymentRequest->staff_id = $validatedData['staff_id'];
            $PaymentRequest->accountant_id = Auth::id();
            $PaymentRequest->narration = $validatedData['narration'];
            $PaymentRequest->region = $validatedData['region'];
            $PaymentRequest->amount = $validatedData['amount'];
            $PaymentRequest->status = 'accepted';
            $PaymentRequest->save();


            $OpeningBalanceHistory = OpeningBalanceHistory::where('account_id', $bank_account->chart_account_id)->first();
            $OpeningBalanceHistory->amount -= $validatedData['amount'];
            $OpeningBalanceHistory->save();


            $voucher = new Voucher();
            $voucher->voucher_type = 'BV';
            $voucher->amount = $validatedData['amount'];
            $voucher->is_approve = 1;
            $voucher->is_transfer = 1;
            $voucher->narration = $validatedData['narration'];
            $voucher->date = Carbon::now();
            $voucher->created_by = Auth::id();
            $voucher->updated_by = Auth::id();
            $voucher->save();

            $transaction = new Transaction();
            $transaction->account_id = $bank_account->chart_account_id;
            $transaction->type = 'Cr';
            $transaction->amount = $validatedData['amount'];
            $transaction->narration = $validatedData['narration'];
            $transaction->voucherable_type = 'Modules\Account\Entities\Voucher';
            $transaction->voucherable_id = $voucher->id;
            $transaction->save();

            DB::commit();
            \LogActivity::successLog('Payment has been added Successfully.');
            Toastr::success(__('Payment Request has been added Successfully'));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage() . ' - Error has been detected for Payment Request creation');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }
    public function approve(Request $request, $id)
    { 
        $paymentRequest = PaymentRequest::findOrFail($id);

        if (!empty($request->bank_id)) {
            $bank_account = BankAccount::find($request->bank_id);
            if ($bank_account->balance_amount < $paymentRequest->amount) {
                Toastr::error("Bank balance is low, please reduce the amount or recharge the account.");
                return back();
            }
        }
        
        $paymentRequest->bank_id = $request->bank_id;
        $paymentRequest->status = 'accepted';
        $paymentRequest->save();

        $bank_account = BankAccount::find($paymentRequest->bank_id);
        $OpeningBalanceHistory = OpeningBalanceHistory::where('account_id', $bank_account->chart_account_id)->first();
        $OpeningBalanceHistory->amount -= $paymentRequest->amount;
        $OpeningBalanceHistory->save();


        $voucher = new Voucher();
        $voucher->voucher_type = 'BV';
        $voucher->amount = $paymentRequest->amount;
        $voucher->is_approve = 1;
        $voucher->is_transfer = 1;
        $voucher->narration = $paymentRequest->narration;
        $voucher->date = Carbon::now();
        $voucher->created_by = Auth::id();
        $voucher->updated_by = Auth::id();
        $voucher->save();

        $transaction = new Transaction();
        $transaction->account_id = $bank_account->chart_account_id;
        $transaction->type = 'Cr';
        $transaction->amount = $paymentRequest->amount;
        $transaction->narration = $paymentRequest->narration;
        $transaction->voucherable_type = 'Modules\Account\Entities\Voucher';
        $transaction->voucherable_id = $voucher->id;
        $transaction->save();

        Toastr::success(__('Payment Request Approved'));
        return redirect()->back();
    }

    public function reject(Request $request, $id)
    {
        $paymentRequest = PaymentRequest::findOrFail($id);
        $paymentRequest->status = 'rejected';
        $paymentRequest->rejection_reason = $request->input('rejection_reason');
        $paymentRequest->save();

        Toastr::error(__('Payment Request Rejected'));
        return redirect()->back();
    }


    
}
