<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::prefix('pro-account')->middleware(['auth','permission'])->group(function() {
    Route::get('/configuration', 'ProAccountController@index')->name('account.configuration');
    Route::get('/configuration-entry-system', 'ProAccountController@entry_system_config')->name('account.configuration_entry_system');
    Route::get('/report-configuration', 'ProAccountController@configuration_report_index')->name('account.report.configuration');
    Route::post('/configuration-update-approval', 'ProAccountController@approval_config_update')->name('account.configuration_update_for_approval');
    //Account Routes
    Route::get('/recievable-accounts', 'ProAccountController@recievable_account')->name('account.recievable_account');
    Route::get('/payable-accounts', 'ProAccountController@payable_account')->name('account.payable_account');

    //Leadger Routes
    Route::get('/leadger', 'ProLeadgerController@index')->name('leadger.index');
    Route::post('/leadger-store', 'ProLeadgerController@store')->name('leadger.store');
    Route::post("/leadger/update-info", "ProLeadgerController@rename_account")->name("leadger.rename_account");
    Route::post('/leadger/delete', 'ProLeadgerController@destroy')->name('leadger.delete');
    Route::get('/leadger-import', 'ProLeadgerController@import_page')->name('leadger.import_page');
    Route::get('/leadger-export', 'ProLeadgerController@export_csv')->name('leadger.export_csv');

    Route::get('/sub-leadger', 'ProSubLeadgerController@index')->name('sub_leadger.index');
    Route::post('/sub-leadger-store', 'ProSubLeadgerController@store')->name('sub_leadger.store');
    
    //Journal Routes
    Route::get('/journal', 'ProJournalController@index')->name("journal.index");
    Route::get('/journal-create', 'ProJournalController@create')->name("journal.create");
    Route::post('/journal-store', 'ProJournalController@store')->name("journal.store");
    Route::get('/journal/{id}/edit', 'ProJournalController@edit')->name("journal.edit");
    Route::post('/journal/{id}/update', 'ProJournalController@update')->name("journal.update");
    Route::get('/journal/{id}/audit-history', 'ProJournalController@audit_history')->name("journal.audit_history");
    Route::get('/journal/{id}/transaction-details', 'ProJournalController@transaction_detail')->name("journal.transaction_detail");

    Route::get('/journal/{id}/audit-history-print', 'ProJournalController@audit_history_print')->name("journal.audit_history_print");
    Route::get('/journal/{id}/transaction-details-print', 'ProJournalController@transaction_detail_print')->name("journal.transaction_detail_print");

    //Voucher Routes (Payment & Receipt)
    Route::get('/payment', 'ProVoucherController@index')->name("vouchers.index");
    Route::get('/payment-create', 'ProVoucherController@create')->name("vouchers.create");
    Route::post('/store', 'ProVoucherController@store')->name("vouchers.store");
    Route::get('/payments/{id}/edit', 'ProVoucherController@edit')->name("vouchers.edit");
    Route::get('/get-voucher-details/{id}', 'ProVoucherController@show')->name("pro-get_voucher_details");

    Route::get('/recieve', 'ProVoucherRecieveController@index')->name("vouchers.recieve_index");
    Route::get('/recieve-create', 'ProVoucherRecieveController@create')->name("vouchers.recieve_create");

    //Voucher Approval
    Route::get('/approval-list', 'ProVoucherController@approval_index')->name("voucher_approval.index"); //pending list
    Route::get('/approved', 'ProVoucherController@approved_voucher')->name("approved_voucher.index"); //approved list
    Route::post('/status-approval', 'ProVoucherController@approval_status')->name("set_voucher_approval");
    Route::post('/all-status-approval', 'ProVoucherController@allApproval')->name("voucher.all.approval");
    Route::post('/voucher/destroy-approved', 'ProVoucherController@destroy_approved')->name("vouchers.destroy_approved");

    //Transaction Routes
    Route::get('/transactions', 'ProTransactionController@index')->name('transaction.transactions');

    //Cash Flow Account
    Route::get('/cash-flow-accounts', 'ProCashFLowAccountController@index')->name('cash_flow_account.index');
    Route::post('/cash-flow-accounts-store', 'ProCashFLowAccountController@store')->name('cash_flow_account.store');
    Route::get('/cash-flow-accounts/edit/{id}', 'ProCashFLowAccountController@edit')->name('cash_flow_account.edit');
    Route::post("/cash-flow-accounts/update/{id}", "ProCashFLowAccountController@update")->name("cash_flow_account.update");
    Route::post('/cash-flow-accounts/delete', 'ProCashFLowAccountController@destroy')->name('cash_flow_account.delete');
    Route::get('/cash-flow-accounts-import', 'ProCashFLowAccountController@import_page')->name('cash_flow_account.import_page');
    Route::get('/cash-flow-reports', 'ProCashFlowReportController@cash_flow_report_view')->name('cash_flow_report.cash_flow_view');

    Route::get('/cash-flow','ProCashFlowController@index')->name('vouchers.cash_flow');
    Route::get('/cash-flow-export-excel/{cash_in}/{cash_out}/{start_date}/{end_date}','ProCashFlowController@export_excel')->name('vouchers.cash_flow_export_excel');

    Route::get('/trial-balance','ProTrialBalanceController@index')->name('vouchers.trial_balance');
    Route::get('/trial-balance-import','ProTrialBalanceController@import_view')->name('vouchers.trial_balance.import_view');

    //Report releated routes
    Route::get('/leadger-reports', 'ProLeadgerReportController@leadger_report_view')->name('leadger_report.leadger_report_view');
    Route::get('/partner-accounts-reports', 'ProLeadgerReportController@sub_leadger_report_view')->name('leadger_report.sub_leadger_report_view');
    Route::get('/partner-summary-reports', 'ProLeadgerReportController@partner_summary_reports')->name('leadger_report.partner_summary_reports');
    Route::get('/partner-summary-reports-excel/{dateFrom},{dateTo},{account_id},{type}', 'ProLeadgerReportController@partner_summary_export_csv')->name('leadger_report.partner_summary_export_csv');

    Route::get('/financial-years', 'ProFinancialYearController@index')->name('financial_years.index');
    Route::get('/income-statement-reports', 'ProIncomeStatementController@index')->name('income_statement_report');
    Route::get('/balance-sheet-statement-reports', 'ProBalanceSheetController@index')->name('balance_sheet_report');
    Route::get('/profit-loss-reports', 'ProIncomeStatementController@profit_loss_single_entry_report')->name('profit_loss_single_entry_report');

    //Expense
    Route::get('/expenses', 'ProExpenseController@index')->name('pro-expenses.index');
    Route::get('/expenses-create', 'ProExpenseController@create')->name('pro-expenses.create');
    Route::post('/expenses-store', 'ProExpenseController@store')->name('pro-expenses.store');
    Route::get('/expenses/edit/{id}', 'ProExpenseController@edit')->name('pro-expenses.edit');
    Route::get('/expenses/import', 'ProExpenseController@csv_upload_view')->name('pro-expenses.csv_upload_view');
    //Income
    Route::get('/income', 'ProIncomeController@index')->name('pro-income.index');
    Route::get('/income-create', 'ProIncomeController@create')->name('pro-income.create');
    Route::post('/income-store', 'ProIncomeController@store')->name('pro-income.store');
    Route::get('/income/edit/{id}', 'ProIncomeController@edit')->name('pro-income.edit');
    Route::get('/income/import', 'ProIncomeController@csv_upload_view')->name('pro-income.csv_upload_view');
    //Opening Balance
    Route::get('/opening-balance', 'ProOpeningBalanceController@index')->name('pro-opening-balance.index');
    Route::get('/opening-balance-create', 'ProOpeningBalanceController@create')->name('pro-opening-balance.create');
    Route::post('/opening-balance-store', 'ProOpeningBalanceController@store')->name('pro-opening-balance.store');
    Route::get('/opening-balance/edit/{id}', 'ProOpeningBalanceController@edit')->name('pro-opening-balance.edit');
    Route::get('/opening-balance/import', 'ProOpeningBalanceController@csv_upload_view')->name('pro-opening-balance.csv_upload_view');

    //Banking Statement
    Route::get('/banking', 'ProBankingStatementController@index')->name('banking_statement.index');
    Route::get('/upload-banking-transaction', 'ProBankingStatementController@create')->name('banking_statement.create');
    Route::post('/upload-banking-transaction-store', 'ProBankingStatementController@store')->name('banking_statement.store');
    Route::post('/reconcile-banking-transaction-update', 'ProBankingStatementController@update')->name('banking_statement.update');
    Route::get('/upload-banking-transaction-show/{id}', 'ProBankingStatementController@show')->name('banking_statement.show');
    Route::get('/upload-banking-transaction-reconciled/{id}', 'ProBankingStatementController@reconciled')->name('banking_statement.reconciled');
    Route::get('/upload-banking-transaction-destroy/{id}', 'ProBankingStatementController@destroy')->name('banking_statement.destroy');
    Route::post('/banking-transaction-entry-modal', 'ProBankingStatementController@transaction_entry_modal')->name('banking_statement.create_transaction_modal');
    Route::post('/banking-transaction-entry-income', 'ProBankingStatementController@transaction_entry_income')->name('banking_statement.transaction_entry_income');
    Route::post('/banking-transaction-entry-expense', 'ProBankingStatementController@transaction_entry_expense')->name('banking_statement.transaction_entry_expense');
    Route::post('/banking-transaction-undo-reconcile', 'ProBankingStatementController@undo_reconcile')->name('banking_statement.undo_reconcile');
    Route::post('/banking-transaction-approve-reconcile', 'ProBankingStatementController@approve_reconcile')->name('banking_statement.approve_reconcile');
    Route::post('/banking-transaction-reconcile-done', 'ProBankingStatementController@reconcile_done')->name('banking_statement.done');
});

Route::prefix('account')->middleware(['auth'])->group(function() {

    Route::post('/select-leadger-list-cash-bank', 'ProLeadgerController@cash_bank_account_select')->name('pro_account.cash_bank_account_select');


    Route::get('/self-account-balance', 'ProLeadgerReportController@self_account_balance')->name('leadger_report.self_account_balance');
    Route::get('/leadger/edit/{id}', 'ProLeadgerController@edit')->name('leadger.edit'); // will be without permission
    Route::post('/leadger-import-store', 'ProLeadgerController@import')->name('leadger.import_store'); // will be without permission
    Route::get('/leadger-list', 'ProLeadgerController@list')->name('leadger.get_data_list_component'); // will be without permission
    Route::get('/leadger-list-all', 'ProLeadgerController@list_all')->name('leadger.get_data_list'); // will be without permission
    Route::get("/leadger-list-cost-center", "ProLeadgerController@cost_center")->name("leadger.cost_center"); // will be without permission

    Route::get('/payment/{id}/money-reciept', 'ProVoucherController@money_reciept')->name("vouchers.payment_money_reciept"); // will be without permission
    Route::get('/recieve/{id}/money-reciept', 'ProVoucherRecieveController@money_reciept')->name("vouchers.recieve_money_reciept"); // will be without permission

    Route::post('/cash-flow-accounts-import-store', 'ProCashFLowAccountController@import')->name('cash_flow_account.import_store'); // will be without permission
    Route::post('/trial-balance-import-store','ProTrialBalanceController@import_store')->name('vouchers.trial_balance.import_store'); // will be without permission

    Route::post('/expenses/import-store', 'ProExpenseController@bulk_expense_store')->name('pro-expenses.bulk_expense_store');
    Route::post("/expenses/update/{id}", "ProExpenseController@update")->name("pro-expenses.update");

    Route::post('/income/import-store', 'ProIncomeController@bulk_income_store')->name('pro-income.bulk_income_store');
    Route::post("/income/update/{id}", "ProIncomeController@update")->name("pro-income.update");
    Route::post("/opening-balance/update/{id}", "ProOpeningBalanceController@update")->name("pro-opening-balance.update");

    Route::get('/financial-years-closing/{id}', 'ProFinancialYearController@closing_by_id')->name('financial_years.closing_by_id');
    Route::post('/financial-years-close-now/{id}', 'ProFinancialYearController@closing')->name('financial_years.close_now');
    Route::get('/income-statement-reports-csv-download', 'ProIncomeStatementController@export_excel')->name('income_statement_report.export_excel');
    Route::get('/balance-sheet-statement-reports-csv-download', 'ProBalanceSheetController@export_excel')->name('balance_sheet_report.export_excel');

    //voucher routes
    Route::post('/recieve-store', 'ProVoucherRecieveController@store')->name("vouchers.recieve_store");
    Route::post('/recieve/{id}/update', 'ProVoucherRecieveController@update')->name("vouchers.recieve_update");
    Route::post('/payment/{id}/update', 'ProVoucherController@update')->name("vouchers.update");
    Route::post('/voucher/destroy', 'ProVoucherController@destroy')->name("vouchers.destroy");
    Route::get('/get-print/{id}', 'ProVoucherController@print')->name("vouchers.print");

    Route::get('/transactions/search', 'ProTransactionController@search')->name("transaction.search");

    Route::get('/sub-leadger-list', 'ProSubLeadgerController@list')->name('sub_leadger.get_data_list');
    Route::get('/sub-leadger-parent-account/{id}', 'ProSubLeadgerController@parent_account_by_id')->name('sub_leadger.parent_account_by_id');
    // Route::get('/sub-leadger-by-leadger/{id}', 'SubLeadgerController@sub_leadger_by_leadger')->name('sub_leadger.sub_leadger_by_leadger'); // unused route
    Route::post('/sub-leadger-by-leadger-select-option', 'ProSubLeadgerController@sub_leadger_by_leadger_select_option')->name('sub_leadger.sub_leadger_by_leadger_select_option'); // unused route

    Route::get('/journal/add-new-row-entry', 'ProJournalController@add_new_line')->name("journal.add_new_line");
    Route::get('/income-expense/add-new-row-entry', 'ProIncomeController@add_new_line')->name("pro-income.add_new_line");
    Route::get('/opening-balance-add-new-row-entry', 'ProOpeningBalanceController@add_new_line')->name("pro-opening-balance.add_new_line");

    Route::post('/cash-flow-accounts-list-for-select', 'ProCashFLowAccountController@list_for_select')->name('cash_flow_account.get_data_list_for_select');
    Route::post('/cash-flow-accounts-out-list-for-select', 'ProCashFLowAccountController@out_list_for_select')->name('cash_flow_account.get_cash_out_list_for_select'); // unused route

    // Route::get('/get-payment-account-by-type/{type}', 'VoucherController@get_accounts')->name("voucher.get_accounts_for_payment"); // unused route
    Route::post('/getPaymentsAccount', 'ProVoucherController@get_accounts_with_type')->name("get_accounts_for_payment");
    Route::post('/get-select-option-payment-account-by-type', 'ProVoucherController@get_accounts_for_select_option')->name("voucher.get_accounts_for_select_option");

    Route::post('/leadger-get-for-select-url', 'ProLeadgerController@get_leadger_for_select')->name('leadger.get_leadger_for_select');
    Route::post('/leadger-get-table-row-data', 'ProLeadgerController@get_table_row_data')->name('leadger.get_table_row_data');
    Route::post('/leadger-list-for-select', 'ProLeadgerController@list_for_select')->name('leadger.get_data_list_for_select');
    Route::post('/sub-leadger-list-for-select', 'ProSubLeadgerController@list_for_select')->name('sub_leadger.get_data_list_for_select');
    Route::post('/sub-leadger-supplier-list-for-select', 'ProSubLeadgerController@supplier_list_for_select')->name('sub_leadger.get_supplier_data_list_for_select'); // unused route

});

Route::get('/cashbooks', 'ProCashbookController@index')->name("cashbook.index")->middleware(['auth','permission']);
