<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pro_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('showroom_id')->nullable();
            $table->string('referable_type')->nullable();
            $table->unsignedBigInteger('referable_id')->nullable();
            $table->string('type')->nullable()->comment('pay_cash / pay_bank / rec_cash / rec_bank / journal / pay_in_due');
            $table->date('date')->default(date("Y-m-d"));
            $table->double('amount', 28,4)->default(0);
            $table->integer('txn_id')->nullable();
            $table->string('sale_or_purchase', 50)->nullable()->comment('s => sales / p => purchase');
            $table->string('ref_no', 75)->nullable()->comment('for sales (unique) and purchase (unique)');
            $table->text('narration')->nullable();
            $table->tinyInteger('is_approve')->default(0)->comment('0 => pending, 1 => Approve, 2 => Cancelled');
            $table->boolean('is_transfer')->default(0)->comment('money transfer from here to there');
            $table->boolean('is_invoiced')->default(0);
            $table->boolean('is_cash_flow_journal')->default(0);
            $table->boolean("is_advanced")->default(0);
            $table->boolean("is_manual_entry")->default(0);
            $table->unsignedBigInteger("approved_by")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->foreign("created_by")->on("users")->references("id")->onDelete("cascade");
            $table->unsignedBigInteger("updated_by")->nullable();
            $table->foreign("updated_by")->on("users")->references("id")->onDelete("cascade");
            $table->index(['type', 'is_approve', 'date', 'is_transfer']);
            $table->timestamps();
            $table->softDeletes();
        });

        $sql = [
            //Account Menu
            ['id'  => 2001, 'searchable' => 0,  'module_id' => 80, 'parent_id' => null, 'name' => 'Pro Account', 'route' => 'account_module', 'type' => 1],
            //Ledger
            ['id'  => 2002, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Chart Account', 'route' => 'leadger.index', 'type' => 2],
            ['id'  => 2003, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2002, 'name' => 'List', 'route' => 'leadger.index', 'type' => 3],
            ['id'  => 2004, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2002, 'name' => 'Create', 'route' => 'leadger.store', 'type' => 3],
            ['id'  => 2005, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2002, 'name' => 'Update', 'route' => 'leadger.rename_account', 'type' => 3],
            ['id'  => 2006, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2002, 'name' => 'Delete', 'route' => 'leadger.delete', 'type' => 3],
            ['id'  => 2007, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2002, 'name' => 'Import', 'route' => 'leadger.import_page', 'type' => 3],
            ['id'  => 2008, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2002, 'name' => 'Export', 'route' => 'leadger.export_csv', 'type' => 3],
            //Sub ledger
            ['id'  => 2010, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Partner Account', 'route' => 'sub_leadger.index', 'type' => 2],
            ['id'  => 2011, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2010, 'name' => 'List', 'route' => 'sub_leadger.index', 'type' => 3],
            ['id'  => 2012, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2010, 'name' => 'Create', 'route' => 'sub_leadger.store', 'type' => 3],
            
            ['id'  => 2015, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Account Receivable', 'route' => 'revievable_sub_menu', 'type' => 2],
            ['id'  => 2016, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2015, 'name' => 'Receivable List', 'route' => 'account.recievable_account', 'type' => 3],
            ['id'  => 2017, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2015, 'name' => 'Receive List', 'route' => 'vouchers.recieve_index', 'type' => 3],
            ['id'  => 2018, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2015, 'name' => 'Create', 'route' => 'vouchers.recieve_create', 'type' => 3],

            ['id'  => 2020, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Account Payable', 'route' => 'payable_sub_menu', 'type' => 2],
            ['id'  => 2021, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2020, 'name' => 'Payable List', 'route' => 'account.payable_account', 'type' => 3],
            ['id'  => 2022, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2020, 'name' => 'Payment List', 'route' => 'vouchers.index', 'type' => 3],
            ['id'  => 2023, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2020, 'name' => 'Create', 'route' => 'vouchers.create', 'type' => 3],
            //Journal
            ['id'  => 2026, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Journal', 'route' => 'journal_sub_menu', 'type' => 2],
            ['id'  => 2027, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2026, 'name' => 'Journal List', 'route' => 'journal.index', 'type' => 3],
            ['id'  => 2028, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2026, 'name' => 'Create', 'route' => 'journal.create', 'type' => 3],
            ['id'  => 2029, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2026, 'name' => 'Edit', 'route' => 'journal.edit', 'type' => 3],
            ['id'  => 2030, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2026, 'name' => 'Audit History', 'route' => 'journal.audit_history', 'type' => 3],
            ['id'  => 2031, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2026, 'name' => 'Audit His. Print', 'route' => 'journal.audit_history_print', 'type' => 3],
            ['id'  => 2032, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2026, 'name' => 'Transaction', 'route' => 'transaction.transactions', 'type' => 3],
            ['id'  => 2033, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2026, 'name' => 'Transaction Detail', 'route' => 'journal.transaction_detail', 'type' => 3],
            ['id'  => 2034, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2026, 'name' => 'Trans.Details Print', 'route' => 'journal.transaction_detail_print', 'type' => 3],
            //Banking (the remaining part of Account module)
            ['id'  => 2038, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Banking', 'route' => 'banking_statement.index', 'type' => 2],
            ['id'  => 2039, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'List', 'route' => 'banking_statement.index', 'type' => 3],
            ['id'  => 2040, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Create', 'route' => 'banking_statement.create', 'type' => 3],
            ['id'  => 2041, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Update', 'route' => 'banking_statement.update', 'type' => 3],
            ['id'  => 2042, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Show', 'route' => 'banking_statement.show', 'type' => 3],
            ['id'  => 2043, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Delete', 'route' => 'banking_statement.destroy', 'type' => 3],
            ['id'  => 2044, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Reconciled', 'route' => 'banking_statement.reconciled', 'type' => 3],
            ['id'  => 2045, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Check Complete', 'route' => 'banking_statement.done', 'type' => 3],
            ['id'  => 2046, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Undo Reconcile', 'route' => 'banking_statement.undo_reconcile', 'type' => 3],
            ['id'  => 2047, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Approve Reconcile', 'route' => 'banking_statement.approve_reconcile', 'type' => 3],
            ['id'  => 2048, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Create Transaction', 'route' => 'banking_statement.create_transaction_modal', 'type' => 3],
            ['id'  => 2049, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Trans. Entry Income', 'route' => 'banking_statement.transaction_entry_income', 'type' => 3],
            ['id'  => 2050, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2038, 'name' => 'Trans. Entry Expense', 'route' => 'banking_statement.transaction_entry_expense', 'type' => 3],
            //Expense
            ['id'  => 2051, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Expense', 'route' => 'expenses.index', 'type' => 2],
            ['id'  => 2052, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2051, 'name' => 'List', 'route' => 'expenses.index', 'type' => 3],
            ['id'  => 2053, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2051, 'name' => 'Create', 'route' => 'expenses.store', 'type' => 3],
            ['id'  => 2054, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2051, 'name' => 'Update', 'route' => 'expenses.edit', 'type' => 3],
            ['id'  => 2055, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2051, 'name' => 'Show', 'route' => 'get_voucher_details', 'type' => 3],
            ['id'  => 2056, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2051, 'name' => 'CSV Upload', 'route' => 'expenses.csv_upload_view', 'type' => 3],
            //Income
            ['id'  => 2057, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Income', 'route' => 'income.index', 'type' => 2],
            ['id'  => 2058, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2057, 'name' => 'List', 'route' => 'income.index', 'type' => 3],
            ['id'  => 2059, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2057, 'name' => 'Create', 'route' => 'income.store', 'type' => 3],
            ['id'  => 2060, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2057, 'name' => 'Update', 'route' => 'income.edit', 'type' => 3],
            ['id'  => 2061, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2057, 'name' => 'Show', 'route' => 'get_voucher_details', 'type' => 3],
            ['id'  => 2062, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2057, 'name' => 'CSV Upload', 'route' => 'income.csv_upload_view', 'type' => 3],
            
            //Voucher
            ['id'  => 2065, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Voucher', 'route' => 'vouchers.index', 'type' => 2],
            ['id'  => 2066, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2065, 'name' => 'Details', 'route' => 'get_voucher_details', 'type' => 3],
            ['id'  => 2067, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2065, 'name' => 'Delete', 'route' => 'vouchers.destroy', 'type' => 3],
            ['id'  => 2068, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2065, 'name' => 'Approved V. Delete', 'route' => 'vouchers.destroy_approved', 'type' => 3],
            ['id'  => 2069, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2065, 'name' => 'Pending V. List', 'route' => 'voucher_approval.index', 'type' => 3],
            ['id'  => 2070, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2065, 'name' => 'Approved V. List', 'route' => 'approved_voucher.index', 'type' => 3],
            ['id'  => 2071, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2065, 'name' => 'Approval Permit', 'route' => 'set_voucher_approval', 'type' => 3],
            ['id'  => 2072, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2065, 'name' => 'Approve All V.', 'route' => 'voucher.all.approval', 'type' => 3],
            ['id'  => 2073, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2065, 'name' => 'Undo From Cancelled', 'route' => 'undo_from_cancelled', 'type' => 3],

            //Reports
            ['id'  => 2075, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Leadger Report', 'route' => 'leadger_report.leadger_report_view', 'type' => 2],
            ['id'  => 2076, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Sub Leadger Report', 'route' => 'leadger_report.sub_leadger_report_view', 'type' => 2],
            ['id'  => 2077, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Partner Summary Report', 'route' => 'leadger_report.partner_summary_reports', 'type' => 2],
            ['id'  => 2078, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Partner Summary Report CSV', 'route' => 'leadger_report.partner_summary_export_csv', 'type' => 2],

            // ['id'  => 1080, 'searchable' => 1,  'module_id' => 10, 'parent_id' => 1001, 'name' => 'Cash Flow Report', 'route' => 'vouchers.cash_flow', 'type' => 2],
            // ['id'  => 1081, 'searchable' => 0,  'module_id' => 10, 'parent_id' => 1001, 'name' => 'Cash Flow Report Excel', 'route' => 'vouchers.cash_flow_export_excel', 'type' => 2],

            ['id'  => 2082, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Trail Balance', 'route' => 'vouchers.trial_balance', 'type' => 2],
            // ['id'  => 1083, 'searchable' => 0,  'module_id' => 10, 'parent_id' => 1001, 'name' => 'Trail Balance Import', 'route' => 'vouchers.trial_balance.import_view', 'type' => 2],
            ['id'  => 2084, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Income Statement', 'route' => 'income_statement_report', 'type' => 2],
            ['id'  => 2085, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Balance Sheet', 'route' => 'balance_sheet_report', 'type' => 2],
            // ['id'  => 895, 'searchable' => 1,  'module_id' => 10, 'parent_id' => 1001, 'name' => 'Profit Loss Report', 'route' => 'profit_loss_single_entry_report', 'type' => 2],

            //Account Configuration
            ['id'  => 2090, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2001, 'name' => 'Account Configuration', 'route' => 'account_configuration_permit', 'type' => 2],
            ['id'  => 2091, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2090, 'name' => 'Activation.', 'route' => 'account.configuration_entry_system', 'type' => 3],
            ['id'  => 2092, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2090, 'name' => 'Configuration', 'route' => 'account.configuration', 'type' => 3],
            ['id'  => 2093, 'searchable' => 0,  'module_id' => 80, 'parent_id' => 2090, 'name' => 'Conf. Update', 'route' => 'account.configuration_update_for_approval', 'type' => 3],
            ['id'  => 2094, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2090, 'name' => 'Report', 'route' => 'account.report.configuration', 'type' => 3],
            ['id'  => 2095, 'searchable' => 1,  'module_id' => 80, 'parent_id' => 2090, 'name' => 'Financial Year', 'route' => 'financial_years.index', 'type' => 3],
        ];

        DB::table('permissions')->insert($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_vouchers');
    }
};
