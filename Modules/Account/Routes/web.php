<?php

use Illuminate\Support\Facades\Route;

Route::prefix('account')->group(function () {

    Route::get('/income/{id}/destroy1', 'IncomeController@destroy')->name('income.delete');
    Route::resource('/income', 'IncomeController');

    Route::post('/get-account-config-type', 'AccountController@get_accounts_configurable_type')->name("get_accounts_configurable_type");
    Route::post('/select-account-list-all', 'AccountController@account_select')->name('account.select_all_list');
    Route::post('/select-account-list-based-on-type', 'AccountController@account_select_based_on_type')->name('account.select_based_on_type');
    Route::post('/select-account-list-contact-accounts', 'AccountController@account_select_contact_accounts')->name('account.select_contact_accounts');
    Route::post('/get-customers', 'AccountController@getCustomers')->name('account.getcustomers');
    Route::post('/select-account-list-cash-bank', 'AccountController@cash_bank_account_select')->name('account.cash_bank_account_select');
    Route::post('/select-account-list-based-on-configuration-group', 'AccountController@account_select_based_on_configuration_group')->name('account.select_based_on_configuration_group');

    Route::get('/tranactions', 'TransactionController@index')->name("transaction.index")->middleware('permission');
    Route::get('/cost-tranactions', 'TransactionController@indexCost')->name("transaction.index.cost")->middleware('permission');
    Route::get('/tranactions-delete/{id}', 'TransactionController@delete')->name("transaction.delete");

    Route::get('/tranactions/search', 'TransactionController@search')->name("transaction.search");
    Route::get('/cost-tranactions/search', 'TransactionController@searchCost')->name("transaction.search.cost");

    Route::prefix('voucher')->group(function () {
        Route::middleware('permission')->group(function () {
                    Route::get('/payment', 'VoucherController@index')->name("vouchers.index");
                    Route::get('/payment-create', 'VoucherController@create')->name("vouchers.create");
                    Route::post('/store', 'VoucherController@store')->name("vouchers.store");
                    Route::post('/payment/{id}/update', 'VoucherController@update')->name("vouchers.update");
                    Route::get('/detail/{id}/show', 'VoucherController@show')->name("vouchers.show");
                    Route::get('/payments/{id}/edit', 'VoucherController@edit')->name("vouchers.edit");
                    Route::get('/payments/{id}/destroy', 'VoucherController@destroy')->name("vouchers.destroy");

                });


                Route::get('/approval-list', 'VoucherController@approval_index')->name("voucher_approval.index")->middleware('permission');

                Route::post('/status-approval', 'VoucherController@approval_status')->name("set_voucher_approval");
                Route::get('/all-status-approval', 'VoucherController@allApproval')->name("voucher.all.approval");
                Route::post('/get-voucher-details', 'VoucherController@get_voucher_details')->name("get_voucher_details");
                Route::post('/get_voucher_details_form_statement', 'VoucherController@get_voucher_details_form_statetment')->name("get_voucher_details_form_statetment");


                Route::middleware('permission')->group(function () {
                    Route::get('/journal', 'JournalController@index')->name("journal.index");
                    Route::get('/journal-create', 'JournalController@create')->name("journal.create");
                    Route::post('/journal-store', 'JournalController@store')->name("journal.store");
                    Route::get('/journal/{id}/edit', 'JournalController@edit')->name("journal.edit");
                    Route::post('/journal/{id}/update', 'JournalController@update')->name("journal.update");

                    Route::get('/contra-voucher', 'ContraVoucherController@index')->name("contra.index");
                    Route::get('/contra-voucher-create', 'ContraVoucherController@create')->name("contra.create");
                    Route::post('/contra-voucher-store', 'ContraVoucherController@store')->name("contra.store");
                    Route::get('/contra-voucher/{id}/edit', 'ContraVoucherController@edit')->name("contra.edit");
                    Route::post('/contra-voucher/{id}/update', 'ContraVoucherController@update')->name("contra.update");

                });

        Route::middleware('permission')->group(function () {
            Route::get('/openning-balance', 'OpeningBalanceHistoryController@index')->name("openning_balance.index");
            Route::get('/openning-balance-create', 'OpeningBalanceHistoryController@create')->name("openning_balance.create");
            Route::post('/openning-balance-store', 'OpeningBalanceHistoryController@store')->name("openning_balance.store");
            Route::get('/openning-balance/{id}/edit', 'OpeningBalanceHistoryController@edit')->name("openning_balance.edit");
            Route::get('/openning-balance/{id}/destroy', 'OpeningBalanceHistoryController@delete')->name("openning_balance.destroy");
            Route::post('/openning-balance/{id}/update', 'OpeningBalanceHistoryController@update')->name("openning_balance.update");
            Route::post('/openning-balance/close', 'OpeningBalanceHistoryController@closeStatement')->name("openning_balance.closeStatement");
            Route::post('/openning-balance/showroom/store', 'OpeningBalanceHistoryController@showroom_openning_balance_store')->name("showroom_openning_balance.store");

            Route::get('/deposit-create', 'AccountDepositController@create')->name("account_deposit.create");
            Route::post('/deposit-store', 'AccountDepositController@store')->name("account_deposit.store");
        });

    });


    Route::middleware('permission')->group(function () {
        Route::get('/', 'AccountController@index')->name("char_accounts.index");
        Route::get("/chart-account-list", "AccountController@create")->name("char_accounts.create");
        Route::post("/chart-account-store", "AccountController@store")->name("char_accounts.store");
        Route::get("/chart-account/{id}/edit", "AccountController@edit")->name("char_accounts.edit");
        Route::post("/chart-account/update/{id}", "AccountController@update")->name("char_accounts.update");
        Route::get("/chart-account/destroy/{id}", "AccountController@destroy")->name("char_accounts.destroy");


  


        Route::resource('bank_accounts', 'BankAccountController');
        Route::get('bank_accounts/destroy/{id}', 'BankAccountController@destroy')->name('bank.account.delete');
        Route::get('/bank-accounts/csv-upload-page', 'BankAccountController@csv_upload')->name('bank_account.csv_upload.create');
    });


    Route::get('/payment-request', 'PaymentRequestController@index')->name("payment_request.index");
    Route::get('/all-payment-request', 'PaymentRequestController@allRequests')->name("payment_request.index.all");
    Route::get("/payment-request-create", "PaymentRequestController@create")->name("payment_request.create");
    Route::post("/payment-request-store", "PaymentRequestController@store")->name("payment_request.store");

    Route::get('/staff-payment-request', 'PaymentRequestController@indexAccountant')->name("staff.payment_request.index");
    Route::get("/staff-payment-request-create", "PaymentRequestController@createAccountant")->name("staff.payment_request.create");
    Route::post("/staff-payment-request-store", "PaymentRequestController@storeAccountant")->name("staff.payment_request.store");
    
    Route::post('/staff-payment-approve/{paymentRequest}', 'PaymentRequestController@approve')->name("payment.approve");
    Route::post('/staff-payment-reject//{paymentRequest}', 'PaymentRequestController@reject')->name('payment.reject');


    Route::post('/bank-accounts/csv-upload-store', 'BankAccountController@csv_upload_store')->name('bank_account.csv_upload.store');
    Route::post('bank_accounts/update', 'BankAccountController@update')->name('bank.account.update');

    Route::get("/chart-account-list-parent", "AccountController@parent_category")->name("chart_accounts.parent");


    Route::get('bank_accounts/history/{id}', 'BankAccountController@showHistory')->name('bank.account.history');


    Route::get('profit', 'AccountBalanceController@income')->name('profit.index');


    Route::get('statement', 'GeneralLedgerController@index')->name('statement.index');

    Route::get('statement-filter-account/{id}', 'GeneralLedgerController@filterAccountBytype')->name('filterAccountBytype');

    // Open to any authenticated user, matching the existing convention for
    // profit.index/statement.index/account.balance.index above - none of
    // those sit behind the 'permission' middleware either.
    Route::get('/trial-balance', 'FinancialReportController@trialBalance')->name('trial_balance.index');
    Route::get('/balance-sheet', 'FinancialReportController@balanceSheet')->name('balance_sheet.index');
    Route::get('/cost-centers', 'FinancialReportController@costCenters')->name('cost_centers.index');

});

Route::get('/cashbooks', 'CashbookController@index')->name("cashbook.index");

Route::get('/account-balance', 'AccountBalanceController@index')->name("account.balance.index");

Route::get('/income-by-customer', 'AccountBalanceController@income_by_customer')->name("income_by_customer");

Route::get('/expense-by-supplier', 'AccountBalanceController@expense_by_supplier')->name("expense_by_supplier");

Route::get('/sale-tax', 'AccountBalanceController@sale_tax')->name("sale_tax");


Route::prefix('transfer')->group(function () {
    Route::get('/lists', 'TransferController@index')->name("transfer_showroom.index");
    Route::get('/create', 'TransferController@showroom_create')->name("transfer_showroom.create");
    Route::post('/store', 'TransferController@showroom_store')->name("transfer_showroom.store");
    Route::get('{id}/edit', 'TransferController@edit')->name("transfer_showroom.edit");
    Route::post('/{id}/update', 'TransferController@update')->name("transfer_showroom.update");
    Route::get('/treasury/create', 'TransferController@treasuryTransferCreate')->name("treasury_transfer.create");
});


Route::post("/chart-account/update-info", "AccountController@rename_account")->name("char_accounts.rename_account");
