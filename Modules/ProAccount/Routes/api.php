<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\API\AccountController;
use Modules\Account\Http\Controllers\API\BalanceSheetController;
use Modules\Account\Http\Controllers\API\BankingStatementController;
use Modules\Account\Http\Controllers\API\CashbookController;
use Modules\Account\Http\Controllers\API\ExpenseController;
use Modules\Account\Http\Controllers\API\FinancialYearController;
use Modules\Account\Http\Controllers\API\IncomeController;
use Modules\Account\Http\Controllers\API\IncomeStatementController;
use Modules\Account\Http\Controllers\API\JournalController;
use Modules\Account\Http\Controllers\API\LeadgerController;
use Modules\Account\Http\Controllers\API\LeadgerReportController;
use Modules\Account\Http\Controllers\API\PurchaseController;
use Modules\Account\Http\Controllers\API\SalesController;
use Modules\Account\Http\Controllers\API\SubLeadgerController;
use Modules\Account\Http\Controllers\API\TransactionController;
use Modules\Account\Http\Controllers\API\TrialBalanceController;
use Modules\Account\Http\Controllers\API\VoucherController;
use Modules\Account\Http\Controllers\API\VoucherRecieveController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('account')->group(function () {
        Route::get('/configuration', [AccountController::class, 'index'])->middleware('apiPermission:account.account-configuration');
        Route::get('/report-configuration', [AccountController::class, 'configuration_report_index'])->middleware('apiPermission:account.report.configuration');
        Route::post('/configuration', [AccountController::class, 'approval_config_update'])->middleware('apiPermission:account.configuration_update_for_approval');

        Route::get('/financial-years', [FinancialYearController::class, 'index'])->middleware('apiPermission:financial_years.index');
        Route::get('/financial-years-closing/{id}', [FinancialYearController::class, 'closing_by_id']);
        Route::post('/financial-years-close-now/{id}', [FinancialYearController::class, 'closing']);


        // Leadger Routes
        Route::get('/leadgers', [LeadgerController::class, 'index'])->middleware('apiPermission:leadger.index');
        Route::get('/leadgers/{id}', [LeadgerController::class, 'getAccountById'])->middleware('apiPermission:leadger.index');
        Route::post('/leadgers', [LeadgerController::class, 'store'])->middleware('apiPermission:leadger.store');
        Route::put('/leadgers/{id}', [LeadgerController::class, 'update'])->middleware('apiPermission:leadger.rename_account');
        Route::delete('/leadgers/{id}', [LeadgerController::class, 'destroy'])->middleware('apiPermission:leadger.delete');
        Route::post('/leadgers-import', [LeadgerController::class, 'import'])->middleware('apiPermission:leadger.import_page');
        Route::get('/leadgers-export', [LeadgerController::class, 'export_csv'])->middleware('apiPermission:leadger.export_csv');
        Route::get('/all-bank-accounts', [LeadgerController::class, 'allBankAccounts']); // account list for add purchase and sales
        // Sub Leadger Routes
        Route::get('/sub-leadgers', [SubLeadgerController::class, 'index'])->middleware('apiPermission:sub_leadger.index');

        // Payment Routes
        Route::get('/payable-accounts', [AccountController::class, 'payable_account'])->middleware('apiPermission:account.payable_account');
        Route::get('/payments', [VoucherController::class, 'index'])->middleware('apiPermission:vouchers.index');
        Route::post('/payments', [VoucherController::class, 'store'])->middleware('apiPermission:vouchers.create');
        Route::get('/payments/{id}', [VoucherController::class, 'show'])->middleware('apiPermission:get_voucher_details');
        Route::put('/payments/{id}', [VoucherController::class, 'update'])->middleware('apiPermission:journal.edit');

        // Recieve Routes
        Route::get('/recievable-accounts', [AccountController::class, 'recievable_account'])->middleware('apiPermission:account.recievable_account');
        Route::get('/recieves', [VoucherRecieveController::class, 'index'])->middleware('apiPermission:vouchers.recieve_index');
        Route::post('/recieves', [VoucherRecieveController::class, 'store'])->middleware('apiPermission:vouchers.recieve_create');
        Route::get('/recieves/{id}', [VoucherRecieveController::class, 'show'])->middleware('apiPermission:get_voucher_details');
        Route::put('/recieves/{id}', [VoucherRecieveController::class, 'update'])->middleware('apiPermission:journal.edit');

        // Voucher Routes
        Route::get('/vouchers', [VoucherController::class, 'index'])->middleware('apiPermission:vouchers.index');
        Route::get('/vouchers/{id}', [VoucherController::class, 'show'])->middleware('apiPermission:get_voucher_details');
        Route::delete('/vouchers/{id}', [VoucherController::class, 'destroy'])->middleware('apiPermission:vouchers.destroy'); // payment, recieve, journal & voucher delete

        Route::get('/vouchers-approved-list', [VoucherController::class, 'approved_voucher'])->middleware('apiPermission:approved_voucher.index');
        Route::get('/vouchers-pending-list', [VoucherController::class, 'approval_index'])->middleware('apiPermission:voucher_approval.index');
        Route::post('/vouchers-single-approve', [VoucherController::class, 'approval_status'])->middleware('apiPermission:set_voucher_approval');
        Route::post('/vouchers-multiple-approve', [VoucherController::class, 'allApproval'])->middleware('apiPermission:voucher.all.approval');
        Route::post('/approved-vouchers-delete', [VoucherController::class, 'destroy_approved'])->middleware('apiPermission:vouchers.destroy_approved');

        // Journal Routes
        Route::get('/journals', [JournalController::class, 'index'])->middleware('apiPermission:journal.index');
        Route::post('/journals', [JournalController::class, 'store'])->middleware('apiPermission:journal.create');
        Route::put('/journals/{id}', [JournalController::class, 'update'])->middleware('apiPermission:journal.edit');
        Route::get('/journals/{id}', [JournalController::class, 'show'])->middleware('apiPermission:get_voucher_details'); //audit-history, transaction-details & journal-details same
        Route::get('/transactions', [TransactionController::class, 'index'])->middleware('apiPermission:transaction.transactions');

        // Banking Statement
        Route::get('/banking-transaction', [BankingStatementController::class, 'index'])->middleware('apiPermission:banking_statement.index');
        Route::post('/banking-transaction-upload', [BankingStatementController::class, 'store'])->middleware('apiPermission:banking_statement.store');
        Route::get('/banking-transaction/{id}', [BankingStatementController::class, 'show'])->middleware('apiPermission:banking_statement.show');
        Route::post('/banking-transaction-update', [BankingStatementController::class, 'update'])->middleware('apiPermission:banking_statement.update');
        Route::delete('/banking-transaction-delete/{id}', [BankingStatementController::class, 'destroy'])->middleware('apiPermission:banking_statement.destroy');
        Route::get('/banking-transaction-details/{id}', [BankingStatementController::class, 'transaction_entry_modal'])->middleware('apiPermission:banking_statement.create_transaction_modal');
        Route::post('/banking-transaction-entry-income', [BankingStatementController::class, 'transaction_entry_income'])->middleware('apiPermission:banking_statement.transaction_entry_income');
        Route::post('/banking-transaction-entry-expense', [BankingStatementController::class, 'transaction_entry_expense'])->middleware('apiPermission:banking_statement.transaction_entry_expense');
        Route::get('/banking-transaction-undo-reconcile/{id}', [BankingStatementController::class, 'undo_reconcile'])->middleware('apiPermission:banking_statement.undo_reconcile');
        Route::get('/banking-transaction-approve-reconcile/{id}', [BankingStatementController::class, 'approve_reconcile'])->middleware('apiPermission:banking_statement.approve_reconcile');
        Route::get('/banking-transaction-reconcile-done/{id}', [BankingStatementController::class, 'reconcile_done'])->middleware('apiPermission:banking_statement.done');


        // Account Sales Routes
        Route::get('/sales', [SalesController::class, 'index'])->middleware('apiPermission:accounting_sales.index');
        Route::post('/sales', [SalesController::class, 'store'])->middleware('apiPermission:accounting_sales.create');
        Route::get('/sales/{id}', [SalesController::class, 'show'])->middleware('apiPermission:accounting_sales_show');
        Route::put('/sales/{id}', [SalesController::class, 'update'])->middleware('apiPermission:accounting_sales.edit');

        // Account Purchase Routes
        Route::get('/purchases', [PurchaseController::class, 'index'])->middleware('apiPermission:accounting_purchase.index');
        Route::post('/purchases', [PurchaseController::class, 'store'])->middleware('apiPermission:accounting_purchase.create');
        Route::get('/purchases/{id}', [PurchaseController::class, 'show'])->middleware('apiPermission:accounting_purchase_show');
        Route::put('/purchases/{id}', [PurchaseController::class, 'update'])->middleware('apiPermission:accounting_purchase.edit');

        // Account Expense Routes
        Route::get('/expenses', [ExpenseController::class, 'index'])->middleware('apiPermission:expenses.index');
        Route::post('/expenses', [ExpenseController::class, 'store'])->middleware('apiPermission:expenses.store');
        Route::get('/expenses/{id}', [ExpenseController::class, 'show'])->middleware('apiPermission:get_voucher_details');
        Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('apiPermission:expenses.edit');
        Route::post('/expenses-import', [ExpenseController::class, 'bulk_expense_store'])->middleware('apiPermission:expenses.csv_upload_view');
        // Account Income Routes
        Route::get('/incomes', [IncomeController::class, 'index'])->middleware('apiPermission:income.index');
        Route::post('/incomes', [IncomeController::class, 'store'])->middleware('apiPermission:income.store');
        Route::get('/incomes/{id}', [IncomeController::class, 'show'])->middleware('apiPermission:get_voucher_details');
        Route::put('/incomes/{id}', [IncomeController::class, 'update'])->middleware('apiPermission:income.edit');
        Route::post('/incomes-import', [IncomeController::class, 'bulk_income_store'])->middleware('apiPermission:income.csv_upload_view');

        // Account Report Routes
        Route::get('/leadger-reports', [LeadgerReportController::class, 'leadger_report_view'])->middleware('apiPermission:leadger_report.leadger_report_view');
        Route::get('/partner-accounts-reports', [LeadgerReportController::class, 'sub_leadger_report_view'])->middleware('apiPermission:leadger_report.sub_leadger_report_view');
        // todo:: will solved later partner-summary-reports print and excel
        Route::get('/partner-summary-reports', [LeadgerReportController::class, 'partner_summary_reports'])->middleware('apiPermission:leadger_report.partner_summary_reports');
        Route::get('/income-statement-reports', [IncomeStatementController::class, 'index'])->middleware('apiPermission:income_statement_report');
        Route::get('/balance-sheet-statement-reports', [BalanceSheetController::class, 'index'])->middleware('apiPermission:balance_sheet_report');

        Route::get('/trial-balance', [TrialBalanceController::class, 'index'])->middleware('apiPermission:vouchers.trial_balance');
        Route::post('/trial-balance-import-store', [TrialBalanceController::class, 'import_store'])->middleware('apiPermission:vouchers.trial_balance.import_view');
    });
    Route::get('/cashbooks', [CashbookController::class, 'index'])->middleware('apiPermission:cashbook.index');
});
