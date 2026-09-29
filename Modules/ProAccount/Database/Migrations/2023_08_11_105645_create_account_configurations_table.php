<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('value')->nullable();
            $table->timestamps();
        });



        DB::table('account_configurations')->insert(array (
            0 => 
            array (
              'name' => 'journal_voucher',
              'value' => '0',
            ),
            1 => 
            array (
              'name' => 'bank_journal',
              'value' => '0',
            ),
            2 => 
            array (
              'name' => 'cash_journal',
              'value' => '0',
            ),
            3 => 
            array (
              'name' => 'default_salary_expense_account',
              'value' => '34',
            ),
            4 => 
            array (
              'name' => 'default_sales_account',
              'value' => '36',
            ),
            5 => 
            array (
              'name' => 'default_purchase_account',
              'value' => '7',
            ),
            6 => 
            array (
              'name' => 'default_capital_account',
              'value' => '14',
            ),
            7 => 
            array (
              'name' => 'leadger_account_for_employee',
              'value' => '33',
            ),
            8 => 
            array (
              'name' => 'account_payable',
              'value' => '19',
            ),
            9 => 
            array (
              'name' => 'account_recievable',
              'value' => '8',
            ),
            10 => 
            array (
              'name' => 'direct_income_leadger',
              'value' => '30',
            ),
            11 => 
            array (
              'name' => 'in_direct_income_leadger',
              'value' => '31',
            ),
            12 => 
            array (
              'name' => 'direct_expense_leadger',
              'value' => '27',
            ),
            13 => 
            array (
              'name' => 'in_direct_expense_leadger',
              'value' => '28',
            ),
            14 => 
            array (
              'name' => 'income_summary_debit_leadger',
              'value' => '42',
            ),
            15 => 
            array (
              'name' => 'company_tax_leadger',
              'value' => '48',
            ),
            16 => 
            array (
              'name' => 'retail_earning_leadger',
              'value' => '49',
            ),
            17 => 
            array (
              'name' => 'company_tax',
              'value' => '0',
            ),
            18 => 
            array (
              'name' => 'advance_loan_and_accured_salary_account',
              'value' => '35',
            ),
            19 => 
            array (
              'name' => 'default_sales_return_account',
              'value' => '39',
            ),
            20 => 
            array (
              'name' => 'default_sales_cash_flow_account',
              'value' => '1',
            ),
            21 => 
            array (
              'name' => 'default_purchase_return_account',
              'value' => '7',
            ),
            22 => 
            array (
              'name' => 'default_purchase_cash_flow_account',
              'value' => '4',
            ),
            23 => 
            array (
              'name' => 'default_cost_of_goods_sold_account',
              'value' => '40',
            ),
            24 => 
            array (
              'name' => 'shipping_and_other_charge_expense',
              'value' => '38',
            ),
            25 => 
            array (
              'name' => 'shipping_and_other_charge_income',
              'value' => '37',
            ),
            26 => 
            array (
              'name' => 'default_product_tax_account',
              'value' => '43',
            ),
            27 => 
            array (
              'name' => 'cgst_account',
              'value' => '45',
            ),
            28 => 
            array (
              'name' => 'sgst_account',
              'value' => '44',
            ),
            29 => 
            array (
              'name' => 'cess_account',
              'value' => '47',
            ),
            30 => 
            array (
              'name' => 'company_income_tax',
              'value' => '50',
            ),
            31 => 
            array (
              'name' => 'default_salary_cash_flow_account',
              'value' => '4',
            ),
            32 => 
            array (
              'name' => 'default_loan_cash_flow_account',
              'value' => '3',
            ),
            33 => 
            array (
              'name' => 'retailer_commissions_account',
              'value' => '51',
            ),
            34 => 
            array (
              'name' => 'employee_tax_ledger',
              'value' => '52',
            ),
            35 => 
            array (
              'name' => 'use_cash_flow_in_accounting',
              'value' => '0',
            ),
            36 => 
            array (
              'name' => 'purchase_discount_recieve_at_payment_time',
              'value' => '53',
            ),
            37 => 
            array (
              'name' => 'sale_discount_at_recieve_time',
              'value' => '54',
            ),
            38 => 
            array (
              'name' => 'default_expense_account',
              'value' => '57',
            ),
            39 => 
            array (
              'name' => 'default_warranty_liability_account',
              'value' => '56',
            ),
            40 => 
            array (
              'name' => 'accounting_entry_system',
              'value' => 'double_entry',
            ),
            41 => 
            array (
              'name' => 'default_income_account',
              'value' => '58',
            ),
            42 => 
            array (
              'name' => 'opening_balance_equity',
              'value' => '60',
            ),
            43 => 
            array (
              'name' => 'stock_adjustment_loss',
              'value' => '61',
            ),
            44 => 
            array (
              'name' => 'stock_adjustment_income',
              'value' => '62',
            ),
            45 => 
            array (
              'name' => 'affiliator_account',
              'value' => '63',
            ),
            46 => 
            array (
              'name' => 'default_online_transaction_account',
              'value' => '64',
            ),
            46 => 
            array (
              'name' => 'current_active_accounting',
              'value' => 'pro',
            ),
        ));
          
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_configurations');
    }
}
